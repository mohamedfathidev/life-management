<?php

use App\Livewire\Dashboard\Overview as DashboardOverview;
use App\Livewire\Goals\Index as GoalsIndex;
use App\Livewire\Goals\Show as GoalShow;
use App\Livewire\Planner\DayShow;
use App\Livewire\Planner\WeekView;
use App\Livewire\Statistics\Index as StatisticsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardOverview::class)->name('dashboard');

    Route::get('goals', GoalsIndex::class)->name('goals.index');
    Route::get('goals/{goal}', GoalShow::class)->name('goals.show');

    Route::get('planner', WeekView::class)->name('planner');
    Route::get('planner/pool', \App\Livewire\Planner\Pool::class)->name('planner.pool');
    Route::get('planner/week/{date}', WeekView::class)->name('planner.week');
    Route::get('planner/day/{date?}', DayShow::class)->name('planner.day');

    Route::get('statistics', StatisticsIndex::class)->name('statistics');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
