<?php

namespace App\Livewire\Cvs;

use App\Models\Cv;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Cv $cv;

    public function mount(Cv $cv): void
    {
        $this->authorize('view', $cv);
        $this->cv = $cv;
    }

    public function delete()
    {
        $this->authorize('delete', $this->cv);

        \Illuminate\Support\Facades\Storage::disk('local')->delete($this->cv->file_path);
        $this->cv->delete();

        return $this->redirectRoute('cvs.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.cvs.show');
    }
}
