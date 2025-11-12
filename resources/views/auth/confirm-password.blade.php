@extends('layouts.guest')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-0">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="glass w-full max-w-md p-8 sm:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-2">Konfirmasi Password 🔐</h2>
                    <p class="text-gray-300 text-sm">Ini adalah area yang aman</p>
                </div>

                <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Untuk melanjutkan, silakan konfirmasi password Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-200 mb-2">Password</label>
                        <input type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        @error('password')
                            <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="btn-modern w-full py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 rounded-lg font-bold text-white shadow-lg shadow-purple-500/50 hover:shadow-pink-500/70 text-base sm:text-lg">
                        Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .glass > * {
        animation: fadeInUp 0.6s ease-out forwards;
    }
</style>
@endsection
