<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class MotivationalQuote extends Component
{
    public array $quote = [];
    public bool $addMode = false;
    public string $newQuoteText = '';
    public ?string $newQuoteAuthor = null;

    public function mount(): void
    {
        $this->loadRandomQuote();
    }

    public function loadRandomQuote(): void
    {
        $path = public_path('files/motivational-quotes.json');

        if (! is_file($path)) {
            $this->quote = [];
            return;
        }

        $data = json_decode((string) file_get_contents($path), true);
        $quotes = $data['quotes'] ?? [];

        if (! is_array($quotes) || $quotes === []) {
            $this->quote = [];
            return;
        }

        // Get random quote
        $this->quote = $quotes[array_rand($quotes)];
    }

    public function refreshQuote(): void
    {
        $this->loadRandomQuote();
    }

    public function openAddMode(): void
    {
        $this->addMode = true;
        $this->newQuoteText = '';
        $this->newQuoteAuthor = null;
    }

    public function closeAddMode(): void
    {
        $this->addMode = false;
        $this->reset(['newQuoteText', 'newQuoteAuthor']);
        $this->resetValidation();
    }

    public function addQuote(): void
    {
        $this->validate([
            'newQuoteText' => ['required', 'string', 'min:10', 'max:500'],
            'newQuoteAuthor' => ['nullable', 'string', 'max:100'],
        ], [
            'newQuoteText.required' => 'الجملة مطلوبة',
            'newQuoteText.min' => 'الجملة قصيرة جداً',
            'newQuoteText.max' => 'الجملة طويلة جداً',
            'newQuoteAuthor.max' => 'اسم المؤلف طويل جداً',
        ]);

        $path = public_path('files/motivational-quotes.json');
        
        $data = json_decode((string) file_get_contents($path), true);
        $quotes = $data['quotes'] ?? [];

        // Add new quote
        $quotes[] = [
            'text' => $this->newQuoteText,
            'author' => $this->newQuoteAuthor ?: null,
        ];

        // Save back to file
        file_put_contents($path, json_encode(['quotes' => $quotes], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Refresh quote to show the new one
        $this->quote = [
            'text' => $this->newQuoteText,
            'author' => $this->newQuoteAuthor,
        ];

        $this->closeAddMode();

        $this->dispatch('quote-added');
    }

    public function render(): View
    {
        return view('livewire.dashboard.motivational-quote');
    }
}
