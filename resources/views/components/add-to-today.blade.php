@props([
    'kind' => 'other',
    'title' => '',
    'goalId' => null,
    'label' => 'أضف تاسك النهاردة',
])

{{--
    Reusable launcher: collects a task straight into today's plan from any module.
    Dispatches the global `create-task` event handled by the app-layout task modal.
--}}
<button type="button" x-data
    @click="Livewire.dispatch('create-task', @js(['today' => true, 'kind' => $kind, 'title' => (string) $title, 'goalId' => $goalId ? (int) $goalId : null]))"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark text-sm font-medium hover:bg-primary/20 dark:hover:bg-primary-dark/30 transition']) }}>
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    <span>{{ $label }}</span>
</button>
