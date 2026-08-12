<?php

namespace App\Livewire\Career;

use App\Models\Activity;
use App\Models\JobApplication;
use App\Models\Scholarship;
use App\Models\VolunteerActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "انترفيوز" — every career item currently in the interview stage, in one place,
 * each with a "what to focus on / prepare for" note you can edit inline.
 */
#[Layout('layouts.app')]
class Interviews extends Component
{
    /** @var array<string, class-string<Model>> */
    private const MODELS = [
        'activity' => Activity::class,
        'volunteer' => VolunteerActivity::class,
        'scholarship' => Scholarship::class,
        'job' => JobApplication::class,
    ];

    /** Focus notes keyed by "{type}_{id}", bound to the textareas. */
    public array $focus = [];

    public function mount(): void
    {
        foreach ($this->items() as $item) {
            $this->focus[$item['type'].'_'.$item['id']] = $item['focus'];
        }
    }

    public function saveFocus(string $type, int $id): void
    {
        $model = self::MODELS[$type] ?? null;
        if (! $model) {
            return;
        }

        $record = $model::query()->where('user_id', Auth::id())->find($id);
        if (! $record) {
            return;
        }

        $record->update(['interview_focus' => $this->focus[$type.'_'.$id] ?? null]);

        $this->dispatch('interview-focus-saved');
    }

    /** @return array<int, array<string, mixed>> */
    private function items(): array
    {
        $user = Auth::user();
        $items = [];

        foreach (Activity::query()->ownedBy($user)->where('stage', 'interview')->get() as $a) {
            $items[] = ['type' => 'activity', 'id' => $a->id, 'emoji' => $a->type->emoji(), 'source' => 'نشاط', 'title' => $a->title, 'sub' => $a->organizer, 'date' => $a->start_date, 'focus' => $a->interview_focus, 'url' => route('activities.show', $a)];
        }
        foreach (VolunteerActivity::query()->ownedBy($user)->where('stage', 'interview')->get() as $v) {
            $items[] = ['type' => 'volunteer', 'id' => $v->id, 'emoji' => '🤝', 'source' => 'تطوّع', 'title' => $v->title, 'sub' => $v->organization, 'date' => $v->start_date, 'focus' => $v->interview_focus, 'url' => route('scholarships.volunteering')];
        }
        foreach (Scholarship::query()->ownedBy($user)->where('stage', 'interview')->get() as $s) {
            $items[] = ['type' => 'scholarship', 'id' => $s->id, 'emoji' => '🎓', 'source' => 'منحة', 'title' => $s->name, 'sub' => $s->institution, 'date' => $s->apply_to, 'focus' => $s->interview_focus, 'url' => route('scholarships.show', $s)];
        }
        foreach (JobApplication::query()->where('user_id', $user->id)->where('stage', 'interview')->get() as $j) {
            $items[] = ['type' => 'job', 'id' => $j->id, 'emoji' => '💼', 'source' => 'وظيفة', 'title' => $j->position, 'sub' => $j->company, 'date' => $j->interview_at, 'focus' => $j->interview_focus, 'url' => route('jobs.show', $j)];
        }

        return $items;
    }

    public function render(): View
    {
        return view('livewire.career.interviews', [
            'items' => $this->items(),
        ]);
    }
}
