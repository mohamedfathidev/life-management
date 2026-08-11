<?php

namespace App\Livewire\Marketing;

use App\Enums\MarketingStatus;
use App\Livewire\Forms\MarketingPostForm;
use App\Models\MarketingPost;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManagePost extends Component
{
    public MarketingPostForm $form;

    public bool $open = false;

    #[On('create-post')]
    public function openForCreate(?string $status = null): void
    {
        $this->form->reset();
        $this->resetValidation();
        if ($status) {
            $this->form->status = $status;
        }
        $this->open = true;
    }

    #[On('edit-post')]
    public function openForEdit(MarketingPost $post): void
    {
        $this->authorize('update', $post);
        $this->resetValidation();
        $this->form->setPost($post);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->post) {
            $this->authorize('update', $this->form->post);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('post-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.marketing.manage-post', [
            'statuses' => MarketingStatus::cases(),
        ]);
    }
}
