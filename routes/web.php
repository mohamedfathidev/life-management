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

// ---------------------------------------------------------------------------
// Arena — shared challenges (owner + participants). Separate login; participants
// can reach ONLY this area.
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('arena/login', \App\Livewire\Arena\Login::class)->name('arena.login');
    Route::get('arena/register', \App\Livewire\Arena\Register::class)->name('arena.register');
});

Route::middleware('auth')->group(function () {
    Route::get('arena', \App\Livewire\Arena\Index::class)->name('arena.index');
    Route::get('arena/c/{challenge}', \App\Livewire\Arena\ChallengeShow::class)->name('arena.challenges.show');

    // Owner-only: create / edit a shared challenge.
    Route::middleware('owner.only')->group(function () {
        Route::get('arena/new', \App\Livewire\Arena\ManageChallenge::class)->name('arena.challenges.create');
        Route::get('arena/c/{challenge}/edit', \App\Livewire\Arena\ManageChallenge::class)->name('arena.challenges.edit');
    });

    Route::post('arena/logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('arena.login');
    })->name('arena.logout');
});

// The personal app — owner only.
Route::middleware(['auth', 'verified', 'owner.only'])->group(function () {
    Route::get('dashboard', DashboardOverview::class)->name('dashboard');
    Route::get('simply', \App\Livewire\Simply\Index::class)->name('simply');

    Route::get('goals', GoalsIndex::class)->name('goals.index');
    Route::get('goals/{goal}', GoalShow::class)->name('goals.show');

    Route::get('dreams', \App\Livewire\Dreams\Index::class)->name('dreams.index');
    Route::get('dreams/{dream}', \App\Livewire\Dreams\Show::class)->name('dreams.show');

    Route::get('tasks', \App\Livewire\Tasks\Index::class)->name('tasks.index');
    Route::get('tasks/{task}', \App\Livewire\Tasks\Show::class)->name('tasks.show');
    Route::get('focus', \App\Livewire\Focus\Index::class)->name('focus');
    Route::get('mind', \App\Livewire\Mind\Index::class)->name('mind');

    Route::get('planner', WeekView::class)->name('planner');
    Route::get('planner/pool', \App\Livewire\Planner\Pool::class)->name('planner.pool');
    Route::get('planner/week/{date}', WeekView::class)->name('planner.week');
    Route::get('planner/day/{date?}', DayShow::class)->name('planner.day');
    Route::get('planner/day-overview/{date?}', \App\Livewire\Planner\DayOverview::class)->name('planner.day-overview');

    Route::get('archive', \App\Livewire\Archive\Index::class)->name('archive');

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
    Route::get('career/scholarships/topics/{topic}', \App\Livewire\Scholarships\TopicShow::class)->name('scholarships.topics.show');
    Route::get('career/scholarships/volunteering', \App\Livewire\Scholarships\Volunteering::class)->name('scholarships.volunteering');
    Route::get('career/scholarships/documents', \App\Livewire\Scholarships\Documents::class)->name('scholarships.documents');
    Route::get('career/scholarships/resources', \App\Livewire\Career\Resources::class)->defaults('context', 'scholarship')->name('scholarships.resources');
    Route::get('career/resources/{resource}/image', function (\App\Models\CareerResource $resource) {
        abort_unless($resource->user_id === auth()->id(), 403);
        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($resource->image_path);
        abort_unless($resource->image_path && is_file($path), 404);

        return response()->file($path);
    })->name('career.resources.image');
    Route::get('career/scholarships/documents/{document}/file', function (\App\Models\ScholarshipDocument $document) {
        abort_unless($document->user_id === auth()->id(), 403);
        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($document->file_path);
        abort_unless($document->file_path && is_file($path), 404);

        return response()->file($path);
    })->name('scholarships.documents.file');
    Route::get('career/scholarships/{scholarship}', \App\Livewire\Scholarships\Show::class)->name('scholarships.show');

    Route::get('career/jobs', \App\Livewire\Jobs\Index::class)->name('jobs.index');
    Route::get('career/jobs/resources', \App\Livewire\Career\Resources::class)->defaults('context', 'job')->name('jobs.resources');
    Route::get('career/jobs/{job}', \App\Livewire\Jobs\Show::class)->name('jobs.show');

    Route::get('career/study', \App\Livewire\MarketStudy\Index::class)->name('market-study.index');
    Route::get('career/study/{track}', \App\Livewire\MarketStudy\Show::class)->name('market-study.show');

    Route::get('career/marketing', \App\Livewire\Marketing\Index::class)->name('marketing.index');

    Route::get('career/activities', \App\Livewire\Activities\Index::class)->name('activities.index');
    Route::get('career/activities/{activity}', \App\Livewire\Activities\Show::class)->name('activities.show');

    Route::get('career/pitfalls', \App\Livewire\Career\Pitfalls::class)->name('career.pitfalls');
    Route::get('career/interviews', \App\Livewire\Career\Interviews::class)->name('career.interviews');

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
        Route::get('diary/{entry}', \App\Livewire\Diary\Show::class)->name('diary.show');
        Route::get('recovery', RecoveryIndex::class)->name('recovery.index');
        Route::get('recovery/topics', \App\Livewire\Recovery\Topics::class)->name('recovery.topics');
        Route::get('recovery/topics/{topic}', \App\Livewire\Recovery\TopicShow::class)->name('recovery.topics.show');
        Route::get('recovery/nutrition', \App\Livewire\Recovery\MentalNutrition::class)->name('recovery.nutrition');
        Route::get('recovery/pledge', \App\Livewire\Recovery\Pledge::class)->name('recovery.pledge');
        Route::get('recovery/mistakes', \App\Livewire\Recovery\Mistakes::class)->name('recovery.mistakes');
        Route::get('recovery/mistakes/{mistake}', \App\Livewire\Recovery\MistakeShow::class)->name('recovery.mistakes.show');
        Route::get('recovery/setbacks', \App\Livewire\Recovery\Setbacks::class)->name('recovery.setbacks');
        Route::get('recovery/{recovery}', RecoveryShow::class)->name('recovery.show');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
