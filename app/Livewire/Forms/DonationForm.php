<?php

namespace App\Livewire\Forms;

use App\Models\Donation;
use Illuminate\Support\Carbon;
use Livewire\Form;

class DonationForm extends Form
{
    public ?Donation $donation = null;

    public string $date = '';
    public ?float $amount = null;
    public ?string $cause = null;
    public ?string $note = null;
    public bool $is_recurring = false;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'cause' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'is_recurring' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['date' => 'التاريخ', 'amount' => 'المبلغ', 'cause' => 'الجهة/السبب'];
    }

    public function setDonation(Donation $donation): void
    {
        $this->donation = $donation;
        $this->date = $donation->date->toDateString();
        $this->amount = (float) $donation->amount;
        $this->cause = $donation->cause;
        $this->note = $donation->note;
        $this->is_recurring = $donation->is_recurring;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->date = Carbon::today()->toDateString();
    }

    public function persist(int $userId): Donation
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->donation) {
            $this->donation->update($data);

            return $this->donation;
        }

        return Donation::create($data);
    }
}
