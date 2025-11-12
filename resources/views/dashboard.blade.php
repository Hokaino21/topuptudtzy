<x-app-layout>
    <!-- Include AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                purchaseModal: false,
                topupModal: false,
                selectedGame: null,
                loadingState: false,
                userBalance: {{ auth()->user()->balance ?? 0 }},

                updateBalance(newBalance) {
                    this.userBalance = newBalance;
                },

                openTopupSelection() {
                    this.topupModal = true;
                },

                closeTopupSelection() {
                    this.topupModal = false;
                },

                openTopupCustom() {
                    // Open the custom topup modal component (defined in components/topup-modal.blade.php)
                    document.querySelector('[x-ref="topupModal"]')?.__x.$data.openModal();
                },

                async handleTopup(amount, method) {
                    try {
                        this.loadingState = true;
                        const response = await fetch('{{ route('transactions.topup') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ amount, payment_method: method })
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.updateBalance(data.new_balance);
                                // Close the custom topup modal (use Alpine internals via __x)
                                // Use document query to find the topup modal component and call its method
                                document.querySelector('[x-ref="topupModal"]')?.__x.$data.closeModal();
                                this.showNotification('Success!', data.message);
                        } else {
                            throw new Error(data.message || 'An error occurred');
                        }
                    } catch (error) {
                        this.showNotification('Error!', error.message, 'error');
                    } finally {
                        this.loadingState = false;
                    }
                },

                showNotification(title, message, type = 'success') {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            title: title,
                            message: message,
                            type: type
                        }
                    }));
                },

                async handlePurchase(game, item, method) {
                    try {
                        this.loadingState = true;
                        const response = await fetch('{{ route('transactions.purchase') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                game_name: game.name,
                                item_name: item.name,
                                amount: item.price,
                                payment_method: method
                            })
                        });

                        const data = await response.json();
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'An error occurred');
                        }
                    } catch (error) {
                        alert('Failed to process purchase');
                    } finally {
                        this.loadingState = false;
                    }
                },

                openPurchaseModal(game) {
                    this.selectedGame = game;
                    this.purchaseModal = true;
                }
            }));
        });
    </script>

    <div x-data="dashboard" 
         class="min-h-screen bg-gradient-to-br from-slate-950 via-purple-950 to-slate-900 text-white relative overflow-hidden">
        
        <!-- Top Up Modal -->
       <div x-show="topupModal" 
           x-cloak 
           @keydown.escape.window="closeTopupSelection()"
             class="fixed inset-0 z-50">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeTopupSelection()"></div>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4" @click.stop>
                    <div class="relative bg-gradient-to-br from-slate-900 to-purple-900/80 border border-purple-500/20 rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-white">Top Up Saldo</h3>
                            <button @click.stop="closeTopupSelection()" class="text-gray-400 hover:text-white focus:outline-none">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ([10000, 20000, 50000, 100000, 200000, 500000] as $amount)
                                <button @click="handleTopup({{ $amount }}, 'bank_transfer')" 
                                        :disabled="loadingState"
                                        class="p-4 bg-slate-800 hover:bg-purple-600 rounded-xl transition-colors">
                                    <div class="text-xs text-gray-400">Top Up</div>
                                    <div class="font-bold text-white">Rp {{ number_format($amount, 0, ',', '.') }}</div>
                                </button>
                                @endforeach
                            </div>

                            <div class="flex justify-end gap-2 pt-4 border-t border-gray-700">
                                <button @click.stop="closeTopupSelection()" 
                                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors focus:outline-none">
                                    Kembali
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Purchase Modal -->
        <div x-show="purchaseModal" 
             x-cloak 
             @keydown.escape.window="purchaseModal = false"
             class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="purchaseModal = false"></div>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-gradient-to-br from-slate-900 to-purple-900/80 border border-purple-500/20 rounded-2xl shadow-xl max-w-md w-full p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold" x-text="selectedGame?.name"></h3>
                            <button @click="purchaseModal = false" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="item in [
                                    { name: 'Small Pack', price: 10000 },
                                    { name: 'Medium Pack', price: 20000 },
                                    { name: 'Large Pack', price: 50000 },
                                    { name: 'XL Pack', price: 100000 }
                                ]">
                                    <button @click="handlePurchase(selectedGame, item, 'balance')"
                                            :disabled="loadingState"
                                            class="p-4 bg-slate-800 hover:bg-purple-600 rounded-xl transition-colors">
                                        <div x-text="item.name" class="text-sm font-bold"></div>
                                        <div x-text="'Rp ' + item.price.toLocaleString()" class="text-xs text-gray-400"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 relative z-10">
            <!-- Header -->
            <header class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
                <div class="space-y-2">
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                        TopupGameTudTzy
                    </h1>
                    <p class="text-gray-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Top up cepat & aman — diamond, UC, dan item resmi
                    </p>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="bg-gradient-to-br from-slate-800/80 to-purple-900/40 backdrop-blur-xl p-4 rounded-2xl shadow-2xl border border-purple-500/20 hover:border-purple-500/40 transition-all duration-300 group">
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Saldo Anda</div>
                        <div class="text-2xl font-bold bg-gradient-to-r from-yellow-300 to-yellow-500 bg-clip-text text-transparent group-hover:scale-105 transition-transform">
                            Rp <span x-text="Number(userBalance).toLocaleString('id-ID')">{{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button @click="openTopupSelection()" class="px-5 py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 rounded-xl text-sm font-bold shadow-lg shadow-purple-500/50 hover:shadow-purple-500/70 hover:scale-105 transition-all duration-300 whitespace-nowrap">
                        💎 Top Up
                    </button>
                </div>
            </header>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Section (3 columns) -->
                <section class="lg:col-span-3 space-y-6">
                    <!-- Hero Banner -->
                    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-900/80 to-purple-900/40 backdrop-blur-xl border border-purple-500/20 shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-pink-600/10"></div>
                        <div class="relative p-8">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                                <div class="space-y-4 flex-1">
                                    <div class="inline-block px-3 py-1 bg-purple-500/20 rounded-full text-xs font-semibold text-purple-300 border border-purple-500/30">
                                        🔥 Trending Now
                                    </div>
                                    <h2 class="text-3xl font-bold text-white">Selamat Datang, Player!</h2>
                                    <p class="text-gray-300 text-sm leading-relaxed max-w-xl">
                                        Pilih game dan paket yang ingin kamu top-up. Transaksi cepat, konfirmasi instan dengan keamanan terjamin.
                                    </p>
                                    <div class="flex gap-3 pt-2">
                                            <button @click="openTopupSelection()" class="px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 rounded-xl text-white font-bold shadow-lg hover:shadow-pink-500/50 hover:scale-105 transition-all duration-300">
                                                Mulai Top Up →
                                            </button>
                                        <a href="{{ route('transactions.history') }}" class="px-6 py-3 bg-slate-800/80 backdrop-blur-sm border border-slate-700 rounded-xl text-gray-300 font-semibold hover:bg-slate-700/80 transition-all duration-300">
                                            📜 Riwayat
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-3">
                                    <div class="p-4 bg-gradient-to-br from-purple-900/50 to-pink-900/30 backdrop-blur-sm rounded-2xl border border-purple-500/30 text-center min-w-[180px] hover:scale-105 transition-transform duration-300">
                                        <div class="text-xs text-purple-300 uppercase tracking-wider mb-1">⚡ Promo Saat Ini</div>
                                        <div class="text-sm font-bold text-white">Diskon 10% setiap Jumat</div>
                                    </div>
                                    <div class="p-4 bg-slate-900/60 backdrop-blur-sm rounded-2xl border border-green-500/30 text-center hover:scale-105 transition-transform duration-300">
                                        <div class="text-xs text-green-300 uppercase tracking-wider mb-1">🔒 Keamanan</div>
                                        <div class="text-sm font-bold text-green-400">Pembayaran Terenkripsi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Games Grid -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <span class="text-2xl">🎮</span>
                            <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Game Populer</span>
                        </h3>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                            $games = [
                                ['name' => 'Mobile Legends', 'icon' => '⚔️', 'color' => 'from-blue-600/20 to-purple-600/20'],
                                ['name' => 'Free Fire', 'icon' => '🔥', 'color' => 'from-orange-600/20 to-red-600/20'],
                                ['name' => 'Valorant', 'icon' => '🎯', 'color' => 'from-red-600/20 to-pink-600/20'],
                                ['name' => 'PUBG Mobile', 'icon' => '🎮', 'color' => 'from-yellow-600/20 to-orange-600/20'],
                                ['name' => 'Genshin Impact', 'icon' => '⚡', 'color' => 'from-purple-600/20 to-indigo-600/20'],
                                ['name' => 'COD Mobile', 'icon' => '🎖️', 'color' => 'from-green-600/20 to-teal-600/20'],
                            ];
                            @endphp
                            
                            @foreach ($games as $g)
                            <article class="group relative bg-gradient-to-br {{ $g['color'] }} backdrop-blur-sm border border-slate-700/50 rounded-2xl p-5 hover:border-purple-500/50 hover:scale-105 transform transition-all duration-300 shadow-lg hover:shadow-purple-500/20 cursor-pointer">
                                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-purple-900/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative">
                                    <div class="flex items-start gap-4 mb-3">
                                        <div class="relative">
                                            <div class="w-16 h-16 flex items-center justify-center rounded-xl border-2 border-slate-700 group-hover:border-purple-500 bg-slate-900 transition-all duration-300">
                                                <span class="text-3xl">{{ $g['icon'] }}</span>
                                            </div>
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-slate-900 animate-pulse"></div>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-base text-white group-hover:text-purple-300 transition-colors">{{ $g['name'] }}</h3>
                                            <p class="text-xs text-gray-400 mt-1">⚡ Proses instan</p>
                                            <div class="flex items-center gap-1 mt-1">
                                                <span class="text-yellow-400 text-xs">★★★★★</span>
                                                <span class="text-xs text-gray-500">(4.9)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-700/50">
                                        <div>
                                            <div class="text-xs text-gray-500">Mulai dari</div>
                                            <div class="text-sm font-bold text-white">Rp 10.000</div>
                                        </div>
                                        <button @click="openPurchaseModal({{ json_encode($g) }})" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg text-white text-sm font-bold shadow-lg hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300">
                                            Top Up
                                        </button>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- Popular Packages -->
                    <div id="topup">
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <span class="text-2xl">💎</span>
                            <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Paket Populer</span>
                        </h3>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                            // Add numeric `price_raw` so client-side can immediately charge balance without opening modal
                            $packages = [
                                ['title'=>'ML Diamond 86','price'=>'Rp 20.000','price_raw' => 20000,'game'=>'Mobile Legends', 'badge'=>'Terlaris', 'discount'=>''],
                                ['title'=>'FF Diamond 120','price'=>'Rp 25.000','price_raw' => 25000,'game'=>'Free Fire', 'badge'=>'Hot', 'discount'=>'-15%'],
                                ['title'=>'PUBG UC 300','price'=>'Rp 50.000','price_raw' => 50000,'game'=>'PUBG Mobile', 'badge'=>'Promo', 'discount'=>'-10%'],
                            ];
                            @endphp
                            
                            @foreach($packages as $p)
                            <div class="group relative bg-gradient-to-br from-slate-900/80 to-slate-800/40 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-5 hover:border-purple-500/50 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-purple-500/20">
                                @if($p['badge'])
                                <div class="absolute -top-2 -right-2 px-3 py-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full text-xs font-bold shadow-lg animate-pulse">
                                    {{ $p['badge'] }}
                                </div>
                                @endif
                                
                                <div class="space-y-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="text-xs text-purple-400 font-semibold mb-1">{{ $p['game'] }}</div>
                                            <div class="font-bold text-lg text-white">{{ $p['title'] }}</div>
                                            @if($p['discount'])
                                            <div class="inline-block mt-1 px-2 py-0.5 bg-red-500/20 border border-red-500/30 rounded text-xs font-bold text-red-400">
                                                {{ $p['discount'] }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500">Harga</div>
                                            <div class="font-black text-xl bg-gradient-to-r from-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                                                {{ $p['price'] }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <!-- Direct purchase: immediately charge user's balance using package amount -->
                                        <button @click="handlePurchase({ name: '{{ $p['game'] }}' }, { name: '{{ $p['title'] }}', price: {{ $p['price_raw'] }} }, 'balance')" class="flex-1 text-center py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl text-white font-bold shadow-lg hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300">
                                            🛒 Beli
                                        </button>
                                        <a href="#" class="px-4 py-2.5 bg-slate-800/80 backdrop-blur-sm border border-slate-700 rounded-xl text-gray-300 font-semibold hover:bg-slate-700/80 hover:border-purple-500/50 transition-all duration-300">
                                            ℹ️
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <!-- Right Sidebar -->
                <aside class="lg:col-span-1 space-y-4">
                    <!-- User Profile Card -->
                    <div class="bg-gradient-to-br from-slate-900/80 to-purple-900/40 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-5 shadow-2xl hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-xl font-bold shadow-lg">
                                {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 uppercase tracking-wider">Akun</div>
                                <div class="font-bold text-white">{{ Auth::user()->name ?? 'Player' }}</div>
                                <div class="text-xs text-purple-400">ID: #{{ Auth::user()->id ?? '0000' }}</div>
                            </div>
                        </div>
                        <button @click="openTopupSelection()" class="block w-full text-center py-3 bg-gradient-to-r from-pink-600 via-purple-600 to-pink-600 rounded-xl text-white font-bold shadow-lg hover:shadow-pink-500/50 hover:scale-105 transition-all duration-300">
                            💰 Isi Saldo
                        </button>
                    </div>

                    <!-- Stats Card -->
                    <div class="bg-gradient-to-br from-slate-900/60 to-slate-800/40 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-5 shadow-xl">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span>📊</span> Statistik Bulan Ini
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-green-500/10 border border-green-500/20 rounded-xl">
                                <div>
                                    <div class="text-xs text-gray-400">Total Transaksi</div>
                                    <div class="font-black text-xl text-green-400">Rp {{ number_format(auth()->user()->transactions()->where('status', 'completed')->sum('amount'), 0, ',', '.') }}</div>
                                </div>
                                <div class="text-2xl">💸</div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                                <div>
                                    <div class="text-xs text-gray-400">Pending</div>
                                    <div class="font-black text-xl text-yellow-400">2</div>
                                </div>
                                <div class="text-2xl">⏳</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div id="transactions" class="bg-gradient-to-br from-slate-900/80 to-purple-900/40 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-5 shadow-2xl">
                        <div class="text-sm font-bold mb-4 flex items-center gap-2">
                            <span>📜</span>
                            <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Riwayat Terbaru</span>
                        </div>
                        <ul class="space-y-3">
                            @forelse (auth()->user()->transactions()->latest()->take(5)->get() as $trx)
                            <li class="flex items-center justify-between p-3 bg-slate-800/50 rounded-xl border border-slate-700/30 hover:border-{{ $trx->status === 'completed' ? 'green' : ($trx->status === 'pending' ? 'yellow' : 'red') }}-500/30 transition-all duration-300">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-{{ $trx->status === 'completed' ? 'green' : ($trx->status === 'pending' ? 'yellow' : 'red') }}-400 rounded-full animate-pulse"></div>
                                    <div>
                                        <div class="text-xs text-gray-400">{{ $trx->created_at->format('Y-m-d') }}</div>
                                        <div class="text-sm font-semibold text-white">
                                            @if($trx->type === 'purchase')
                                                {{ $trx->game_name }} - {{ $trx->item_name }}
                                            @else
                                                Top Up Saldo
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-{{ $trx->status === 'completed' ? 'green' : ($trx->status === 'pending' ? 'yellow' : 'red') }}-400 px-2 py-1 bg-{{ $trx->status === 'completed' ? 'green' : ($trx->status === 'pending' ? 'yellow' : 'red') }}-500/10 rounded">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </li>
                            @empty
                            <li class="text-center text-gray-400 text-sm py-3">
                                Belum ada transaksi
                            </li>
                            @endforelse
                        </ul>
                        <a href="{{ route('transactions.history') }}" class="block mt-4 text-center text-sm text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                            Lihat Semua →
                        </a>
                    </div>

                    <!-- Support Banner -->
                    <div class="bg-gradient-to-br from-blue-900/40 to-purple-900/40 backdrop-blur-xl border border-blue-500/20 rounded-2xl p-5 text-center shadow-xl">
                        <div class="text-3xl mb-2">💬</div>
                        <div class="text-sm font-bold text-white mb-1">Butuh Bantuan?</div>
                        <div class="text-xs text-gray-400 mb-3">Customer service kami siap 24/7</div>
                        <div class="space-y-2">
                            <a href="https://wa.me/+6285695984270?text=Halo%20Admin%20TopupGameTudTzy%2C%0A%0ASaya%20butuh%20bantuan%20untuk%20topup%20game.%0A%0ANama%3A%20{{ Auth::user()->name ?? 'Player' }}%0AID%3A%20%23{{ Auth::user()->id ?? '0000' }}%0A%0AMohon%20bantuannya%2C%20terima%20kasih!" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white text-sm font-semibold transition-all duration-300">
                                <span>📱 Chat WhatsApp</span>
                            </a>
                            <div class="text-xs text-gray-500">Atau hubungi kami di:</div>
                            <div class="text-sm text-green-400 font-medium">+62 856-9598-4270</div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Footer -->
            <footer class="mt-12 pt-8 border-t border-slate-800">
                <div class="text-center space-y-2">
                    <div class="flex items-center justify-center gap-4 text-sm text-gray-500">
                        <a href="#" class="hover:text-purple-400 transition-colors">Tentang Kami</a>
                        <span>•</span>
                        <a href="#" class="hover:text-purple-400 transition-colors">Syarat & Ketentuan</a>
                        <span>•</span>
                        <a href="#" class="hover:text-purple-400 transition-colors">Kebijakan Privasi</a>
                    </div>
                    <p class="text-sm text-gray-600">
                        © {{ date('Y') }} GameTopUpZone. All Rights Reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <style>
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }

        [x-cloak] { 
            display: none !important; 
        }
    </style>

    <!-- Top Up Modal -->
    <x-topup-modal x-ref="topupModal" />

    <!-- Purchase Modal -->
    <x-modal-purchase :show="false" :game="null" />

</x-app-layout>