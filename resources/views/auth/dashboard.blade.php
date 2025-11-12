<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            🎮 Dashboard - Game TopUp Zone
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white p-6">
        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto text-center py-10">
            <h1 class="text-4xl md:text-5xl font-bold text-purple-400 drop-shadow-lg">
                Selamat Datang di GameTopUpZone
            </h1>
            <p class="text-gray-300 mt-3 text-lg">Top up game favoritmu dengan cepat, aman, dan harga terbaik 🚀</p>
            <div class="mt-6">
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-full shadow-md transition duration-300">
                    Mulai Top Up Sekarang
                </a>
            </div>
        </div>

        <!-- Game List Section -->
        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
            @php
                $games = [
                    ['name' => 'Mobile Legends', 'img' => 'https://upload.wikimedia.org/wikipedia/en/2/21/Mobile_Legends_logo.png'],
                    ['name' => 'Free Fire', 'img' => 'https://seeklogo.com/images/F/free-fire-logo-5C6E6370C2-seeklogo.com.png'],
                    ['name' => 'Valorant', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/Valorant_logo_-_pink_color_version.svg'],
                    ['name' => 'PUBG Mobile', 'img' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/PUBG_New_State_logo.png'],
                    ['name' => 'Genshin Impact', 'img' => 'https://upload.wikimedia.org/wikipedia/en/3/31/Genshin_Impact_logo.svg'],
                    ['name' => 'COD Mobile', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/5/59/Call_of_Duty_Mobile_logo.png'],
                ];
            @endphp

            @foreach ($games as $game)
                <div class="bg-gray-800 hover:bg-gray-700 rounded-2xl shadow-lg overflow-hidden transition duration-300">
                    <img src="{{ $game['img'] }}" alt="{{ $game['name'] }}" class="h-40 w-full object-contain bg-gray-900 p-4">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-purple-400">{{ $game['name'] }}</h3>
                        <p class="text-sm text-gray-400 mt-2">Top up {{ $game['name'] }} dengan harga termurah & proses instan!</p>
                        <button class="mt-4 bg-purple-600 hover:bg-purple-700 w-full py-2 rounded-lg font-semibold transition">
                            Top Up Sekarang
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="text-center mt-16 text-gray-500 text-sm">
            <p>© {{ date('Y') }} GameTopUpZone. All Rights Reserved.</p>
        </div>
    </div>
</x-app-layout>
