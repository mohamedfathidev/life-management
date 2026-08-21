<?php

namespace Tests\Feature;

use App\Livewire\Recovery\Index;
use App\Livewire\Recovery\ManageLog;
use App\Livewire\Recovery\ManageRecovery;
use App\Livewire\Recovery\Setbacks;
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

    public function test_period_card_shows_duration_and_how_the_streak_is_counted(): void
    {
        $user = User::factory()->create();

        $planned = Recovery::factory()->for($user)->create([
            'title' => 'شهر كامل',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-14',
        ]);

        $openEnded = Recovery::factory()->for($user)->create([
            'title' => 'رحلة مفتوحة',
            'start_date' => '2026-08-09',
            'end_date' => null,
        ]);

        // Planned periods show their full length; open-ended ones count from start until today.
        $this->assertSame(14, $planned->periodTotalDays());
        $this->assertSame(3, $openEnded->periodTotalDays());

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('مدة الفترة: 14 يوم')
            ->assertSee('مدة الفترة: 3 يوم')
            ->assertSee('بتتعد من أول يوم تعافٍ');
    }

    public function test_period_card_explains_streak_restart_after_a_setback(): void
    {
        $user = User::factory()->create();

        $recovery = Recovery::factory()->for($user)->create([
            'title' => 'بعد انتكاسة',
            'start_date' => '2026-08-01',
        ]);
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-08')->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('بتتعد من آخر انتكاسة')
            ->assertSee('1 انتكاسة');
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

    public function test_each_clean_period_stands_alone_and_survives_the_next_setback(): void
    {
        // Down on the 9th, clean stretch after it, down again on the 11th (today).
        $recovery = Recovery::factory()->create(['start_date' => '2026-08-05']);
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-09')->create();
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-11')->create();

        // The fresh run just restarted…
        $this->assertSame(0, $recovery->currentStreakDays());
        // …but the completed stretch between the two setbacks is preserved: it stays 2, not 0.
        $this->assertSame(2, $recovery->lastCleanPeriodDays());
        // And each segment is counted separately for the record.
        $this->assertSame(4, $recovery->bestStreakDays()); // 08-05→08-09
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

    public function test_short_recovery_period_show_is_not_split_into_calendar_weeks(): void
    {
        $user = User::factory()->create();

        // "الأسبوع الثاني" spans 20–26 Aug, crossing the calendar-week boundary.
        $recovery = Recovery::factory()->for($user)->create([
            'title' => 'الأسبوع الثاني',
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-26',
        ]);

        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-20')->create();
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-26')->create();

        $component = Livewire::actingAs($user)
            ->test(Show::class, ['recovery' => $recovery]);

        // No sub-blocks are created for periods of 14 days or less.
        $this->assertTrue($component->viewData('availableWeeks')->isEmpty());

        // Whole period shown as a single unit — no "أسبوع 1" / "أسبوع 2" split.
        $component
            ->assertSee('الأسبوع الثاني')
            ->assertDontSee('أسبوع 1')
            ->assertDontSee('أسبوع 2');
    }

    public function test_long_recovery_period_show_splits_from_recovery_start_not_calendar_weeks(): void
    {
        $user = User::factory()->create();

        $recovery = Recovery::factory()->for($user)->create([
            'title' => 'تحدي ثلاثين يوم',
            'start_date' => '2026-08-19',
            'end_date' => '2026-09-02',
        ]);

        $component = Livewire::actingAs($user)
            ->test(Show::class, ['recovery' => $recovery]);

        $weeks = $component->viewData('availableWeeks');

        $this->assertCount(3, $weeks); // 19–25, 26-01, 02
        $this->assertSame('2026-08-19', $weeks->first()->start_date->toDateString());
        $this->assertSame('2026-09-02', $weeks->last()->end_date->toDateString());

        $component->assertSee('أسبوع 1');
    }

    public function test_short_recovery_period_setbacks_are_not_split_into_calendar_weeks(): void
    {
        $user = User::factory()->create();

        $recovery = Recovery::factory()->for($user)->create([
            'title' => 'الأسبوع الثاني',
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-26',
        ]);

        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-20')->create();
        RecoveryLog::factory()->for($recovery)->setbackOn('2026-08-26')->create();

        $component = Livewire::actingAs($user)
            ->test(Setbacks::class)
            ->set('recoveryId', $recovery->id);

        // No sub-blocks for short periods — everything is one unit.
        $this->assertTrue($component->viewData('availableWeeks')->isEmpty());

        // Both setbacks (on either side of the calendar-week boundary) appear together.
        $component
            ->assertSee('الأسبوع الثاني')
            ->assertDontSee('الأسبوع 1 في التحدي')
            ->assertDontSee('الأسبوع 2 في التحدي')
            ->assertSee('عدد الانتكاسات: 2');
    }

    public function test_long_recovery_period_setbacks_split_from_recovery_start_not_calendar_weeks(): void
    {
        $user = User::factory()->create();

        $recovery = Recovery::factory()->for($user)->create([
            'title' => 'تحدي ثلاثين يوم',
            'start_date' => '2026-08-19',
            'end_date' => '2026-09-02',
        ]);

        $component = Livewire::actingAs($user)
            ->test(Setbacks::class)
            ->set('recoveryId', $recovery->id);

        $weeks = $component->viewData('availableWeeks');

        $this->assertCount(3, $weeks);
        $this->assertSame('2026-08-19', $weeks->first()->start_date->toDateString());
        $this->assertSame('2026-09-02', $weeks->last()->end_date->toDateString());

        $component->assertSee('الأسبوع 3 في التحدي');
    }
}
