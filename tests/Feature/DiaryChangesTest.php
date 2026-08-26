<?php

namespace Tests\Feature;

use App\Livewire\Diary\Changes;
use App\Models\DiaryChange;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class DiaryChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_change(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Changes::class)
            ->set('body', 'الأفعال بتدي ثقة أكتر من الكلام')
            ->call('save')
            ->assertHasNoErrors();

        $change = DiaryChange::first();
        $this->assertNotNull($change);
        $this->assertSame('الأفعال بتدي ثقة أكتر من الكلام', $change->body);
    }

    public function test_editing_updates_the_body(): void
    {
        $user = User::factory()->create();
        $change = DiaryChange::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(Changes::class)
            ->call('edit', $change->id)
            ->set('body', 'نص معدّل')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('نص معدّل', $change->fresh()->body);
    }

    public function test_deleting_removes_the_change(): void
    {
        $user = User::factory()->create();
        $change = DiaryChange::factory()->for($user)->create();

        Livewire::actingAs($user)->test(Changes::class)->call('delete', $change->id);

        $this->assertDatabaseMissing('diary_changes', ['id' => $change->id]);
    }

    public function test_body_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $change = DiaryChange::factory()->for($user)->create(['body' => 'سر شخصي جدًا']);

        $raw = DB::table('diary_changes')->where('id', $change->id)->value('body');

        $this->assertNotSame('سر شخصي جدًا', $raw);
        $this->assertSame('سر شخصي جدًا', $change->fresh()->body);
    }

    public function test_user_cannot_edit_or_delete_another_users_change(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $change = DiaryChange::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)
            ->test(Changes::class)
            ->call('edit', $change->id);
    }

    public function test_starring_a_change_toggles_is_important(): void
    {
        $user = User::factory()->create();
        $change = DiaryChange::factory()->for($user)->create(['is_important' => false]);

        Livewire::actingAs($user)->test(Changes::class)->call('toggleImportant', $change->id);
        $this->assertTrue($change->fresh()->is_important);

        Livewire::actingAs($user)->test(Changes::class)->call('toggleImportant', $change->id);
        $this->assertFalse($change->fresh()->is_important);
    }

    public function test_intruder_cannot_star_another_users_change(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $change = DiaryChange::factory()->for($owner)->create(['is_important' => false]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(Changes::class)->call('toggleImportant', $change->id);
    }

    public function test_starred_change_renders_as_a_circle_instead_of_a_row(): void
    {
        $user = User::factory()->create();
        DiaryChange::factory()->for($user)->create(['body' => 'حاجة مهمة جدًا', 'is_important' => true]);

        Livewire::actingAs($user)
            ->test(Changes::class)
            ->assertSee('حاجة مهمة جدًا')
            ->assertSeeHtml('rounded-full');
    }
}
