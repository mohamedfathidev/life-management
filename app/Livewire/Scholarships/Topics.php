<?php

namespace App\Livewire\Scholarships;

use App\Models\ScholarshipTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Topics extends Component
{
    #[Url]
    public string $tag = '';

    #[On('scholarship-topic-saved')]
    public function refreshList(): void
    {
        //
    }

    /** Ready-made learning topics, each with a starter plan. */
    private const SUGGESTED = [
        ['title' => 'خطاب الدوافع (SOP / Motivation Letter)', 'plan' => ['افهم مكوّنات الخطاب', 'اكتب مسودة أولى', 'خصّصها لكل منحة', 'راجعها مع حد متمكّن']],
        ['title' => 'الاستعداد لامتحان IELTS', 'plan' => ['حدّد مستواك (امتحان تجريبي)', 'خطة مذاكرة أسبوعية', 'تدرّب على الـ4 مهارات', 'امتحانات محاكية']],
        ['title' => 'خطابات التوصية', 'plan' => ['اختر المُوصِّين', 'جهّزلهم ملخص إنجازاتك', 'اطلب مبكرًا', 'تابع قبل الموعد']],
        ['title' => 'البحث عن المنح المناسبة', 'plan' => ['حدّد مجالك والدول', 'اعمل قائمة منح', 'قارن الشروط والمواعيد']],
        ['title' => 'السيرة الذاتية الأكاديمية (CV)', 'plan' => ['اجمع إنجازاتك', 'رتّبها بشكل أكاديمي', 'خصّصها للمنحة']],
    ];

    public function addSuggested(): void
    {
        $existing = ScholarshipTopic::ownedBy(Auth::user())->pluck('title')->all();

        foreach (self::SUGGESTED as $s) {
            if (in_array($s['title'], $existing, true)) {
                continue;
            }

            $topic = ScholarshipTopic::create(['user_id' => Auth::id(), 'title' => $s['title'], 'tags' => []]);

            $position = 0;
            foreach ($s['plan'] as $step) {
                $topic->documents()->create(['user_id' => Auth::id(), 'name' => $step, 'position' => ++$position]);
            }
        }
    }

    public function render(): View
    {
        $topics = ScholarshipTopic::query()
            ->ownedBy(Auth::user())
            ->when($this->tag !== '', fn ($q) => $q->withTag($this->tag))
            ->latest()
            ->get();

        $allTags = ScholarshipTopic::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.scholarships.topics', [
            'topics' => $topics,
            'allTags' => $allTags,
        ]);
    }
}
