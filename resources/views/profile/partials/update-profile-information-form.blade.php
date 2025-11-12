<section>
    <header>
        <h2 class="text-lg font-extrabold text-black">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm font-bold text-black">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo Upload -->
        <div x-data="{ photoPreview: '{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : '' }}' }">
            <x-input-label for="profile_photo" :value="__('Profile Photo')" class="text-black font-extrabold" />
            
            <!-- Current Photo Display -->
            <div class="mt-3 mb-4">
                <div class="flex items-center gap-4">
                    <img x-show="photoPreview" 
                         :src="photoPreview" 
                         alt="Preview" 
                         class="w-24 h-24 rounded-lg object-cover border-2 border-gray-300">
                    <div x-show="!photoPreview" class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">{{ __('Upload a photo (JPEG, PNG, GIF - max 2MB)') }}</p>
                        <input 
                            type="file" 
                            id="profile_photo" 
                            name="profile_photo" 
                            accept="image/*"
                            @change="
                                if ($event.target.files.length) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => photoPreview = e.target.result;
                                    reader.readAsDataURL($event.target.files[0]);
                                }
                            "
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100"
                        />
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-black font-extrabold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-black font-bold" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-black font-extrabold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-black font-bold" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 font-extrabold text-black">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm font-extrabold text-black hover:text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-bold text-sm text-green-800">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-extrabold text-black"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
