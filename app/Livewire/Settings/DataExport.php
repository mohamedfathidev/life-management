<?php

namespace App\Livewire\Settings;

use App\Services\DataExportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DataExport extends Component
{
    public function export()
    {
        $json = DataExportService::for(Auth::user())->toJson();
        $filename = 'sebha-3ala-allah-backup-'.Carbon::now()->format('Y-m-d').'.json';

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public function render(): View
    {
        return view('livewire.settings.data-export');
    }
}
