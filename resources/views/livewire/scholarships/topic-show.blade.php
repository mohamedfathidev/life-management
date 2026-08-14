<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('scholarships.topics') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعلّم عن المنح والاستعداد</a>

        {{-- Topic header + content --}}
        <div class="mt-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="flex items-start justify-between gap-3">
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📘 {{ $topic->title }}</h1>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" wire:click="editTopic" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                    <button type="button" wire:click="delete" wire:confirm="حذف الموضوع وخطته؟" class="text-xs text-danger hover:underline">حذف</button>
                </div>
            </div>
            @if (! empty($topic->tags))
                <div class="flex flex-wrap gap-1.5 mt-3">
                    @foreach ($topic->tags as $t)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark">#{{ $t }}</span>
                    @endforeach
                </div>
            @endif
            @if ($topic->content)
                <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-4">{!! $topic->content !!}</div>
            @endif
        </div>

        {{-- Learning plan (checklist) --}}
        <div class="mt-6">
            <livewire:career.item-documents
                documentable-type="topic"
                :documentable-id="$topic->id"
                heading="📋 خطة التعلّم"
                placeholder="أضف خطوة في الخطة…"
                :show-library="false"
                :wire:key="'topicplan-'.$topic->id" />
        </div>
    </div>

    <livewire:scholarships.manage-topic />
</div>
