<?php

namespace App\Services;

use App\Models\TelegramPost;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Pulls recent posts from a PUBLIC Telegram channel via its unauthenticated
 * preview page (t.me/s/{channel}) — no bot token or login needed, but only
 * the ~20 most recent posts are visible there (not the full channel history).
 */
class TelegramChannelSyncService
{
    /** Curated recovery-themed channels, username => display name. */
    public const CHANNELS = [
        'G_Y_17' => 'خطر العادة السرية',
        'G_Y_19' => 'خطر الإباحية',
    ];

    private readonly string $channel;

    public function __construct(private readonly User $user, string $channel)
    {
        if (! array_key_exists($channel, self::CHANNELS)) {
            throw new \InvalidArgumentException("Unknown Telegram channel: {$channel}");
        }

        $this->channel = $channel;
    }

    /** Fetch the channel's preview page and store any posts not already saved. Returns how many were new. */
    public function sync(): int
    {
        $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->timeout(15)
            ->get('https://t.me/s/'.$this->channel);

        if (! $response->successful()) {
            throw new \RuntimeException('تعذّر الوصول لقناة تيليجرام دلوقتي.');
        }

        $created = 0;

        foreach ($this->parse($response->body()) as $post) {
            $log = TelegramPost::updateOrCreate(
                ['user_id' => $this->user->id, 'channel' => $this->channel, 'message_id' => $post['message_id']],
                $post,
            );

            if ($log->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Parse the preview page's HTML into post rows. Public (not private) so it
     * can be unit-tested against a saved HTML fixture without hitting the network.
     *
     * @return array<int, array{message_id:int, content:?string, image_url:?string, video_url:?string, post_url:string, posted_at:?Carbon}>
     */
    public function parse(string $html): array
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_use_internal_errors(false);

        $xpath = new DOMXPath($dom);
        $posts = [];

        foreach ($xpath->query('//div[@data-post]') as $node) {
            /** @var DOMElement $node */
            [$channel, $id] = explode('/', (string) $node->getAttribute('data-post')) + [null, null];

            if ($channel !== $this->channel || ! $id) {
                continue;
            }

            $textNode = $this->firstByClass($xpath, $node, 'tgme_widget_message_text');
            $dateNode = $this->firstByClass($xpath, $node, 'tgme_widget_message_date');
            $photoNode = $this->firstByClass($xpath, $node, 'tgme_widget_message_photo_wrap');
            $videoNode = $this->firstByClass($xpath, $node, 'js-message_video')
                ?? $this->firstByClass($xpath, $node, 'tgme_widget_message_video');

            $content = $textNode ? trim($this->innerHtml($textNode)) : null;
            $imageUrl = $photoNode ? $this->backgroundImageUrl((string) $photoNode->getAttribute('style')) : null;
            $videoUrl = $videoNode?->getAttribute('src') ?: null;
            $postUrl = $dateNode?->getAttribute('href') ?: "https://t.me/{$channel}/{$id}";

            $time = $dateNode ? $xpath->query('.//time[@datetime]', $dateNode)->item(0) : null;
            $postedAt = $time ? Carbon::parse($time->getAttribute('datetime')) : null;

            if ($content === null && $imageUrl === null && $videoUrl === null) {
                continue; // service message or unsupported media we can't render
            }

            $posts[] = [
                'message_id' => (int) $id,
                'content' => $content ?: null,
                'image_url' => $imageUrl,
                'video_url' => $videoUrl,
                'post_url' => $postUrl,
                'posted_at' => $postedAt,
            ];
        }

        return $posts;
    }

    private function firstByClass(DOMXPath $xpath, DOMElement $context, string $class): ?DOMElement
    {
        $node = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]", $context)->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    private function innerHtml(DOMElement $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    private function backgroundImageUrl(string $style): ?string
    {
        return preg_match("/background-image:\s*url\('([^']+)'\)/", $style, $m) ? $m[1] : null;
    }
}
