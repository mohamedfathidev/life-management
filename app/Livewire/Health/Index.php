<?php

namespace App\Livewire\Health;

use App\Models\HealthHarm;
use App\Models\HealthLog;
use App\Models\HealthPurchase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "الصحة" — daily healthy-habit checklist, a ranked list of health harms to
 * stay aware of, and a streak tracker for the last unhealthy-snack purchase.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    // --- Harms form -----------------------------------------------------
    public ?int $editingHarmId = null;

    public string $harmTitle = '';

    public ?string $harmNote = null;

    public int $harmSeverity = 50;

    // --- Purchase form ----------------------------------------------------
    public string $purchaseItem = '';

    public string $purchaseDate = '';

    public ?string $purchaseNote = null;

    public function mount(): void
    {
        $this->purchaseDate = Carbon::today()->toDateString();
    }

    // --- Today's checklist --------------------------------------------------

    public function toggleToday(string $field): void
    {
        if (! in_array($field, ['healthy_eating', 'slept_early', 'woke_early', 'phone_away_sleep'], true)) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $log = HealthLog::ownedBy(Auth::user())->whereDate('date', $today)->first();

        if ($log) {
            $log->update([$field => ! $log->{$field}]);
        } else {
            HealthLog::create([
                'user_id' => Auth::id(),
                'date' => $today,
                $field => true,
            ]);
        }
    }

    // --- Harms ------------------------------------------------------------

    public function saveHarm(): void
    {
        $data = $this->validate([
            'harmTitle' => ['required', 'string', 'max:255'],
            'harmNote' => ['nullable', 'string', 'max:2000'],
            'harmSeverity' => ['required', 'integer', 'between:0,100'],
        ], attributes: ['harmTitle' => 'الضرر', 'harmNote' => 'ملاحظة', 'harmSeverity' => 'الخطورة']);

        if ($this->editingHarmId) {
            HealthHarm::ownedBy(Auth::user())->where('id', $this->editingHarmId)->update([
                'title' => $data['harmTitle'],
                'note' => $data['harmNote'],
                'severity' => $data['harmSeverity'],
            ]);
        } else {
            HealthHarm::create([
                'user_id' => Auth::id(),
                'title' => $data['harmTitle'],
                'note' => $data['harmNote'],
                'severity' => $data['harmSeverity'],
            ]);
        }

        $this->resetHarmForm();
    }

    public function editHarm(int $id): void
    {
        $harm = HealthHarm::ownedBy(Auth::user())->findOrFail($id);

        $this->editingHarmId = $harm->id;
        $this->harmTitle = $harm->title;
        $this->harmNote = $harm->note;
        $this->harmSeverity = $harm->severity;
    }

    public function deleteHarm(int $id): void
    {
        HealthHarm::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingHarmId === $id) {
            $this->resetHarmForm();
        }
    }

    public function resetHarmForm(): void
    {
        $this->reset(['editingHarmId', 'harmTitle', 'harmNote']);
        $this->harmSeverity = 50;
        $this->resetValidation();
    }

    // --- Unhealthy purchases ------------------------------------------------

    public function logPurchase(): void
    {
        $data = $this->validate([
            'purchaseItem' => ['required', 'string', 'max:255'],
            'purchaseDate' => ['required', 'date'],
            'purchaseNote' => ['nullable', 'string', 'max:1000'],
        ], attributes: ['purchaseItem' => 'الحاجة اللي اشتريتها', 'purchaseDate' => 'التاريخ']);

        HealthPurchase::create([
            'user_id' => Auth::id(),
            'date' => $data['purchaseDate'],
            'item' => $data['purchaseItem'],
            'note' => $data['purchaseNote'],
        ]);

        $this->reset(['purchaseItem', 'purchaseNote']);
        $this->purchaseDate = Carbon::today()->toDateString();
        $this->resetValidation();
    }

    public function deletePurchase(int $id): void
    {
        HealthPurchase::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    public function render(): View
    {
        $user = Auth::user();

        // Seed the two harms the module exists to track, the first time.
        if (HealthHarm::ownedBy($user)->count() === 0) {
            HealthHarm::insert([
                ['user_id' => $user->id, 'title' => 'السهر — بيأثر على الدماغ والعقل', 'severity' => 75, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $user->id, 'title' => 'استخدام الهاتف بكثرة', 'severity' => 60, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $today = HealthLog::ownedBy($user)->whereDate('date', Carbon::today())->first();
        $harms = HealthHarm::ownedBy($user)->orderByDesc('severity')->get();
        $purchases = HealthPurchase::ownedBy($user)->orderByDesc('date')->limit(10)->get();
        $lastPurchase = $purchases->first();

        return view('livewire.health.index', [
            'today' => $today,
            'harms' => $harms,
            'purchases' => $purchases,
            'daysSincePurchase' => $lastPurchase ? (int) $lastPurchase->date->diffInDays(Carbon::today()) : null,
        ]);
    }
}
