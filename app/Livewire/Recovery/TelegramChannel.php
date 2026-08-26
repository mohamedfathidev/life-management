<?php

namespace App\Livewire\Recovery;

use App\Models\TelegramPost;
use App\Services\TelegramChannelSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Read-only feed of curated recovery-themed Telegram channels, pulled in on
 * demand (button click) rather than synced automatically. One tab per
 * channel — TelegramChannelSyncService::CHANNELS is the source of truth.
 */
#[Layout('layouts.app')]
class TelegramChannel extends Component
{
    use WithPagination;

    #[Url]
    public string $channel = 'G_Y_17';

    public ?string $syncMessage = null;

    public bool $syncFailed = false;

    public function mount(): void
    {
        if (! array_key_exists($this->channel, TelegramChannelSyncService::CHANNELS)) {
            $this->channel = array_key_first(TelegramChannelSyncService::CHANNELS);
        }
    }

    public function switchChannel(string $channel): void
    {
        if (! array_key_exists($channel, TelegramChannelSyncService::CHANNELS)) {
            return;
        }

        $this->channel = $channel;
        $this->syncMessage = null;
        $this->resetPage();
    }

    public function sync(): void
    {
        try {
            $added = (new TelegramChannelSyncService(Auth::user(), $this->channel))->sync();
            $this->syncFailed = false;
            $this->syncMessage = $added > 0 ? "تم جلب {$added} منشور جديد ✓" : 'مفيش جديد دلوقتي.';
        } catch (\Throwable) {
            $this->syncFailed = true;
            $this->syncMessage = 'تعذّر الوصول للقناة دلوقتي، جرّب تاني بعد شوية.';
        }
    }

    public function render(): View
    {
        return view('livewire.recovery.telegram-channel', [
            'channels' => TelegramChannelSyncService::CHANNELS,
            'posts' => TelegramPost::ownedBy(Auth::user())
                ->where('channel', $this->channel)
                ->orderByDesc('posted_at')
                ->paginate(15),
        ]);
    }
}
