<?php

namespace App\Livewire\Career;

use App\Models\CareerLesson;
use App\Models\CareerPitfallMark;
use App\Support\CareerPitfalls;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Pitfalls extends Component
{
    /** Which section is shown: 'curated' (general pitfalls) | 'mine' (my lessons). */
    #[Url]
    public string $tab = 'curated';

    #[Validate('required|string|max:255')]
    public string $newTitle = '';

    #[Validate('nullable|string|max:5000')]
    public ?string $newBody = null;

    #[Validate('required|in:general,ai')]
    public string $newCategory = 'general';

    /** Flag / unflag a curated pitfall as "applies to me". */
    public function toggleMark(string $key): void
    {
        $existing = CareerPitfallMark::ownedBy(Auth::user())->where('pitfall_key', $key)->first();

        if ($existing) {
            $existing->delete();
        } else {
            CareerPitfallMark::create(['user_id' => Auth::id(), 'pitfall_key' => $key]);
        }
    }

    public function addLesson(): void
    {
        $this->validate();

        CareerLesson::create([
            'user_id' => Auth::id(),
            'title' => $this->newTitle,
            'body' => $this->newBody,
            'category' => $this->newCategory,
        ]);

        $this->reset('newTitle', 'newBody');
    }

    public function toggleAvoided(int $id): void
    {
        $lesson = CareerLesson::ownedBy(Auth::user())->findOrFail($id);
        $lesson->update(['avoided' => ! $lesson->avoided]);
    }

    public function deleteLesson(int $id): void
    {
        CareerLesson::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    public function render(): View
    {
        return view('livewire.career.pitfalls', [
            'sections' => CareerPitfalls::sections(),
            'markedKeys' => CareerPitfallMark::ownedBy(Auth::user())->pluck('pitfall_key')->all(),
            'lessons' => CareerLesson::ownedBy(Auth::user())->latest()->get(),
        ]);
    }
}
