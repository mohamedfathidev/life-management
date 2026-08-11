<?php

namespace App\Livewire\Forms;

use App\Enums\MarketingStatus;
use App\Models\MarketingPost;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class MarketingPostForm extends Form
{
    public ?MarketingPost $post = null;

    public string $platform = 'LinkedIn';
    public string $topic = '';
    public ?string $content = null;
    public string $status = 'idea';
    public ?string $scheduled_for = null;
    public ?string $link = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'platform' => ['required', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:20000'],
            'status' => ['required', new Enum(MarketingStatus::class)],
            'scheduled_for' => ['nullable', 'date'],
            'link' => ['nullable', 'url', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'platform' => 'المنصّة',
            'topic' => 'الموضوع',
            'status' => 'الحالة',
            'scheduled_for' => 'موعد النشر',
            'link' => 'الرابط',
        ];
    }

    public function setPost(MarketingPost $post): void
    {
        $this->post = $post;
        $this->platform = $post->platform;
        $this->topic = $post->topic;
        $this->content = $post->content;
        $this->status = $post->status->value;
        $this->scheduled_for = $post->scheduled_for?->toDateString();
        $this->link = $post->link;
    }

    public function persist(int $userId): MarketingPost
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->post) {
            $this->post->update($data);

            return $this->post;
        }

        return MarketingPost::create($data);
    }
}
