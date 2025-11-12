@extends('layouts.guest')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-0">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <!-- Left Section - Branding & Info -->
            <div class="space-y-6 lg:space-y-8">
                <div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black mb-3">
                        <span class="gradient-text">TopupGameTudTzy</span>
                    </h1>
                    <p class="text-gray-300 text-base sm:text-lg font-medium">Platform Top Up Game Resmi Indonesia</p>
                </div>

                <div class="space-y-4">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                        Selamat Datang! 👋
                    </h2>
                    <p class="text-gray-300 text-base sm:text-lg leading-relaxed">
                        Kami adalah portal terpercaya untuk top-up game di Indonesia. Nikmati kemudahan bertransaksi dengan keamanan terjamin dan proses yang cepat.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Feature 1 -->
                    <div class="glass p-5 rounded-2xl">
                        <div class="text-3xl mb-3">⚡</div>
                        <h3 class="text-white font-bold mb-2">CEPAT</h3>
                        <p class="text-gray-300 text-sm">Proses instan, konfirmasi dalam hitungan detik</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="glass p-5 rounded-2xl">
                        <div class="text-3xl mb-3">🛡️</div>
                        <h3 class="text-white font-bold mb-2">AMAN</h3>
                        <p class="text-gray-300 text-sm">Pembayaran terenkripsi dengan keamanan maksimal</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="glass p-5 rounded-2xl">
                        <div class="text-3xl mb-3">🎮</div>
                        <h3 class="text-white font-bold mb-2">MUDAH</h3>
                        <p class="text-gray-300 text-sm">Interface intuitif yang mudah digunakan semua orang</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="glass p-5 rounded-2xl">
                        <div class="text-3xl mb-3">💎</div>
                        <h3 class="text-white font-bold mb-2">TERPERCAYA</h3>
                        <p class="text-gray-300 text-sm">Ribuan pelanggan puas telah mempercayai kami</p>
                    </div>
                </div>

                <div class="hidden lg:block pt-4">
                    <p class="text-gray-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Sudah melayani lebih dari 10,000+ pemain
                    </p>
                </div>
            </div>

            <!-- Right Section - Login Form -->
            <div class="flex items-center justify-center">
                <div class="glass w-full max-w-md p-8 sm:p-10">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl sm:text-4xl font-black text-white mb-2">Masuk 🔐</h2>
                        <p class="text-gray-300 text-sm">Silakan login ke akun Anda untuk melanjutkan</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-200 mb-2">Email Anda</label>
                            <input type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                placeholder="contoh@email.com"
                                class="w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('email')
                                <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-200 mb-2">Password</label>
                            <input type="password" 
                                name="password" 
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            @error('password')
                                <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-400 text-pink-600 focus:ring-pink-500">
                                <span>Ingat Saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-pink-400 hover:text-pink-300 transition-colors font-medium">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="btn-modern w-full py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 rounded-lg font-bold text-white shadow-lg shadow-purple-500/50 hover:shadow-pink-500/70 text-base sm:text-lg">
                            Masuk Sekarang
                        </button>

                        <!-- Divider -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-gradient-to-b from-slate-900/50 to-slate-900/50 text-gray-400">atau</span>
                            </div>
                        </div>

                        <!-- Register Link -->
                        <p class="text-center text-gray-300">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="font-bold text-transparent bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text hover:from-purple-300 hover:to-pink-300 transition-colors">
                                Daftar Sekarang
                            </a>
                        </p>
                    </form>

                    <!-- Additional Info -->
                    <div class="mt-8 pt-6 border-t border-gray-600/30">
                        <div class="flex items-start gap-2 text-xs text-gray-400">
                            <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Data Anda aman dan terlindungi dengan enkripsi tingkat enterprise</span>
                        </div>
                    </div>
                </div>
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

    @media (max-width: 1024px) {
        .hidden.lg\:block {
            display: none;
        }
    }
</style>
@endsection
