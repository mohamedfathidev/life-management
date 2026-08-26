<?php

namespace Tests\Feature;

use App\Livewire\Diary\Reasons;
use App\Models\DiaryReason;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class DiaryReasonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_top_level_reason(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Reasons::class)
            ->set('body', 'بعدي عن ربي')
            ->call('save')
            ->assertHasNoErrors();

        $reason = DiaryReason::first();
        $this->assertNotNull($reason);
        $this->assertSame('بعدي عن ربي', $reason->body);
        $this->assertNull($reason->parent_id);
    }

    public function test_user_can_branch_a_sub_reason_off_an_existing_one(): void
    {
        $user = User::factory()->create();
        $root = DiaryReason::factory()->for($user)->create(['body' => 'الكسل']);

        Livewire::actingAs($user)
            ->test(Reasons::class)
            ->set('body', 'الخوف من الفشل')
            ->set('parentId', $root->id)
            ->call('save')
            ->assertHasNoErrors();

        $child = $root->children()->first();
        $this->assertNotNull($child);
        $this->assertSame('الخوف من الفشل', $child->body);
    }

    public function test_editing_only_changes_the_body_not_the_parent(): void
    {
        $user = User::factory()->create();
        $root = DiaryReason::factory()->for($user)->create();
        $child = DiaryReason::factory()->for($user)->create(['parent_id' => $root->id]);

        Livewire::actingAs($user)
            ->test(Reasons::class)
            ->call('edit', $child->id)
            ->set('body', 'نص معدّل')
            ->call('save')
            ->assertHasNoErrors();

        $child->refresh();
        $this->assertSame('نص معدّل', $child->body);
        $this->assertSame($root->id, $child->parent_id);
    }

    public function test_deleting_a_reason_cascades_to_its_children(): void
    {
        $user = User::factory()->create();
        $root = DiaryReason::factory()->for($user)->create();
        $child = DiaryReason::factory()->for($user)->create(['parent_id' => $root->id]);

        Livewire::actingAs($user)->test(Reasons::class)->call('delete', $root->id);

        $this->assertDatabaseMissing('diary_reasons', ['id' => $root->id]);
        $this->assertDatabaseMissing('diary_reasons', ['id' => $child->id]);
    }

    public function test_body_is_encrypted_at_rest(): void
    {
        $user = User::factory()->create();
        $reason = DiaryReason::factory()->for($user)->create(['body' => 'سر شخصي جدًا']);

        $raw = DB::table('diary_reasons')->where('id', $reason->id)->value('body');

        $this->assertNotSame('سر شخصي جدًا', $raw);
        $this->assertSame('سر شخصي جدًا', $reason->fresh()->body);
    }

    public function test_user_cannot_edit_or_delete_another_users_reason(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $reason = DiaryReason::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)
            ->test(Reasons::class)
            ->call('edit', $reason->id);
    }

    public function test_intruder_delete_does_not_remove_owners_reason(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $reason = DiaryReason::factory()->for($owner)->create();

        Livewire::actingAs($intruder)->test(Reasons::class)->call('delete', $reason->id);

        $this->assertDatabaseHas('diary_reasons', ['id' => $reason->id]);
    }

    public function test_starring_a_reason_toggles_is_important(): void
    {
        $user = User::factory()->create();
        $reason = DiaryReason::factory()->for($user)->create(['is_important' => false]);

        Livewire::actingAs($user)->test(Reasons::class)->call('toggleImportant', $reason->id);
        $this->assertTrue($reason->fresh()->is_important);

        Livewire::actingAs($user)->test(Reasons::class)->call('toggleImportant', $reason->id);
        $this->assertFalse($reason->fresh()->is_important);
    }

    public function test_intruder_cannot_star_another_users_reason(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $reason = DiaryReason::factory()->for($owner)->create(['is_important' => false]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($intruder)->test(Reasons::class)->call('toggleImportant', $reason->id);
    }

    public function test_starred_reason_renders_as_a_circle_instead_of_a_row(): void
    {
        $user = User::factory()->create();
        DiaryReason::factory()->for($user)->create(['body' => 'خطر مهم جدًا', 'is_important' => true]);

        Livewire::actingAs($user)
            ->test(Reasons::class)
            ->assertSee('خطر مهم جدًا')
            ->assertSeeHtml('rounded-full');
    }
}
