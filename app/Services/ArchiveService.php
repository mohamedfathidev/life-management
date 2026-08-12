<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\Donation;
use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\MindSession;
use App\Models\QuranLog;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * A unified, read-only history of the whole app (excluding the PIN-protected
 * Diary & Recovery). Each record is normalized to {date, type, title, url}.
 */
class ArchiveService
{
    public function __construct(private readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    /** @return array<string, array{label:string, emoji:string}> */
    public static function types(): array
    {
        return [
            'task' => ['label' => 'تاسك', 'emoji' => '🗂️'],
            'habit' => ['label' => 'عادة', 'emoji' => '🔁'],
            'challenge' => ['label' => 'تحدٍّ', 'emoji' => '🔥'],
            'focus' => ['label' => 'تركيز', 'emoji' => '🎯'],
            'mind' => ['label' => 'تنضيف العقل', 'emoji' => '🧠'],
            'donation' => ['label' => 'صدقة', 'emoji' => '🤲'],
            'transaction' => ['label' => 'محفظة', 'emoji' => '💰'],
            'quran' => ['label' => 'قرآن', 'emoji' => '📖'],
            'appointment' => ['label' => 'موعد', 'emoji' => '📅'],
        ];
    }

    /**
     * @return Collection<int, array{date:\Illuminate\Support\Carbon, type:string, emoji:string, typeLabel:string, title:string, url:?string}>
     */
    public function records(string $type = '', string $search = '', ?string $from = null, ?string $to = null): Collection
    {
        $sources = $type !== '' && isset(self::types()[$type]) ? [$type] : array_keys(self::types());

        $records = collect();
        foreach ($sources as $source) {
            $records = $records->concat($this->{$source.'Records'}());
        }

        if ($from) {
            $records = $records->filter(fn ($r) => $r['date']->toDateString() >= $from);
        }

        if ($to) {
            $records = $records->filter(fn ($r) => $r['date']->toDateString() <= $to);
        }

        if ($search !== '') {
            $needle = mb_strtolower(trim($search));
            $records = $records->filter(
                fn ($r) => str_contains(mb_strtolower($r['title']), $needle)
                    || str_contains(mb_strtolower($r['typeLabel']), $needle)
            );
        }

        return $records->sortByDesc(fn ($r) => $r['date']->timestamp)->values();
    }

    private function meta(string $type): array
    {
        return self::types()[$type];
    }

    /** @return Collection<int, array> */
    private function taskRecords(): Collection
    {
        $m = $this->meta('task');

        return Task::query()->ownedBy($this->user)->with('day:id,date')->get()->map(fn (Task $t) => [
            'date' => $t->day?->date ?? $t->created_at,
            'type' => 'task', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => $t->title.' — '.$t->status->label().' ('.$t->progress.'%)',
            'url' => route('tasks.show', $t),
        ]);
    }

    private function habitRecords(): Collection
    {
        $m = $this->meta('habit');

        return Habit::query()->ownedBy($this->user)->with('logs')->get()
            ->flatMap(fn (Habit $h) => $h->logs->map(fn ($log) => [
                'date' => $log->date,
                'type' => 'habit', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
                'title' => 'علّمت عادة: '.$h->title,
                'url' => route('habits.show', $h),
            ]));
    }

    private function challengeRecords(): Collection
    {
        $m = $this->meta('challenge');

        return Challenge::query()->where('user_id', $this->user->id)->with('logs')->get()
            ->flatMap(fn (Challenge $c) => $c->logs->map(fn ($log) => [
                'date' => $log->date,
                'type' => 'challenge', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
                'title' => 'تحدٍّ: '.$c->title,
                'url' => route('challenges.show', $c),
            ]));
    }

    private function focusRecords(): Collection
    {
        $m = $this->meta('focus');

        return FocusSession::query()->where('user_id', $this->user->id)->with('focusable')->get()->map(function (FocusSession $f) use ($m) {
            $on = $f->focusable?->title ? ' على: '.$f->focusable->title : '';

            return [
                'date' => $f->date,
                'type' => 'focus', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
                'title' => 'ركّزت '.(int) round($f->seconds / 60).' د'.$on,
                'url' => route('focus'),
            ];
        });
    }

    private function mindRecords(): Collection
    {
        $m = $this->meta('mind');

        return MindSession::query()->ownedBy($this->user)->get()->map(fn (MindSession $s) => [
            'date' => $s->date,
            'type' => 'mind', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => $s->game.' — '.$s->minutes.' د',
            'url' => route('mind'),
        ]);
    }

    private function donationRecords(): Collection
    {
        $m = $this->meta('donation');

        return Donation::query()->ownedBy($this->user)->get()->map(fn (Donation $d) => [
            'date' => $d->date,
            'type' => 'donation', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => 'صدقة: '.number_format((float) $d->amount, 2).($d->cause ? ' — '.$d->cause : ''),
            'url' => route('religion.donations'),
        ]);
    }

    private function transactionRecords(): Collection
    {
        $m = $this->meta('transaction');

        return Transaction::query()->ownedBy($this->user)->get()->map(fn (Transaction $t) => [
            'date' => $t->date,
            'type' => 'transaction', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => $t->type->label().': '.number_format((float) $t->amount, 2).($t->category ? ' — '.$t->category : ''),
            'url' => route('wallet'),
        ]);
    }

    private function quranRecords(): Collection
    {
        $m = $this->meta('quran');

        return QuranLog::query()->ownedBy($this->user)->get()->map(fn (QuranLog $q) => [
            'date' => $q->date,
            'type' => 'quran', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => 'ورد قرآن'.($q->pages ? ' — '.$q->pages.' صفحة' : '').($q->from_surah ? ' (من '.$q->from_surah.')' : ''),
            'url' => route('religion.quran'),
        ]);
    }

    private function appointmentRecords(): Collection
    {
        $m = $this->meta('appointment');

        return Appointment::query()->where('user_id', $this->user->id)->get()->map(fn (Appointment $a) => [
            'date' => $a->date,
            'type' => 'appointment', 'emoji' => $m['emoji'], 'typeLabel' => $m['label'],
            'title' => 'موعد: '.$a->title,
            'url' => route('appointments'),
        ]);
    }
}
