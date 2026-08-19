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

            {{-- Color Scheme Selector --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <livewire:settings.color-scheme-selector />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:settings.privacy-pin />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                 x-data="{
                    enabled: localStorage.getItem('notif_enabled') === '1',
                    perm: (window.Notification ? Notification.permission : 'unsupported'),
                    async toggle() {
                        if (! this.enabled) {
                            if (this.perm !== 'granted') {
                                this.perm = await Notification.requestPermission();
                                if (this.perm !== 'granted') return;
                            }
                            this.enabled = true; localStorage.setItem('notif_enabled', '1');
                        } else {
                            this.enabled = false; localStorage.setItem('notif_enabled', '0');
                        }
                    }
                 }">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">إشعارات التذكير</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            فعّل إشعارات المتصفح عشان يوصلك تنبيه بتذكيراتك (صلوات، مواعيد، تاسكات…) وأنت فاتح التطبيق أو مثبّته على موبايلك.
                        </p>
                    </header>
                    <div class="mt-4">
                        <template x-if="perm === 'unsupported'">
                            <p class="text-sm text-gray-500 dark:text-gray-400">متصفحك لا يدعم الإشعارات.</p>
                        </template>
                        <template x-if="perm !== 'unsupported'">
                            <div>
                                <button type="button" @click="toggle()"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition"
                                    :class="enabled ? 'bg-danger/15 text-danger' : 'bg-primary dark:bg-primary-dark text-white hover:opacity-90'"
                                    x-text="enabled ? 'إيقاف الإشعارات' : 'تفعيل الإشعارات'"></button>
                                <p x-show="perm === 'denied'" x-cloak class="mt-2 text-xs text-danger">الإشعارات مرفوضة من إعدادات المتصفح — فعّلها من إعدادات الموقع في متصفحك.</p>
                                <p x-show="enabled" x-cloak class="mt-2 text-xs text-success">الإشعارات مفعّلة ✓</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:settings.data-export />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                 x-data="{ installable: !! window.deferredInstallPrompt, installed: false }"
                 x-on:pwa-installable.window="installable = true"
                 x-on:pwa-installed.window="installable = false; installed = true">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">تطبيق الموبايل</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">ثبّت «سيبها على الله» على جهازك للوصول السريع بدون متصفح، ويشتغل جزئيًا بدون نت.</p>
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
