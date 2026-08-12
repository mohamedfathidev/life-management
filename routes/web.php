<?php

use App\Livewire\Dashboard\Overview as DashboardOverview;
use App\Livewire\Goals\Index as GoalsIndex;
use App\Livewire\Goals\Show as GoalShow;
use App\Livewire\Planner\DayShow;
use App\Livewire\Planner\WeekView;
use App\Livewire\Privacy\Unlock as PrivacyUnlock;
use App\Livewire\Recovery\Index as RecoveryIndex;
use App\Livewire\Recovery\Show as RecoveryShow;
use App\Livewire\Statistics\Index as StatisticsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardOverview::class)->name('dashboard');

    Route::get('goals', GoalsIndex::class)->name('goals.index');
    Route::get('goals/{goal}', GoalShow::class)->name('goals.show');

    Route::get('dreams', \App\Livewire\Dreams\Index::class)->name('dreams.index');
    Route::get('dreams/{dream}', \App\Livewire\Dreams\Show::class)->name('dreams.show');

    Route::get('tasks', \App\Livewire\Tasks\Index::class)->name('tasks.index');
    Route::get('focus', \App\Livewire\Focus\Index::class)->name('focus');

    Route::get('planner', WeekView::class)->name('planner');
    Route::get('planner/pool', \App\Livewire\Planner\Pool::class)->name('planner.pool');
    Route::get('planner/week/{date}', WeekView::class)->name('planner.week');
    Route::get('planner/day/{date?}', DayShow::class)->name('planner.day');

    Route::get('statistics', StatisticsIndex::class)->name('statistics');
    Route::get('review', \App\Livewire\Review\Index::class)->name('review');
    Route::get('achievements', \App\Livewire\Achievements\Index::class)->name('achievements');

    Route::get('habits', \App\Livewire\Habits\Index::class)->name('habits.index');
    Route::get('habits/{habit}', \App\Livewire\Habits\Show::class)->name('habits.show');

    Route::get('search', \App\Livewire\Search\Index::class)->name('search');
    Route::get('appointments', \App\Livewire\Appointments\Index::class)->name('appointments');
    Route::get('wallet', \App\Livewire\Wallet\Index::class)->name('wallet');
    Route::get('comfort-zone', \App\Livewire\ComfortZone\Index::class)->name('comfort-zone');
    Route::get('challenges', \App\Livewire\Challenges\Index::class)->name('challenges.index');
    Route::get('challenges/{challenge}', \App\Livewire\Challenges\Show::class)->name('challenges.show');

    // Religion section
    Route::get('religion', \App\Livewire\Religion\Index::class)->name('religion');
    Route::get('religion/prayers', \App\Livewire\Religion\Prayers::class)->name('religion.prayers');
    Route::get('religion/quran', \App\Livewire\Religion\Quran::class)->name('religion.quran');
    Route::get('religion/donations', \App\Livewire\Religion\Donations::class)->name('religion.donations');
    Route::get('religion/duaas', \App\Livewire\Religion\Duaas::class)->name('religion.duaas');

    // Career section
    Route::get('career', \App\Livewire\Career\Index::class)->name('career');
    Route::get('career/scholarships', \App\Livewire\Scholarships\Index::class)->name('scholarships.index');
    Route::get('career/scholarships/topics', \App\Livewire\Scholarships\Topics::class)->name('scholarships.topics');
    Route::get('career/scholarships/volunteering', \App\Livewire\Scholarships\Volunteering::class)->name('scholarships.volunteering');
    Route::get('career/scholarships/{scholarship}', \App\Livewire\Scholarships\Show::class)->name('scholarships.show');

    Route::get('career/jobs', \App\Livewire\Jobs\Index::class)->name('jobs.index');
    Route::get('career/jobs/{job}', \App\Livewire\Jobs\Show::class)->name('jobs.show');

    Route::get('career/study', \App\Livewire\MarketStudy\Index::class)->name('market-study.index');
    Route::get('career/study/{track}', \App\Livewire\MarketStudy\Show::class)->name('market-study.show');

    Route::get('career/marketing', \App\Livewire\Marketing\Index::class)->name('marketing.index');

    Route::get('career/activities', \App\Livewire\Activities\Index::class)->name('activities.index');
    Route::get('career/activities/{activity}', \App\Livewire\Activities\Show::class)->name('activities.show');

    Route::get('career/pitfalls', \App\Livewire\Career\Pitfalls::class)->name('career.pitfalls');

    Route::get('career/cvs', \App\Livewire\Cvs\Index::class)->name('cvs.index');
    Route::get('career/cvs/{cv}', \App\Livewire\Cvs\Show::class)->name('cvs.show');
    Route::get('career/cvs/{cv}/file', function (\App\Models\Cv $cv) {
        abort_unless($cv->user_id === auth()->id(), 403);
        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($cv->file_path);
        abort_unless(is_file($path), 404);

        return response()->file($path, ['Content-Type' => 'application/pdf']);
    })->name('cvs.file');

    // Privacy unlock gate (must NOT be behind the lock itself)
    Route::get('unlock', PrivacyUnlock::class)->name('privacy.unlock');

    // Sensitive sections gated by the privacy PIN
    Route::middleware('privacy.lock')->group(function () {
        Route::get('diary', \App\Livewire\Diary\Index::class)->name('diary.index');
        Route::get('recovery', RecoveryIndex::class)->name('recovery.index');
        Route::get('recovery/topics', \App\Livewire\Recovery\Topics::class)->name('recovery.topics');
        Route::get('recovery/nutrition', \App\Livewire\Recovery\MentalNutrition::class)->name('recovery.nutrition');
        Route::get('recovery/pledge', \App\Livewire\Recovery\Pledge::class)->name('recovery.pledge');
        Route::get('recovery/{recovery}', RecoveryShow::class)->name('recovery.show');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
