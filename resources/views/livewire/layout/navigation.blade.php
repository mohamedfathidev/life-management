<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <img src="{{ asset('icons/Logo.jpg') }}" alt="سيبها على الله" class="block h-9 w-9 rounded-lg object-cover" />
                        <span class="font-bold text-primary dark:text-primary-dark hidden sm:inline">سيبها على الله</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:gap-6 sm:items-stretch">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        لوحة التحكم
                    </x-nav-link>
                    <x-nav-link :href="route('simply')" :active="request()->routeIs('simply')" wire:navigate>
                        🌿 ببساطة
                    </x-nav-link>

                    {{-- Planning group --}}
                    <x-nav-dropdown label="التخطيط"
                        :active="request()->routeIs('dreams.*') || request()->routeIs('goals.*') || request()->routeIs('planner*') || request()->routeIs('tasks.*') || request()->routeIs('appointments')">
                        <x-dropdown-link :href="route('dreams.index')" wire:navigate>✨ الأحلام</x-dropdown-link>
                        <x-dropdown-link :href="route('goals.index')" wire:navigate>🎯 الأهداف</x-dropdown-link>
                        <x-dropdown-link :href="route('tasks.index')" wire:navigate>🗂️ كل التاسكات</x-dropdown-link>
                        <x-dropdown-link :href="route('planner')" wire:navigate>🗓️ المخطط</x-dropdown-link>
                        <x-dropdown-link :href="route('planner.day-overview')" wire:navigate>👁️ نظرة على اليوم</x-dropdown-link>
                        <x-dropdown-link :href="route('appointments')" wire:navigate>📅 المواعيد</x-dropdown-link>
                    </x-nav-dropdown>

                    {{-- Self-development group --}}
                    <x-nav-dropdown label="التطوير"
                        :active="request()->routeIs('focus') || request()->routeIs('mind') || request()->routeIs('habits.*') || request()->routeIs('recovery.*') || request()->routeIs('diary.*') || request()->routeIs('comfort-zone') || request()->routeIs('challenges.*')">
                        <x-dropdown-link :href="route('focus')" wire:navigate>🎯 التركيز</x-dropdown-link>
                        <x-dropdown-link :href="route('mind')" wire:navigate>🧠 تنضيف العقل</x-dropdown-link>
                        <x-dropdown-link :href="route('habits.index')" wire:navigate>🔁 العادات</x-dropdown-link>
                        <x-dropdown-link :href="route('challenges.index')" wire:navigate>🔥 التحديات</x-dropdown-link>
                        <x-dropdown-link :href="route('recovery.index')" wire:navigate>🌱 التعافي</x-dropdown-link>
                        <x-dropdown-link :href="route('diary.index')" wire:navigate>📖 المذكرات</x-dropdown-link>
                        <x-dropdown-link :href="route('comfort-zone')" wire:navigate>🚀 خارج الزون</x-dropdown-link>
                    </x-nav-dropdown>

                    <x-nav-link :href="route('career')" :active="request()->routeIs('career') || request()->routeIs('scholarships.*') || request()->routeIs('jobs.*') || request()->routeIs('market-study.*') || request()->routeIs('marketing.*') || request()->routeIs('activities.*') || request()->routeIs('lab.*') || request()->routeIs('career.pitfalls') || request()->routeIs('career.interviews') || request()->routeIs('cvs.*')" wire:navigate>
                        الكارير
                    </x-nav-link>
                    <x-nav-link :href="route('religion')" :active="request()->routeIs('religion*')" wire:navigate>
                        الدين
                    </x-nav-link>
                    <x-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" wire:navigate>
                        المحفظة
                    </x-nav-link>
                    {{-- Reports group --}}
                    <x-nav-dropdown label="التقارير" :active="request()->routeIs('statistics') || request()->routeIs('review') || request()->routeIs('achievements') || request()->routeIs('archive') || request()->routeIs('change-curve')">
                        <x-dropdown-link :href="route('statistics')" wire:navigate>📊 الإحصائيات</x-dropdown-link>
                        <x-dropdown-link :href="route('review')" wire:navigate>🗒️ المراجعة</x-dropdown-link>
                        <x-dropdown-link :href="route('change-curve')" wire:navigate>📈 منحنى التغيير</x-dropdown-link>
                        <x-dropdown-link :href="route('achievements')" wire:navigate>🏅 الإنجازات</x-dropdown-link>
                        <x-dropdown-link :href="route('archive')" wire:navigate>🗄️ الأرشيف</x-dropdown-link>
                    </x-nav-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                <a href="{{ route('search') }}" wire:navigate title="بحث"
                   class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-dark hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('search') ? 'text-primary dark:text-primary-dark' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                </a>
                <livewire:notifications.bell />
                <livewire:settings.theme-toggle />
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            الملف الشخصي
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                تسجيل الخروج
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                لوحة التحكم
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('simply')" :active="request()->routeIs('simply')" wire:navigate>
                🌿 ببساطة
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')" wire:navigate>
                🔍 بحث
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dreams.index')" :active="request()->routeIs('dreams.*')" wire:navigate>
                ✨ الأحلام
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')" wire:navigate>
                الأهداف
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" wire:navigate>
                🗂️ كل التاسكات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('planner')" :active="request()->routeIs('planner') || request()->routeIs('planner.week')" wire:navigate>
                المخطط
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('planner.day-overview')" :active="request()->routeIs('planner.day-overview')" wire:navigate>
                👁️ نظرة على اليوم
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('appointments')" :active="request()->routeIs('appointments')" wire:navigate>
                المواعيد
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('recovery.index')" :active="request()->routeIs('recovery.*')" wire:navigate>
                التعافي
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('diary.index')" :active="request()->routeIs('diary.*')" wire:navigate>
                المذكرات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('comfort-zone')" :active="request()->routeIs('comfort-zone')" wire:navigate>
                خارج الزون
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('focus')" :active="request()->routeIs('focus')" wire:navigate>
                🎯 التركيز
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('mind')" :active="request()->routeIs('mind')" wire:navigate>
                🧠 تنضيف العقل
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('habits.index')" :active="request()->routeIs('habits.*')" wire:navigate>
                العادات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('challenges.index')" :active="request()->routeIs('challenges.*')" wire:navigate>
                التحديات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('career')" :active="request()->routeIs('career') || request()->routeIs('scholarships.*') || request()->routeIs('jobs.*') || request()->routeIs('market-study.*') || request()->routeIs('marketing.*') || request()->routeIs('activities.*') || request()->routeIs('lab.*') || request()->routeIs('career.pitfalls') || request()->routeIs('career.interviews') || request()->routeIs('cvs.*')" wire:navigate>
                الكارير
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('religion')" :active="request()->routeIs('religion*')" wire:navigate>
                الدين
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" wire:navigate>
                المحفظة
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')" wire:navigate>
                إحصائيات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('review')" :active="request()->routeIs('review')" wire:navigate>
                المراجعة
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('change-curve')" :active="request()->routeIs('change-curve')" wire:navigate>
                📈 منحنى التغيير
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('achievements')" :active="request()->routeIs('achievements')" wire:navigate>
                الإنجازات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('archive')" :active="request()->routeIs('archive')" wire:navigate>
                🗄️ الأرشيف
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    الملف الشخصي
                </x-responsive-nav-link>

                <div class="px-4 py-2">
                    <livewire:settings.theme-toggle />
                </div>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        تسجيل الخروج
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
