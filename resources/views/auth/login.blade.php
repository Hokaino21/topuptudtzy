@extends('layouts.guest')

@section('content')
<div class="flex flex-col items-center justify-center w-full px-6 py-12">
    <div class="glass w-full max-w-md p-8 text-white">
        <h2 class="text-3xl font-semibold text-center mb-6">Welcome Back 👋</h2>
        <p class="text-center text-gray-200 mb-8">Silakan login ke akunmu untuk melanjutkan.</p>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 rounded-lg text-gray-800 focus:ring-2 focus:ring-indigo-400 outline-none">
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 rounded-lg text-gray-800 focus:ring-2 focus:ring-indigo-400 outline-none">
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="btn-modern w-full py-2 bg-indigo-500 rounded-lg font-semibold hover:bg-indigo-600">
                Login
            </button>

            <p class="text-center text-sm text-gray-200 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-white underline hover:text-indigo-200">
                    Daftar Sekarang
                </a>
            </p>
        </form>
    </div>
</div>
@endsection
