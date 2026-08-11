<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:settings.privacy-pin />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                 x-data="{ installable: !! window.deferredInstallPrompt, installed: false }"
                 x-on:pwa-installable.window="installable = true"
                 x-on:pwa-installed.window="installable = false; installed = true">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">تطبيق الموبايل</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">ثبّت «إدارة الحياة» على جهازك للوصول السريع بدون متصفح، ويشتغل جزئيًا بدون نت.</p>
                    </header>
                    <div class="mt-4">
                        <button type="button" x-show="installable"
                            @click="const p = window.deferredInstallPrompt; if (p) { p.prompt(); p.userChoice.finally(() => { window.deferredInstallPrompt = null; installable = false; }); }"
                            class="inline-flex items-center px-4 py-2 bg-primary dark:bg-primary-dark text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                            تثبيت التطبيق
                        </button>
                        <p x-show="installed" class="text-sm text-success">تم التثبيت ✓</p>
                        <p x-show="! installable && ! installed" x-cloak class="text-sm text-gray-500 dark:text-gray-400">
                            لو الزر مش ظاهر: افتح قائمة المتصفح واختر «تثبيت التطبيق» أو «إضافة إلى الشاشة الرئيسية».
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
