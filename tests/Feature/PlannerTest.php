<?php

namespace Tests\Feature;

use App\Enums\DayStatus;
use App\Enums\TaskStatus;
use App\Models\Day;
use App\Models\Task;
use App\Models\User;
use App\Models\Week;
use App\Services\DayService;
use App\Services\PlannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlannerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_resolve_day_creates_the_day_and_its_saturday_week(): void
    {
        // Tuesday 2026-08-11 → its week starts Saturday 2026-08-08.
        $user = User::factory()->create();
        $day = app(PlannerService::class)->resolveDay($user, Carbon::parse('2026-08-11'));

        $this->assertTrue(Day::whereKey($day->id)->whereDate('date', '2026-08-11')->exists());
        $this->assertNotNull($day->week_id);
        $this->assertSame('2026-08-08', $day->week->start_date->toDateString());
        $this->assertSame(Carbon::SATURDAY, $day->week->start_date->dayOfWeek);
    }

    public function test_resolve_day_is_idempotent(): void
    {
        $user = User::factory()->create();
        $service = app(PlannerService::class);

        $a = $service->resolveDay($user, Carbon::parse('2026-08-11'));
        $b = $service->resolveDay($user, Carbon::parse('2026-08-11'));

        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, Day::count());
        $this->assertSame(1, Week::count());
    }

    public function test_starting_the_day_records_the_time(): void
    {
        Carbon::setTestNow('2026-08-11 09:30:00');
        $day = Day::factory()->create();

        app(DayService::class)->start($day);

        $this->assertNotNull($day->fresh()->started_at);
        $this->assertSame('09:30', $day->fresh()->started_at->format('H:i'));
    }

    public function test_worked_minutes_subtract_breaks(): void
    {
        Carbon::setTestNow('2026-08-11 14:00:00');
        $day = Day::factory()->create([
            'started_at' => '2026-08-11 10:00:00', // 4 hours ago
        ]);
        // a 30-minute break
        $day->breaks()->create([
            'started_at' => '2026-08-11 11:00:00',
            'ended_at' => '2026-08-11 11:30:00',
        ]);

        $this->assertSame(30, $day->breakMinutes());
        $this->assertSame(240 - 30, $day->workedMinutes());
    }

    public function test_toggle_break_starts_then_ends(): void
    {
        Carbon::setTestNow('2026-08-11 10:00:00');
        $day = Day::factory()->create(['started_at' => '2026-08-11 09:00:00']);
        $service = app(DayService::class);

        $break = $service->startBreak($day);
        $this->assertNotNull($break);
        $this->assertTrue($day->breaks()->whereNull('ended_at')->exists());

        // starting again while one is running is a no-op
        $this->assertNull($service->startBreak($day));

        $service->endBreak($break);
        $this->assertFalse($day->breaks()->whereNull('ended_at')->exists());
    }

    public function test_closing_the_day_carries_incomplete_task_to_next_day(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->on('2026-08-11')->create();
        $task = Task::factory()->for($user)->progress(40)->create(['day_id' => $day->id]);

        app(DayService::class)->close($day, rating: 8, reflection: 'يوم جيد', decisions: [$task->id => 'carry']);

        $day->refresh();
        $task->refresh();

        $this->assertSame(DayStatus::Closed, $day->status);
        $this->assertSame(8, $day->rating);

        // task moved to 2026-08-12, progress preserved, carry counted
        $nextDay = Day::where('user_id', $user->id)->whereDate('date', '2026-08-12')->first();
        $this->assertNotNull($nextDay);
        $this->assertSame($nextDay->id, $task->day_id);
        $this->assertSame(40, $task->progress);
        $this->assertSame(1, $task->carry_count);
    }

    public function test_closing_the_day_can_send_incomplete_task_to_pool(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->on('2026-08-11')->create();
        $task = Task::factory()->for($user)->progress(20)->create(['day_id' => $day->id]);

        app(DayService::class)->close($day, rating: 5, reflection: null, decisions: [$task->id => 'pool']);

        $this->assertNull($task->fresh()->day_id);
    }

    public function test_completed_tasks_are_not_carried(): void
    {
        $user = User::factory()->create();
        $day = Day::factory()->for($user)->on('2026-08-11')->create();
        $done = Task::factory()->for($user)->progress(100)->create(['day_id' => $day->id]);

        app(DayService::class)->close($day, rating: 9, reflection: null);

        $this->assertSame($day->id, $done->fresh()->day_id);
    }

    public function test_set_progress_keeps_status_in_sync(): void
    {
        $task = Task::factory()->create(['progress' => 0]);

        $task->setProgress(100);
        $this->assertSame(TaskStatus::Done, $task->status);

        $task->setProgress(50);
        $this->assertSame(TaskStatus::InProgress, $task->status);

        $task->setProgress(0);
        $this->assertSame(TaskStatus::Pending, $task->status);
    }

    public function test_day_completion_is_the_average_task_progress(): void
    {
        $day = Day::factory()->create();
        Task::factory()->progress(100)->create(['day_id' => $day->id]);
        Task::factory()->progress(50)->create(['day_id' => $day->id]);

        $this->assertSame(75, $day->completionPercent());
    }
}
