<?php

namespace App\Livewire\Recovery;

use App\Enums\RecoveryRoad;
use App\Enums\RecoveryStatus;
use App\Livewire\Concerns\BuildsWindingRoad;
use App\Models\Recovery;
use App\Models\RecoveryDamage;
use App\Models\RecoveryDream;
use App\Models\RecoveryHardMoment;
use App\Models\RecoveryLog;
use App\Models\RecoveryMistake;
use App\Models\RecoveryRoadItemOverride;
use App\Models\RecoveryRoadNote;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * The drawn, detailed view of a single road from "قبل الوقوع تذكر" — its
 * stops (real data + the user's own added notes) laid out as a winding path
 * from "إنت هنا" down to the road's actual destination. Every stop, real or
 * custom, can be edited or hidden from here without touching the underlying
 * record (trigger notes, damages, dreams, ... stay intact for the rest of
 * the app; only how they show up on this road changes).
 */
#[Layout('layouts.app')]
class RememberRoad extends Component
{
    use BuildsWindingRoad;

    public string $road;

    public string $newStartNote = '';

    public string $newHarvestNote = '';

    public string $newFinaleNote = '';

    public ?string $editingItem = null;

    public string $editingText = '';

    public function mount(string $road): void
    {
        abort_unless(RecoveryRoad::tryFrom($road) !== null, 404);

        $this->road = $road;
    }

    public function addNote(string $stage): void
    {
        $property = match ($stage) {
            'start' => 'newStartNote',
            'finale' => 'newFinaleNote',
            default => 'newHarvestNote',
        };

        $this->validate([
            $property => ['required', 'string', 'max:500'],
        ], attributes: [$property => 'الملاحظة']);

        RecoveryRoadNote::create([
            'user_id' => Auth::id(),
            'road' => $this->road,
            'stage' => $stage,
            'body' => trim($this->{$property}),
        ]);

        $this->{$property} = '';
    }

    public function startEdit(string $identifier, string $currentText): void
    {
        $this->editingItem = $identifier;
        $this->editingText = $currentText;
    }

    public function cancelEdit(): void
    {
        $this->editingItem = null;
        $this->editingText = '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingText' => ['required', 'string', 'max:1000'],
        ], attributes: ['editingText' => 'النص']);

        if ($this->editingItem === null) {
            return;
        }

        if (str_starts_with($this->editingItem, 'note:')) {
            $id = (int) substr($this->editingItem, 5);
            // Loaded as a model instance (not a mass update) so the
            // `encrypted` cast actually re-encrypts the new value.
            $note = RecoveryRoadNote::ownedBy(Auth::user())->where('id', $id)->first();
            $note?->update(['body' => trim($this->editingText)]);
        } elseif (str_starts_with($this->editingItem, 'real:')) {
            $key = substr($this->editingItem, 5);
            RecoveryRoadItemOverride::updateOrCreate(
                ['user_id' => Auth::id(), 'source_key' => $key],
                ['body' => trim($this->editingText), 'hidden' => false]
            );
        }

        $this->cancelEdit();
    }

    public function removeItem(string $identifier): void
    {
        if (str_starts_with($identifier, 'note:')) {
            $id = (int) substr($identifier, 5);
            RecoveryRoadNote::ownedBy(Auth::user())->where('id', $id)->delete();
        } elseif (str_starts_with($identifier, 'real:')) {
            $key = substr($identifier, 5);
            RecoveryRoadItemOverride::updateOrCreate(
                ['user_id' => Auth::id(), 'source_key' => $key],
                ['hidden' => true]
            );
        }

        if ($this->editingItem === $identifier) {
            $this->cancelEdit();
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        $roadEnum = RecoveryRoad::from($this->road);
        $overrides = RecoveryRoadItemOverride::ownedBy($user)->get()->keyBy('source_key');

        [$startItems, $harvestItems, $footerStat] = $roadEnum === RecoveryRoad::Destruction
            ? $this->destructionData($user, $overrides)
            : $this->salvationData($user, $overrides);

        $customStart = RecoveryRoadNote::ownedBy($user)->road($roadEnum)->stage('start')->latest()->get()
            ->map(fn (RecoveryRoadNote $n) => ['text' => $n->body, 'identifier' => "note:{$n->id}"]);
        $customHarvest = RecoveryRoadNote::ownedBy($user)->road($roadEnum)->stage('harvest')->latest()->get()
            ->map(fn (RecoveryRoadNote $n) => ['text' => $n->body, 'identifier' => "note:{$n->id}"]);

        $startItems = $startItems->concat($customStart)->values();
        $harvestItems = $harvestItems->concat($customHarvest)->values();

        $map = $this->buildRoadMap($roadEnum, $startItems, $harvestItems);

        // Custom items added directly at the road's end — these exist even
        // when there's no real data yet (e.g. no dreams recorded so far).
        $customFinale = RecoveryRoadNote::ownedBy($user)->road($roadEnum)->stage('finale')->latest()->get()
            ->map(fn (RecoveryRoadNote $n) => ['text' => $n->body, 'identifier' => "note:{$n->id}"]);

        $finale = null;
        $victory = null;
        if ($roadEnum === RecoveryRoad::Destruction) {
            $finale = array_merge($this->destructionFinale($user, $overrides), $customFinale->all());
        } else {
            $victory = $this->salvationFinale($user, $overrides);
            $victory['items'] = array_merge($victory['items'], $customFinale->all());
        }

        return view('livewire.recovery.remember-road', [
            'roadEnum' => $roadEnum,
            'footerStat' => $footerStat,
            'nodes' => $map['nodes'],
            'pathD' => $map['pathD'],
            'height' => $map['height'],
            'editingItem' => $this->editingItem,
            'finale' => $finale,
            'victory' => $victory,
        ]);
    }

    /** @return array{0: Collection, 1: Collection, 2: ?string} */
    private function destructionData(User $user, Collection $overrides): array
    {
        $recoveries = Recovery::ownedBy($user)->get();
        $recoveryIds = $recoveries->pluck('id');

        $recentTriggers = RecoveryLog::query()
            ->whereIn('recovery_id', $recoveryIds)
            ->where('is_setback', true)
            ->whereNotNull('trigger_note')
            ->latest('date')
            ->limit(20)
            ->get(['id', 'trigger_note'])
            ->filter(fn (RecoveryLog $log) => trim((string) $log->trigger_note) !== '')
            ->unique('trigger_note')
            ->take(5)
            ->map(fn (RecoveryLog $log) => ['text' => $log->trigger_note, 'key' => "log_trigger:{$log->id}"]);

        $hardMoments = RecoveryHardMoment::ownedBy($user)->latest()->limit(4)->get()
            ->map(fn (RecoveryHardMoment $m) => ['text' => $m->title, 'key' => "hard_moment:{$m->id}"]);

        $startItems = $this->applyOverrides(collect($recentTriggers)->concat($hardMoments)->values(), $overrides);
        if ($startItems->isEmpty()) {
            $startItems = collect([
                ['text' => 'فكرة عابرة بتقول لك "جرّب مرة واحدة بس"', 'identifier' => null],
                ['text' => 'لحظة فراغ أو ملل بتفتح فيها الباب', 'identifier' => null],
                ['text' => 'قرار صغير إنك تفتح حاجة "من غير قصد"', 'identifier' => null],
            ]);
        }

        $damages = RecoveryDamage::query()->ownedBy($user)->main()->orderByDesc('degree')->limit(5)->get()
            ->map(fn (RecoveryDamage $d) => ['text' => $d->title, 'key' => "damage:{$d->id}"]);
        $mistakes = RecoveryMistake::ownedBy($user)->orderByDesc('weight')->limit(4)->get()
            ->map(fn (RecoveryMistake $m) => ['text' => $m->title, 'key' => "mistake:{$m->id}"]);

        $harvestItems = $this->applyOverrides(collect($damages)->concat($mistakes)->values(), $overrides);
        if ($harvestItems->isEmpty()) {
            $harvestItems = collect([
                ['text' => 'ندم بيقعدلك أيام', 'identifier' => null],
                ['text' => 'وقت وطاقة ضايعين كان ممكن تبني بيهم حاجة', 'identifier' => null],
                ['text' => 'ثقتك في نفسك بتتهزّ من جديد', 'identifier' => null],
            ]);
        }

        $setbackCount = $recoveries->sum(fn (Recovery $r) => $r->setbackCount());
        $footer = $setbackCount > 0 ? "رجعت للطريق ده {$setbackCount} مرة قبل كده — عارف نهايته" : null;

        return [$startItems, $harvestItems, $footer];
    }

    /**
     * The full, uncapped reveal of every damage and mistake the user has
     * recorded — shown as a dramatic finale at the end of طريق الهلاك,
     * not just the handful sampled into the winding path above.
     */
    private function destructionFinale(User $user, Collection $overrides): array
    {
        $damages = RecoveryDamage::query()->ownedBy($user)->main()->orderByDesc('degree')->get()
            ->map(fn (RecoveryDamage $d) => ['text' => $d->title, 'key' => "damage:{$d->id}", 'severity' => $d->degree]);

        $mistakes = RecoveryMistake::ownedBy($user)->orderByDesc('weight')->get()
            ->map(fn (RecoveryMistake $m) => ['text' => $m->title, 'key' => "mistake:{$m->id}", 'severity' => $m->weight]);

        return $this->applyOverrides(collect($damages)->concat($mistakes)->values(), $overrides)->all();
    }

    /** @return array{0: Collection, 1: Collection, 2: ?string} */
    private function salvationData(User $user, Collection $overrides): array
    {
        $recoveries = Recovery::ownedBy($user)->get();
        $recoveryIds = $recoveries->pluck('id');
        $active = $recoveries->firstWhere('status', RecoveryStatus::Active);

        $protectionLogs = RecoveryLog::query()
            ->whereIn('recovery_id', $recoveryIds)
            ->where('is_setback', false)
            ->whereNotNull('protection_actions')
            ->latest('date')
            ->limit(20)
            ->get(['id', 'protection_actions']);

        $protectionActions = collect();
        foreach ($protectionLogs as $log) {
            foreach ((array) $log->protection_actions as $i => $action) {
                if (trim((string) $action) === '') {
                    continue;
                }
                $protectionActions->push(['text' => $action, 'key' => "log_protection:{$log->id}:{$i}"]);
            }
        }
        $protectionActions = $protectionActions->unique('text')->take(5)->values();

        $copingPlans = RecoveryHardMoment::ownedBy($user)->latest()->limit(10)->get()
            ->filter(fn (RecoveryHardMoment $m) => filled($m->plan))
            ->take(4)
            ->map(fn (RecoveryHardMoment $m) => ['text' => $m->plan, 'key' => "hard_moment_plan:{$m->id}"]);

        $startItems = $this->applyOverrides(collect($protectionActions)->concat($copingPlans)->values(), $overrides);
        if ($startItems->isEmpty()) {
            $startItems = collect([
                ['text' => 'قرار سريع إنك تسيب المكان أو تقفل الموبايل', 'identifier' => null],
                ['text' => 'مكالمة أو رسالة لحد بتثق فيه', 'identifier' => null],
                ['text' => 'سجدة أو دعوة صغيرة تقولها دلوقتي', 'identifier' => null],
            ]);
        }

        $dreams = RecoveryDream::ownedBy($user)->where('is_achieved', false)->latest()->limit(5)->get()
            ->map(fn (RecoveryDream $d) => ['text' => $d->title, 'key' => "dream:{$d->id}"]);

        $harvestItems = $this->applyOverrides(collect($dreams)->values(), $overrides);
        if ($harvestItems->isEmpty()) {
            $harvestItems = collect([
                ['text' => 'فخر بنفسك حتى لو محدش شافه', 'identifier' => null],
                ['text' => 'يوم إضافي في رصيدك، ما بيترجعش', 'identifier' => null],
                ['text' => 'ثقة بتكبر مع كل مرة تنتصر فيها', 'identifier' => null],
            ]);
        }

        $currentStreak = $active?->currentStreakDays() ?? 0;
        $bestStreak = (int) $recoveries->max(fn (Recovery $r) => $r->bestStreakDays());

        $footer = match (true) {
            $currentStreak > 0 && $bestStreak > 0 => "عندك {$currentStreak} يوم نضافة متواصل دلوقتي · أطول سلسلة ليك: {$bestStreak} يوم",
            $currentStreak > 0 => "عندك {$currentStreak} يوم نضافة متواصل دلوقتي",
            $bestStreak > 0 => "أطول سلسلة وصلتلها قبل كده: {$bestStreak} يوم",
            default => null,
        };

        return [$startItems, $harvestItems, $footer];
    }

    /**
     * The full, uncapped reveal of everything worth resisting for — every
     * unachieved dream with its benefits, plus what's already been proven
     * possible — shown as the victory finale at the end of طريق النجاة.
     */
    private function salvationFinale(User $user, Collection $overrides): array
    {
        $recoveries = Recovery::ownedBy($user)->get();
        $active = $recoveries->firstWhere('status', RecoveryStatus::Active);

        $dreams = RecoveryDream::ownedBy($user)->where('is_achieved', false)->latest()->get()
            ->map(fn (RecoveryDream $d) => [
                'text' => $d->title,
                'key' => "dream:{$d->id}",
                'icon' => $d->icon,
                'benefits' => (array) $d->benefits,
            ]);

        $items = $this->applyOverrides(collect($dreams)->values(), $overrides)->all();

        return [
            'items' => $items,
            'achievedCount' => RecoveryDream::ownedBy($user)->where('is_achieved', true)->count(),
            'currentStreak' => $active?->currentStreakDays() ?? 0,
            'bestStreak' => (int) $recoveries->max(fn (Recovery $r) => $r->bestStreakDays()),
            'cleanDays' => $recoveries->sum(fn (Recovery $r) => $r->cleanDaysCount()),
        ];
    }

    /**
     * Drops items the user hid, swaps in edited text where they overrode it,
     * and turns each surviving item's source key into a stable identifier
     * the view can send back to startEdit()/removeItem().
     */
    private function applyOverrides(Collection $items, Collection $overrides): Collection
    {
        return $items
            ->map(function (array $item) use ($overrides) {
                $override = $overrides->get($item['key']);

                if ($override) {
                    if ($override->hidden) {
                        return null;
                    }
                    if (filled($override->body)) {
                        $item['text'] = $override->body;
                    }
                }

                $item['identifier'] = "real:{$item['key']}";

                return $item;
            })
            ->filter()
            ->values();
    }

    /**
     * Lay out start-items → divider → harvest-items → destination as an
     * alternating zigzag path (Duolingo-style), returning node positions
     * on a 320-wide canvas and a smooth SVG path drawn through them.
     *
     * @return array{nodes: array, pathD: string, height: int}
     */
    private function buildRoadMap(RecoveryRoad $road, Collection $startItems, Collection $harvestItems): array
    {
        $nodes = [];
        $y = 30;
        $stepY = 110;
        $side = 1;

        $nodes[] = ['type' => 'you-are-here', 'x' => 160, 'y' => $y, 'text' => 'إنت هنا'];

        foreach ($startItems as $item) {
            $y += $stepY;
            $x = 160 + ($side * 90);
            $nodes[] = ['type' => 'item', 'x' => $x, 'y' => $y, 'side' => $side > 0 ? 'right' : 'left', 'text' => $item['text'], 'identifier' => $item['identifier']];
            $side *= -1;
        }

        $y += (int) ($stepY * 0.8);
        $nodes[] = ['type' => 'divider', 'x' => 160, 'y' => $y, 'text' => 'وبعدين...'];

        foreach ($harvestItems as $item) {
            $y += $stepY;
            $x = 160 + ($side * 90);
            $nodes[] = ['type' => 'item', 'x' => $x, 'y' => $y, 'side' => $side > 0 ? 'right' : 'left', 'text' => $item['text'], 'identifier' => $item['identifier']];
            $side *= -1;
        }

        $y += $stepY;
        $nodes[] = ['type' => 'destination', 'x' => 160, 'y' => $y, 'text' => $road->destinationLabel()];

        return [
            'nodes' => $nodes,
            'pathD' => $this->buildPathD($nodes),
            'height' => $y + 60,
        ];
    }
}
