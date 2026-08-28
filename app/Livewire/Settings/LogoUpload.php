<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Lets the app owner replace the nav-bar logo (public/icons/Logo.jpg,
 * the only place it's referenced — see navigation.blade.php). Whatever
 * format is uploaded gets re-encoded to a real JPEG at that fixed path,
 * so the existing asset() reference never has to change.
 */
class LogoUpload extends Component
{
    use WithFileUploads;

    public $logo = null;

    public function updateLogo(): void
    {
        abort_unless(Auth::user()->isOwner(), 403);

        $this->validate([
            'logo' => ['required', 'image', 'max:4096'],
        ], [
            'logo.required' => 'اختار صورة الأول',
            'logo.image' => 'لازم تكون صورة (jpg, png, webp, ...)',
            'logo.max' => 'الصورة كبيرة أوي، الحد الأقصى 4 ميجا',
        ]);

        $source = @imagecreatefromstring(file_get_contents($this->logo->getRealPath()));

        if ($source === false) {
            $this->addError('logo', 'مقدرتش أقرأ الصورة دي، جرّب صورة تانية');

            return;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Flatten onto white first — JPEG has no alpha channel, so a
        // transparent PNG would otherwise turn black.
        $flat = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($flat, 255, 255, 255);
        imagefill($flat, 0, 0, $white);
        imagecopy($flat, $source, 0, 0, 0, 0, $width, $height);

        imagejpeg($flat, public_path('icons/Logo.jpg'), 90);

        imagedestroy($source);
        imagedestroy($flat);

        $this->logo = null;
        $this->dispatch('logo-updated');
    }

    public function render(): View
    {
        $path = public_path('icons/Logo.jpg');
        $currentLogoUrl = asset('icons/Logo.jpg').(file_exists($path) ? '?v='.filemtime($path) : '');

        return view('livewire.settings.logo-upload', [
            'currentLogoUrl' => $currentLogoUrl,
        ]);
    }
}
