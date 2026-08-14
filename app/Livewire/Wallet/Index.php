<?php

namespace App\Livewire\Wallet;

use App\Enums\TransactionType;
use App\Livewire\Forms\TransactionForm;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public TransactionForm $form;

    public bool $open = false;

    /** List/breakdown scope: 'month' | 'all'. */
    #[Url]
    public string $scope = 'month';

    #[\Livewire\Attributes\On('wallet-updated')]
    public function refreshWallet(): void
    {
        // A wishlist purchase created a transaction — re-render to update totals.
    }

    public function openCreate(string $type = 'expense'): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate($type);
        $this->open = true;
    }

    public function edit(int $id): void
    {
        $tx = Transaction::ownedBy(Auth::user())->findOrFail($id);
        $this->resetValidation();
        $this->form->setTransaction($tx);
        $this->open = true;
    }

    public function delete(int $id): void
    {
        Transaction::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    public function save(): void
    {
        $this->form->persist(Auth::id());
        $this->open = false;
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        $user = Auth::user();

        // All-time totals
        $totalIncome = (float) Transaction::query()->ownedBy($user)->income()->sum('amount');
        $totalExpense = (float) Transaction::query()->ownedBy($user)->expenses()->sum('amount');
        $monthExpense = (float) Transaction::query()->ownedBy($user)->expenses()
            ->whereBetween('date', [Carbon::today()->startOfMonth()->toDateString(), Carbon::today()->endOfMonth()->toDateString()])
            ->sum('amount');

        // Scoped list
        $query = Transaction::query()->ownedBy($user);
        if ($this->scope === 'month') {
            $query->whereBetween('date', [Carbon::today()->startOfMonth()->toDateString(), Carbon::today()->endOfMonth()->toDateString()]);
        }
        $transactions = $query->orderByDesc('date')->latest()->get();

        // Expense breakdown by category (within scope)
        $breakdown = $transactions
            ->where('type', TransactionType::Expense)
            ->groupBy(fn (Transaction $t) => $t->category ?: 'غير مصنّف')
            ->map(fn ($group) => (float) $group->sum('amount'))
            ->sortDesc();

        return view('livewire.wallet.index', [
            'balance' => $totalIncome - $totalExpense,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'monthExpense' => $monthExpense,
            'transactions' => $transactions,
            'breakdown' => $breakdown,
        ]);
    }
}
