<?php

namespace Tests\Feature;

use App\Livewire\Privacy\Unlock;
use App\Livewire\Settings\PrivacyPin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PrivacyLockTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPin(string $pin = '1234'): User
    {
        $user = User::factory()->create();
        $user->pin = $pin; // hashed via cast
        $user->save();

        return $user;
    }

    public function test_recovery_is_open_when_no_pin_is_set(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('recovery.index'))->assertOk();
    }

    public function test_recovery_redirects_to_unlock_when_pin_set_and_locked(): void
    {
        $user = $this->userWithPin();

        $this->actingAs($user)
            ->get(route('recovery.index'))
            ->assertRedirect(route('privacy.unlock'));
    }

    public function test_correct_pin_unlocks_the_session(): void
    {
        $user = $this->userWithPin('1234');

        Livewire::actingAs($user)
            ->test(Unlock::class)
            ->set('pin', '1234')
            ->call('unlock')
            ->assertHasNoErrors();

        $this->assertTrue((bool) session('privacy_unlocked'));

        // now the gate lets the request through
        $this->actingAs($user)->get(route('recovery.index'))->assertOk();
    }

    public function test_wrong_pin_does_not_unlock(): void
    {
        $user = $this->userWithPin('1234');

        Livewire::actingAs($user)
            ->test(Unlock::class)
            ->set('pin', '0000')
            ->call('unlock')
            ->assertHasErrors('pin');

        $this->assertNull(session('privacy_unlocked'));
    }

    public function test_user_can_set_a_pin_from_settings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PrivacyPin::class)
            ->set('pin', '4567')
            ->set('pin_confirmation', '4567')
            ->call('setPin')
            ->assertHasNoErrors()
            ->assertDispatched('pin-updated');

        $user->refresh();
        $this->assertNotNull($user->pin);
        $this->assertTrue(Hash::check('4567', $user->pin));
        $this->assertTrue((bool) session('privacy_unlocked'));
    }

    public function test_changing_pin_requires_the_current_pin(): void
    {
        $user = $this->userWithPin('1234');

        Livewire::actingAs($user)
            ->test(PrivacyPin::class)
            ->set('current_pin', '9999') // wrong
            ->set('pin', '5678')
            ->set('pin_confirmation', '5678')
            ->call('setPin')
            ->assertHasErrors('current_pin');
    }
}
