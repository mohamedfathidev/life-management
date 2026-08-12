<?php

namespace App\Livewire\Forms;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class TransactionForm extends Form
{
    public ?Transaction $transaction = null;

    public string $type = 'expense';
    public ?float $amount = null;
    public ?string $category = null;
    public ?string $note = null;
    public string $date = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(TransactionType::class)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'date' => ['required', 'date'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['amount' => 'المبلغ', 'category' => 'البند', 'date' => 'التاريخ'];
    }

    public function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
        $this->type = $transaction->type->value;
        $this->amount = (float) $transaction->amount;
        $this->category = $transaction->category;
        $this->note = $transaction->note;
        $this->date = $transaction->date->toDateString();
    }

    public function prepareForCreate(string $type = 'expense'): void
    {
        $this->reset();
        $this->type = $type;
        $this->date = Carbon::today()->toDateString();
    }

    public function persist(int $userId): Transaction
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->transaction) {
            $this->transaction->update($data);

            return $this->transaction;
        }

        return Transaction::create($data);
    }
}
