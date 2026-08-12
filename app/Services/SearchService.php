<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\ComfortExperience;
use App\Models\Cv;
use App\Models\DiaryEntry;
use App\Models\Donation;
use App\Models\Duaa;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\JobApplication;
use App\Models\MarketingPost;
use App\Models\Recovery;
use App\Models\RecoveryTopic;
use App\Models\Scholarship;
use App\Models\ScholarshipTopic;
use App\Models\StudyTrack;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VolunteerActivity;

/**
 * Cross-module search. Runs a LIKE query against the text columns of each
 * module and returns unified, linkable results. (Encrypted fields such as
 * diary/recovery content are not searchable and are matched by title only.)
 */
class SearchService
{
    /** @return array<int, array{section:string, emoji:string, title:string, subtitle:?string, url:string}> */
    public static function search(User $user, string $q, int $perType = 5): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 2) {
            return [];
        }

        $like = '%'.$q.'%';
        $results = [];

        $push = function (string $section, string $emoji, $items, callable $map) use (&$results): void {
            foreach ($items as $item) {
                $results[] = array_merge(['section' => $section, 'emoji' => $emoji], $map($item));
            }
        };

        // Goals
        $push('الأهداف', '🎯',
            Goal::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like))
                ->limit($perType)->get(),
            fn (Goal $g) => ['title' => $g->title, 'subtitle' => $g->category->label(), 'url' => route('goals.show', $g)]);

        // Tasks
        $push('التاسكات', '✅',
            Task::query()->where('user_id', $user->id)->where('title', 'like', $like)->with('day')->limit($perType)->get(),
            fn (Task $t) => [
                'title' => $t->title,
                'subtitle' => $t->day ? $t->day->date->translatedFormat('j M') : 'المؤجّلات',
                'url' => $t->day ? route('planner.day', $t->day->date->toDateString()) : route('planner.pool'),
            ]);

        // Appointments
        $push('المواعيد', '📅',
            Appointment::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('note', 'like', $like))
                ->limit($perType)->get(),
            fn (Appointment $a) => ['title' => $a->title, 'subtitle' => $a->date->translatedFormat('j M Y'), 'url' => route('appointments')]);

        // Challenges / habits / recovery
        $push('التحديات', '🔥',
            Challenge::query()->where('user_id', $user->id)->where('title', 'like', $like)->limit($perType)->get(),
            fn (Challenge $c) => ['title' => $c->title, 'subtitle' => $c->status->label(), 'url' => route('challenges.show', $c)]);
        $push('العادات', '🔁',
            Habit::query()->where('user_id', $user->id)->where('title', 'like', $like)->limit($perType)->get(),
            fn (Habit $h) => ['title' => $h->title, 'subtitle' => $h->type->label(), 'url' => route('habits.show', $h)]);
        $push('التعافي', '🌱',
            Recovery::query()->where('user_id', $user->id)->where('title', 'like', $like)->limit($perType)->get(),
            fn (Recovery $r) => ['title' => $r->title, 'subtitle' => null, 'url' => route('recovery.show', $r)]);
        $push('تعلّم عن التعافي', '📚',
            RecoveryTopic::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('content', 'like', $like))
                ->limit($perType)->get(),
            fn (RecoveryTopic $t) => ['title' => $t->title, 'subtitle' => null, 'url' => route('recovery.topics')]);

        // Diary (title only — content encrypted)
        $push('المذكرات', '📖',
            DiaryEntry::query()->where('user_id', $user->id)->where('title', 'like', $like)->limit($perType)->get(),
            fn (DiaryEntry $d) => ['title' => $d->title ?: $d->date->translatedFormat('j M Y'), 'subtitle' => $d->date->translatedFormat('j M Y'), 'url' => route('diary.index')]);

        // Comfort zone
        $push('خارج الزون', '🚀',
            ComfortExperience::query()->where('user_id', $user->id)->where('title', 'like', $like)->limit($perType)->get(),
            fn (ComfortExperience $e) => ['title' => $e->title, 'subtitle' => $e->status->label(), 'url' => route('comfort-zone')]);

        // Career
        $push('المنح', '🎓',
            Scholarship::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('institution', 'like', $like))
                ->limit($perType)->get(),
            fn (Scholarship $s) => ['title' => $s->name, 'subtitle' => $s->stage->label(), 'url' => route('scholarships.show', $s)]);
        $push('تعلّم عن المنح', '📝',
            ScholarshipTopic::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('content', 'like', $like))
                ->limit($perType)->get(),
            fn (ScholarshipTopic $t) => ['title' => $t->title, 'subtitle' => null, 'url' => route('scholarships.topics')]);
        $push('التطوّع', '🤝',
            VolunteerActivity::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('organization', 'like', $like))
                ->limit($perType)->get(),
            fn (VolunteerActivity $v) => ['title' => $v->title, 'subtitle' => $v->organization, 'url' => route('scholarships.volunteering')]);
        $push('الوظائف', '💻',
            JobApplication::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('position', 'like', $like)->orWhere('company', 'like', $like))
                ->limit($perType)->get(),
            fn (JobApplication $j) => ['title' => $j->position, 'subtitle' => $j->company, 'url' => route('jobs.show', $j)]);
        $push('مذاكرة السوق', '📗',
            StudyTrack::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('field', 'like', $like))
                ->limit($perType)->get(),
            fn (StudyTrack $s) => ['title' => $s->title, 'subtitle' => $s->field, 'url' => route('market-study.show', $s)]);
        $push('التسويق', '📣',
            MarketingPost::query()->where('user_id', $user->id)->where('topic', 'like', $like)->limit($perType)->get(),
            fn (MarketingPost $p) => ['title' => $p->topic, 'subtitle' => $p->status->label(), 'url' => route('marketing.index')]);
        $push('CVs', '📄',
            Cv::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('target', 'like', $like))
                ->limit($perType)->get(),
            fn (Cv $c) => ['title' => $c->title, 'subtitle' => $c->target, 'url' => route('cvs.show', $c)]);

        // Religion & finance
        $push('الأدعية', '📿',
            Duaa::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('content', 'like', $like))
                ->limit($perType)->get(),
            fn (Duaa $d) => ['title' => $d->title, 'subtitle' => null, 'url' => route('religion.duaas')]);
        $push('الصدقات', '🤲',
            Donation::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('cause', 'like', $like)->orWhere('note', 'like', $like))
                ->limit($perType)->get(),
            fn (Donation $d) => ['title' => $d->cause ?: 'صدقة', 'subtitle' => number_format($d->amount, 2), 'url' => route('religion.donations')]);
        $push('المحفظة', '💰',
            Transaction::query()->where('user_id', $user->id)
                ->where(fn ($w) => $w->where('category', 'like', $like)->orWhere('note', 'like', $like))
                ->limit($perType)->get(),
            fn (Transaction $t) => ['title' => $t->category ?: $t->type->label(), 'subtitle' => number_format($t->amount, 2), 'url' => route('wallet')]);

        return $results;
    }
}
