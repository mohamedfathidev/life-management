<?php

namespace App\Livewire\Arena;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.arena')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    #[Url]
    public ?string $code = null;

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], attributes: ['email' => 'البريد الإلكتروني', 'password' => 'كلمة المرور']);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        request()->session()->regenerate();

        return redirect()->intended(route('arena.index'));
    }

    public function render(): View
    {
        return view('livewire.arena.login');
    }
}
