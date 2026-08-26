<?php

namespace Tests\Feature;

use App\Livewire\Recovery\TelegramChannel;
use App\Models\TelegramPost;
use App\Models\User;
use App\Services\TelegramChannelSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class TelegramChannelTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(string $name = 'telegram_channel_preview.html'): string
    {
        return file_get_contents(base_path("tests/Fixtures/{$name}"));
    }

    public function test_parse_extracts_posts_from_the_channel_preview_page(): void
    {
        $user = User::factory()->create();
        $posts = (new TelegramChannelSyncService($user, 'G_Y_17'))->parse($this->fixture());

        $this->assertNotEmpty($posts);
        $this->assertTrue(collect($posts)->every(fn ($p) => is_int($p['message_id']) && $p['message_id'] > 0));
        $this->assertTrue(collect($posts)->contains(fn ($p) => filled($p['content'])));
        $this->assertTrue(collect($posts)->contains(fn ($p) => filled($p['image_url']) || filled($p['video_url'])));
    }

    public function test_rejects_a_channel_outside_the_curated_list(): void
    {
        $user = User::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        new TelegramChannelSyncService($user, 'some_random_channel');
    }

    public function test_sync_stores_posts_and_is_idempotent_on_a_second_run(): void
    {
        Http::fake(['t.me/*' => Http::response($this->fixture(), 200)]);

        $user = User::factory()->create();
        $service = new TelegramChannelSyncService($user, 'G_Y_17');

        $firstRun = $service->sync();
        $totalAfterFirst = TelegramPost::ownedBy($user)->count();

        $this->assertGreaterThan(0, $firstRun);
        $this->assertSame($firstRun, $totalAfterFirst);

        $secondRun = $service->sync();

        $this->assertSame(0, $secondRun);
        $this->assertSame($totalAfterFirst, TelegramPost::ownedBy($user)->count());
    }

    public function test_sync_keeps_each_channels_posts_separate(): void
    {
        Http::fake([
            'https://t.me/s/G_Y_17' => Http::response($this->fixture('telegram_channel_preview.html'), 200),
            'https://t.me/s/G_Y_19' => Http::response($this->fixture('telegram_channel_preview_2.html'), 200),
        ]);

        $user = User::factory()->create();

        (new TelegramChannelSyncService($user, 'G_Y_17'))->sync();
        (new TelegramChannelSyncService($user, 'G_Y_19'))->sync();

        $this->assertGreaterThan(0, TelegramPost::ownedBy($user)->where('channel', 'G_Y_17')->count());
        $this->assertGreaterThan(0, TelegramPost::ownedBy($user)->where('channel', 'G_Y_19')->count());
    }

    public function test_sync_raises_when_the_channel_is_unreachable(): void
    {
        Http::fake(['t.me/*' => Http::response('', 500)]);

        $user = User::factory()->create();

        $this->expectException(\RuntimeException::class);
        (new TelegramChannelSyncService($user, 'G_Y_17'))->sync();
    }

    public function test_component_shows_a_failure_message_instead_of_crashing(): void
    {
        Http::fake(['t.me/*' => Http::response('', 500)]);
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TelegramChannel::class)
            ->call('sync')
            ->assertSet('syncFailed', true)
            ->assertSee('تعذّر الوصول');
    }

    public function test_component_lists_synced_posts_for_the_authenticated_user_only(): void
    {
        Http::fake(['t.me/*' => Http::response($this->fixture(), 200)]);

        $user = User::factory()->create();
        $other = User::factory()->create();

        Livewire::actingAs($user)->test(TelegramChannel::class)->call('sync');

        $this->assertGreaterThan(0, TelegramPost::ownedBy($user)->count());
        $this->assertSame(0, TelegramPost::ownedBy($other)->count());
    }

    public function test_switching_tabs_scopes_the_list_to_that_channel(): void
    {
        Http::fake([
            'https://t.me/s/G_Y_17' => Http::response($this->fixture('telegram_channel_preview.html'), 200),
            'https://t.me/s/G_Y_19' => Http::response($this->fixture('telegram_channel_preview_2.html'), 200),
        ]);

        $user = User::factory()->create();
        (new TelegramChannelSyncService($user, 'G_Y_17'))->sync();
        (new TelegramChannelSyncService($user, 'G_Y_19'))->sync();

        $component = Livewire::actingAs($user)->test(TelegramChannel::class);

        $this->assertTrue($component->viewData('posts')->every(fn ($p) => $p->channel === 'G_Y_17'));

        $component->call('switchChannel', 'G_Y_19');

        $this->assertTrue($component->viewData('posts')->every(fn ($p) => $p->channel === 'G_Y_19'));
    }

    public function test_switching_to_an_unknown_channel_is_ignored(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TelegramChannel::class)
            ->call('switchChannel', 'not_a_real_channel')
            ->assertSet('channel', 'G_Y_17');
    }
}
