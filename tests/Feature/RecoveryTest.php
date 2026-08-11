<?php

namespace Tests\Feature;

use App\Livewire\Recovery\ManageLog;
use App\Livewire\Recovery\ManageRecovery;
use App\Livewire\Recovery\Show;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class RecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-08-11 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_streak_counts_days_since_start_without_setbacks(): void
    {
        $recovery = Recovery::factory()->create(['start_date' => '2026-08-01']);

        $this->assertSame(10, $recovery->currentStreakDays());
        $this->assertSame(0, $recovery->setbackCount());
    }

    public function test_streak_resets_on_the_latest_setback(): void
    {
        $recovery = Recovery::factory()->create(['start_date' => '2026-08-01']);
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-05')->create();
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-09')->create();

        $this->assertSame(2, $recovery->currentStreakDays()); // 08-09 → 08-11
        $this->assertSame(2, $recovery->setbackCount());
    }

    public function test_best_streak_is_the_longest_clean_gap(): void
    {
        $recovery = Recovery::factory()->create(['start_date' => '2026-08-01']);
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-09')->create();

        // gaps: 08-01→08-09 = 8, 08-09→08-11 = 2  → best 8
        $this->assertSame(8, $recovery->bestStreakDays());
    }

    public function test_user_can_create_a_recovery(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageRecovery::class)
            ->call('openForCreate')
            ->set('form.title', 'تعافٍ من التدخين')
            ->set('form.start_date', '2026-08-01')
            ->set('form.status', 'active')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('recovery-saved');

        $this->assertDatabaseHas('recoveries', ['title' => 'تعافٍ من التدخين', 'user_id' => $user->id]);
    }

    public function test_logging_a_setback_is_recorded(): void
    {
        $user = User::factory()->create();
        $recovery = Recovery::factory()->for($user)->create(['start_date' => '2026-08-01']);

        Livewire::actingAs($user)
            ->test(ManageLog::class)
            ->call('openForCreate', recoveryId: $recovery->id, setback: true)
            ->set('form.date', '2026-08-10')
            ->set('form.urge_level', 9)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('recovery-log-saved');

        $this->assertDatabaseHas('recovery_logs', [
            'recovery_id' => $recovery->id,
            'is_setback' => true,
        ]);
        $this->assertSame(1, $recovery->fresh()->currentStreakDays()); // 08-10 → 08-11
    }

    public function test_sensitive_note_is_encrypted_at_rest(): void
    {
        $recovery = Recovery::factory()->create();
        $log = RecoveryLog::factory()->for($recovery)->create(['note' => 'معلومة حساسة']);

        $raw = DB::table('recovery_logs')->where('id', $log->id)->value('note');

        $this->assertNotSame('معلومة حساسة', $raw);      // stored ciphertext
        $this->assertSame('معلومة حساسة', $log->fresh()->note); // decrypted by the model
    }

    public function test_user_cannot_view_another_users_recovery(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $recovery = Recovery::factory()->for($owner)->create();

        Livewire::actingAs($intruder)
            ->test(Show::class, ['recovery' => $recovery])
            ->assertForbidden();
    }
}
