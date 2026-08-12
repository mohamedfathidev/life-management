<?php

namespace App\Livewire\Search;

use App\Services\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $q = '';

    public function render(): View
    {
        $results = SearchService::search(Auth::user(), $this->q);
        $grouped = collect($results)->groupBy('section');

        return view('livewire.search.index', [
            'grouped' => $grouped,
            'count' => count($results),
        ]);
    }
}
