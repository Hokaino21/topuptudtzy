@extends('layouts.guest')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-0">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="glass w-full max-w-md p-8 sm:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-2">Verifikasi Email ✉️</h2>
                    <p class="text-gray-300 text-sm">Terima kasih telah mendaftar!</p>
                </div>

                <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek email dan klik link tersebut untuk memverifikasi akun Anda. Jika Anda tidak menerima email, kami akan dengan senang hati mengirimkan yang lain.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                        <p class="text-green-400 text-sm font-medium">
                            Link verifikasi baru telah kami kirimkan ke email Anda.
                        </p>
                    </div>
                @endif

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="btn-modern w-full py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 rounded-lg font-bold text-white shadow-lg shadow-purple-500/50 hover:shadow-pink-500/70 text-base sm:text-lg">
                            📧 Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg text-gray-300 font-semibold hover:bg-slate-700/50 transition-all duration-300">
                            ← Logout
                        </button>
                    </form>
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
</style>
@endsection
