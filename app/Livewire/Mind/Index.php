<?php

namespace App\Livewire\Mind;

use App\Models\MindSession;
use App\Support\MindGames;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "تنضيف العقل" — a brain-training log: pick a logical game, record a session,
 * and track your escape from AI-era mental laziness.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public string $game = '';
    public ?int $minutes = null;
    public string $date = '';
    public ?string $note = null;

    public function mount(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function pickGame(string $game): void
    {
        $this->game = $game;
    }

    public function logSession(): void
    {
        $data = $this->validate([
            'game' => ['required', 'string', 'max:255'],
            'minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ], attributes: ['game' => 'اللعبة', 'minutes' => 'الدقايق', 'date' => 'التاريخ']);

        MindSession::create($data + ['user_id' => Auth::id()]);

        $this->reset('game', 'minutes', 'note');
        $this->date = Carbon::today()->toDateString();
    }

    public function delete(int $id): void
    {
        MindSession::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    /** Consecutive-day streak of training, ending today or yesterday. */
    private function streak(): int
    {
        $dates = MindSession::query()->where('user_id', Auth::id())
            ->orderByDesc('date')->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())->unique()->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $cursor = $dates->contains($today->toDateString()) ? $today->copy() : $today->copy()->subDay();

        $streak = 0;
        while ($dates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    public function render(): View
    {
        $user = Auth::user();
        $weekStart = Carbon::today()->startOfWeek();

        $weekSessions = MindSession::query()->ownedBy($user)
            ->whereDate('date', '>=', $weekStart->toDateString())->get();

        return view('livewire.mind.index', [
            'games' => MindGames::all(),
            'sessions' => MindSession::query()->ownedBy($user)->orderByDesc('date')->latest()->limit(30)->get(),
            'weekCount' => $weekSessions->count(),
            'weekMinutes' => (int) $weekSessions->sum('minutes'),
            'streak' => $this->streak(),
        ]);
    }
}
