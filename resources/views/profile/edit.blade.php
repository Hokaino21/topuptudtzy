<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl bg-gradient-to-r from-sky-900/90 to-blue-900/80 backdrop-blur-xl border border-sky-500/20 rounded-xl p-4 shadow-lg flex items-center gap-2 text-sky-100">
            <span>👤</span> {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-gradient-to-br from-sky-900/90 via-blue-900/80 to-sky-900/90 backdrop-blur-xl border border-sky-500/20 rounded-xl shadow-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gradient-to-br from-sky-900/90 via-blue-900/80 to-sky-900/90 backdrop-blur-xl border border-sky-500/20 rounded-xl shadow-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gradient-to-br from-sky-900/90 via-blue-900/80 to-sky-900/90 backdrop-blur-xl border border-sky-500/20 rounded-xl shadow-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
