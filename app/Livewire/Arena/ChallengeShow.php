<?php

namespace App\Livewire\Arena;

use App\Models\ChallengeEntry;
use App\Models\SharedChallenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.arena')]
class ChallengeShow extends Component
{
    public SharedChallenge $challenge;

    /** The day being logged/edited (Y-m-d). */
    public string $entryDate = '';

    /** @var array<string, string> prayer key => state */
    public array $prayers = [];
    public int $wirdPages = 0;
    /** @var array<string, bool> extra key => done */
    public array $extrasDone = [];

    public const PRAYERS = ['fajr' => 'الفجر', 'dhuhr' => 'الظهر', 'asr' => 'العصر', 'maghrib' => 'المغرب', 'isha' => 'العشاء'];
    public const STATES = ['jamaah' => 'جماعة', 'ontime' => 'في وقتها', 'prayed' => 'صلّاها', 'none' => 'لأ'];

    public function mount(SharedChallenge $challenge): void
    {
        $user = Auth::user();
        abort_unless($challenge->isJoinedBy($user) || $challenge->isOwnedBy($user), 403);
        $this->challenge = $challenge;
        $this->entryDate = $this->maxDate();
        $this->loadEntry();
    }

    /** The latest loggable day: min(today, end_date), never before start_date. */
    private function maxDate(): Carbon
    {
        $today = Carbon::today();
        $max = $this->challenge->end_date && $this->challenge->end_date->lt($today)
            ? $this->challenge->end_date->copy()
            : $today->copy();

        return $max->lt($this->challenge->start_date) ? $this->challenge->start_date->copy() : $max;
    }

    private function loadEntry(): void
    {
        $entry = ChallengeEntry::where('shared_challenge_id', $this->challenge->id)
            ->where('user_id', Auth::id())->whereDate('date', $this->entryDate)->first();

        $this->prayers = $entry?->prayers ?? array_fill_keys(array_keys(self::PRAYERS), 'none');

        $this->wirdPages = $entry?->wird_pages ?? 0;

        $saved = $entry?->extras ?? [];
        $this->extrasDone = collect($this->challenge->scoring['extras'] ?? [])
            ->mapWithKeys(fn ($e) => [$e['key'] => ! empty($saved[$e['key']])])->all();
    }

    public function setPrayer(string $key, string $state): void
    {
        if (isset(self::PRAYERS[$key]) && isset(self::STATES[$state])) {
            $this->prayers[$key] = $state;
        }
    }

    public function changeDate(int $dir): void
    {
        $new = Carbon::parse($this->entryDate)->addDays($dir);
        $min = $this->challenge->start_date;
        $max = $this->maxDate();

        if ($new->lt($min) || $new->gt($max)) {
            return;
        }

        $this->entryDate = $new->toDateString();
        $this->loadEntry();
    }

    public function saveEntry(): void
    {
        abort_unless($this->challenge->isJoinedBy(Auth::user()), 403);

        $date = Carbon::parse($this->entryDate);
        abort_if($date->lt($this->challenge->start_date) || $date->gt($this->maxDate()), 422);

        $points = $this->challenge->computePoints($this->prayers, (int) $this->wirdPages, $this->extrasDone);

        ChallengeEntry::updateOrCreate(
            ['shared_challenge_id' => $this->challenge->id, 'user_id' => Auth::id(), 'date' => $this->entryDate],
            ['prayers' => $this->prayers, 'wird_pages' => (int) $this->wirdPages, 'extras' => $this->extrasDone, 'points' => $points],
        );

        $this->dispatch('entry-saved');
    }

    public function leave()
    {
        $user = Auth::user();
        if (! $this->challenge->isOwnedBy($user)) {
            $this->challenge->participants()->detach($user->id);

            return $this->redirectRoute('arena.index', navigate: true);
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        $participants = $this->challenge->participants()->orderBy('name')->get();

        // Leaderboard: total points per participant (participants with no entries score 0).
        $totals = ChallengeEntry::where('shared_challenge_id', $this->challenge->id)
            ->selectRaw('user_id, SUM(points) as total')
            ->groupBy('user_id')->pluck('total', 'user_id');

        $leaderboard = $participants->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'total' => (int) ($totals[$p->id] ?? 0),
            'isOwner' => $p->id === $this->challenge->owner_id,
            'isMe' => $p->id === $user->id,
        ])->sortByDesc('total')->values();

        $livePoints = $this->challenge->computePoints($this->prayers, (int) $this->wirdPages, $this->extrasDone);

        return view('livewire.arena.challenge-show', [
            'isOwner' => $this->challenge->isOwnedBy($user),
            'participants' => $participants,
            'inviteUrl' => route('arena.register', ['code' => $this->challenge->join_code]),
            'leaderboard' => $leaderboard,
            'livePoints' => $livePoints,
            'canLogToday' => Carbon::parse($this->entryDate)->lte($this->maxDate()) && Carbon::parse($this->entryDate)->gte($this->challenge->start_date),
            'dateLabel' => Carbon::parse($this->entryDate)->translatedFormat('l، j M'),
            'isMaxDate' => $this->entryDate === $this->maxDate()->toDateString(),
            'isMinDate' => $this->entryDate === $this->challenge->start_date->toDateString(),
            'prayerLabels' => self::PRAYERS,
            'stateLabels' => self::STATES,
        ]);
    }
}
