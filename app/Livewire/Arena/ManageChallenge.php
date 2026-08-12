<?php

namespace App\Livewire\Arena;

use App\Models\SharedChallenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.arena')]
class ManageChallenge extends Component
{
    public ?SharedChallenge $challenge = null;

    public string $name = '';
    public ?string $description = null;
    public string $start_date = '';
    public ?string $end_date = null;

    public bool $prayerEnabled = true;
    public array $prayerPoints = ['jamaah' => 5, 'ontime' => 3, 'prayed' => 1, 'none' => 0];

    public bool $wirdEnabled = true;
    public int $wirdPerPage = 1;

    /** @var array<int, array{key:string,label:string,points:int}> */
    public array $extras = [];

    public function mount(?SharedChallenge $challenge = null): void
    {
        abort_unless(Auth::user()->isOwner(), 403);

        if ($challenge && $challenge->exists) {
            abort_unless($challenge->isOwnedBy(Auth::user()), 403);
            $this->challenge = $challenge;
            $this->name = $challenge->name;
            $this->description = $challenge->description;
            $this->start_date = $challenge->start_date->toDateString();
            $this->end_date = $challenge->end_date?->toDateString();
            $s = $challenge->scoring;
            $this->prayerEnabled = $s['prayer']['enabled'] ?? true;
            $this->prayerPoints = $s['prayer']['points'] ?? $this->prayerPoints;
            $this->wirdEnabled = $s['wird']['enabled'] ?? true;
            $this->wirdPerPage = $s['wird']['points_per_page'] ?? 1;
            $this->extras = $s['extras'] ?? [];
        } else {
            $this->start_date = Carbon::today()->toDateString();
            $this->extras = SharedChallenge::defaultScoring()['extras'];
        }
    }

    public function addExtra(): void
    {
        $this->extras[] = ['key' => 'extra_'.count($this->extras), 'label' => '', 'points' => 1];
    }

    public function removeExtra(int $i): void
    {
        unset($this->extras[$i]);
        $this->extras = array_values($this->extras);
    }

    public function save()
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'prayerPoints.jamaah' => ['required', 'integer', 'between:0,100'],
            'prayerPoints.ontime' => ['required', 'integer', 'between:0,100'],
            'prayerPoints.prayed' => ['required', 'integer', 'between:0,100'],
            'prayerPoints.none' => ['required', 'integer', 'between:0,100'],
            'wirdPerPage' => ['required', 'integer', 'between:0,100'],
            'extras.*.label' => ['required', 'string', 'max:100'],
            'extras.*.points' => ['required', 'integer', 'between:0,100'],
        ], attributes: ['name' => 'اسم التحدي', 'start_date' => 'تاريخ البداية']);

        $scoring = [
            'prayer' => ['enabled' => $this->prayerEnabled, 'points' => array_map('intval', $this->prayerPoints)],
            'wird' => ['enabled' => $this->wirdEnabled, 'points_per_page' => (int) $this->wirdPerPage],
            'extras' => array_map(fn ($e, $i) => [
                'key' => $e['key'] ?: 'extra_'.$i,
                'label' => $e['label'],
                'points' => (int) $e['points'],
            ], $this->extras, array_keys($this->extras)),
        ];

        if ($this->challenge) {
            $this->challenge->update([
                'name' => $data['name'], 'description' => $data['description'],
                'start_date' => $data['start_date'], 'end_date' => $data['end_date'],
                'scoring' => $scoring,
            ]);
            $challenge = $this->challenge;
        } else {
            $challenge = SharedChallenge::create([
                'owner_id' => Auth::id(),
                'name' => $data['name'], 'description' => $data['description'],
                'start_date' => $data['start_date'], 'end_date' => $data['end_date'],
                'join_code' => SharedChallenge::generateCode(),
                'scoring' => $scoring,
            ]);
            // The owner participates too.
            $challenge->participants()->attach(Auth::id());
        }

        return $this->redirectRoute('arena.challenges.show', $challenge, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.arena.manage-challenge');
    }
}
