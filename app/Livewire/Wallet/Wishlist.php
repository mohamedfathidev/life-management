<?php

namespace App\Livewire\Wallet;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\WishlistItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * "ضروريات فقط" — a wishlist of things to buy with an importance level.
 * It never touches the balance unless an item is actually purchased, which
 * then creates a linked expense transaction.
 */
class Wishlist extends Component
{
    public bool $open = false;

    public ?int $editingId = null;
    public string $title = '';
    public ?float $estimated_price = null;
    public string $importance = 'medium';
    public ?string $note = null;

    public function openForm(): void
    {
        $this->resetForm();
        $this->open = true;
    }

    public function edit(int $id): void
    {
        $item = WishlistItem::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $item->id;
        $this->title = $item->title;
        $this->estimated_price = $item->estimated_price ? (float) $item->estimated_price : null;
        $this->importance = $item->importance;
        $this->note = $item->note;
        $this->open = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'estimated_price' => ['nullable', 'numeric', 'min:0'],
            'importance' => ['required', 'in:'.implode(',', array_keys(WishlistItem::IMPORTANCE))],
            'note' => ['nullable', 'string', 'max:1000'],
        ], attributes: ['title' => 'الحاجة', 'estimated_price' => 'السعر']);

        if ($this->editingId) {
            WishlistItem::ownedBy(Auth::user())->where('id', $this->editingId)->update($data);
        } else {
            WishlistItem::create($data + ['user_id' => Auth::id()]);
        }

        $this->open = false;
        $this->resetForm();
    }

    /** Mark an item as bought → create a linked expense (this is the only balance impact). */
    public function buy(int $id): void
    {
        $item = WishlistItem::ownedBy(Auth::user())->findOrFail($id);
        if ($item->is_bought) {
            return;
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'type' => TransactionType::Expense->value,
            'amount' => $item->estimated_price ?? 0,
            'category' => 'ضروريات',
            'note' => $item->title,
            'date' => Carbon::today()->toDateString(),
        ]);

        $item->update(['is_bought' => true, 'transaction_id' => $transaction->id]);

        $this->dispatch('wallet-updated');
    }

    /** Undo a purchase: delete the linked expense and re-list the item. */
    public function undoBuy(int $id): void
    {
        $item = WishlistItem::ownedBy(Auth::user())->findOrFail($id);
        $item->transaction?->delete();
        $item->update(['is_bought' => false, 'transaction_id' => null]);

        $this->dispatch('wallet-updated');
    }

    public function delete(int $id): void
    {
        WishlistItem::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'title', 'estimated_price', 'note');
        $this->importance = 'medium';
        $this->resetValidation();
    }

    public function render(): View
    {
        $user = Auth::user();

        $pending = WishlistItem::ownedBy($user)->where('is_bought', false)
            ->orderByRaw("FIELD(importance, 'critical', 'high', 'medium', 'low')")
            ->latest()->get();

        $bought = WishlistItem::ownedBy($user)->where('is_bought', true)->latest()->get();

        return view('livewire.wallet.wishlist', [
            'pending' => $pending,
            'bought' => $bought,
            'pendingTotal' => (float) $pending->sum('estimated_price'),
            'importanceLevels' => WishlistItem::IMPORTANCE,
        ]);
    }
}
