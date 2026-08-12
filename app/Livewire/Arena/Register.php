<?php

namespace App\Livewire\Arena;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.arena')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /** Optional invite/join code carried from the invite link (used in a later phase). */
    #[Url]
    public ?string $code = null;

    public function register()
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], attributes: [
            'name' => 'الاسم', 'email' => 'البريد الإلكتروني', 'password' => 'كلمة المرور',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::Participant->value,
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);

        return redirect()->route('arena.index', $this->code ? ['code' => $this->code] : []);
    }

    public function render(): View
    {
        return view('livewire.arena.register');
    }
}
