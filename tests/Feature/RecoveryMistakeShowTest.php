<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ManageMistake;
use App\Livewire\Recovery\MistakeShow;
use App\Models\RecoveryMistake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryMistakeShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_renders_the_mistake(): void
    {
        $user = User::factory()->create();
        $mistake = RecoveryMistake::factory()->for($user)->create(['title' => 'السهر لوحدي بالليل']);

        Livewire::actingAs($user)
            ->test(MistakeShow::class, ['mistake' => $mistake])
            ->assertSee('السهر لوحدي بالليل');
    }

    public function test_user_cannot_view_another_users_mistake(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $mistake = RecoveryMistake::factory()->for($owner)->create();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::actingAs($intruder)->test(MistakeShow::class, ['mistake' => $mistake]);
    }

    public function test_editing_via_the_modal_updates_the_mistake(): void
    {
        $user = User::factory()->create();
        $mistake = RecoveryMistake::factory()->for($user)->create(['title' => 'قديم', 'weight' => 30]);

        Livewire::actingAs($user)
            ->test(ManageMistake::class)
            ->call('openForEdit', $mistake->id)
            ->assertSet('title', 'قديم')
            ->set('title', 'جديد')
            ->set('weight', 80)
            ->set('note', '<p>خطة مواجهة</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('mistake-saved');

        $mistake->refresh();
        $this->assertSame('جديد', $mistake->title);
        $this->assertSame(80, $mistake->weight);
        $this->assertSame('<p>خطة مواجهة</p>', $mistake->note);
    }

    public function test_user_cannot_edit_another_users_mistake_via_the_modal(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $mistake = RecoveryMistake::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(ManageMistake::class)->call('openForEdit', $mistake->id);
    }

    public function test_deleting_from_show_page_redirects_to_the_list(): void
    {
        $user = User::factory()->create();
        $mistake = RecoveryMistake::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(MistakeShow::class, ['mistake' => $mistake])
            ->call('delete')
            ->assertRedirect(route('recovery.mistakes'));

        $this->assertDatabaseMissing('recovery_mistakes', ['id' => $mistake->id]);
    }
}
