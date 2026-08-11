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
                        <x-application-logo class="block h-9 w-auto fill-current text-primary dark:text-primary-dark" />
                        <span class="font-bold text-primary dark:text-primary-dark hidden sm:inline">إدارة الحياة</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:gap-6 sm:items-stretch">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        لوحة التحكم
                    </x-nav-link>

                    {{-- Planning group --}}
                    <x-nav-dropdown label="التخطيط"
                        :active="request()->routeIs('goals.*') || request()->routeIs('planner*') || request()->routeIs('appointments')">
                        <x-dropdown-link :href="route('goals.index')" wire:navigate>🎯 الأهداف</x-dropdown-link>
                        <x-dropdown-link :href="route('planner')" wire:navigate>🗓️ المخطط</x-dropdown-link>
                        <x-dropdown-link :href="route('appointments')" wire:navigate>📅 المواعيد</x-dropdown-link>
                    </x-nav-dropdown>

                    {{-- Self-development group --}}
                    <x-nav-dropdown label="التطوير"
                        :active="request()->routeIs('habits.*') || request()->routeIs('recovery.*') || request()->routeIs('diary.*') || request()->routeIs('comfort-zone') || request()->routeIs('challenges.*')">
                        <x-dropdown-link :href="route('habits.index')" wire:navigate>🔁 العادات</x-dropdown-link>
                        <x-dropdown-link :href="route('challenges.index')" wire:navigate>🔥 التحديات</x-dropdown-link>
                        <x-dropdown-link :href="route('recovery.index')" wire:navigate>🌱 التعافي</x-dropdown-link>
                        <x-dropdown-link :href="route('diary.index')" wire:navigate>📖 المذكرات</x-dropdown-link>
                        <x-dropdown-link :href="route('comfort-zone')" wire:navigate>🚀 خارج الزون</x-dropdown-link>
                    </x-nav-dropdown>

                    <x-nav-link :href="route('career')" :active="request()->routeIs('career') || request()->routeIs('scholarships.*') || request()->routeIs('jobs.*') || request()->routeIs('market-study.*') || request()->routeIs('marketing.*') || request()->routeIs('cvs.*')" wire:navigate>
                        الكارير
                    </x-nav-link>
                    <x-nav-link :href="route('religion')" :active="request()->routeIs('religion*')" wire:navigate>
                        الدين
                    </x-nav-link>
                    <x-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')" wire:navigate>
                        إحصائيات
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
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
            <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')" wire:navigate>
                الأهداف
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('planner')" :active="request()->routeIs('planner*')" wire:navigate>
                المخطط
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
            <x-responsive-nav-link :href="route('habits.index')" :active="request()->routeIs('habits.*')" wire:navigate>
                العادات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('challenges.index')" :active="request()->routeIs('challenges.*')" wire:navigate>
                التحديات
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('career')" :active="request()->routeIs('career') || request()->routeIs('scholarships.*') || request()->routeIs('jobs.*') || request()->routeIs('market-study.*') || request()->routeIs('marketing.*') || request()->routeIs('cvs.*')" wire:navigate>
                الكارير
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('religion')" :active="request()->routeIs('religion*')" wire:navigate>
                الدين
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')" wire:navigate>
                إحصائيات
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
