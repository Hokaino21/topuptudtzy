@props(['show' => false, 'game' => null])

<div x-data="{ 
    show: @js($show),
    game: @js($game),
    selectedItem: null,
    paymentMethod: '',
    isSubmitting: false,
    async submit() {
        if (!this.selectedItem) return;
        
        this.isSubmitting = true;
        try {
            const response = await fetch('{{ route('transactions.purchase') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    game_name: this.game.name,
                    item_name: this.selectedItem.name,
                    amount: this.selectedItem.price,
                    payment_method: this.paymentMethod
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'An error occurred');
            }
            
            // Show success message
            this.show = false;
            window.location.reload();
        } catch (error) {
            alert(error.message);
        } finally {
            this.isSubmitting = false;
        }
    }
}" x-show="show" x-cloak @keydown.escape.window="show = false" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Backdrop -->
        <div x-show="show" @click="show = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- Modal -->
        <div x-show="show" class="relative bg-gradient-to-br from-slate-900 to-purple-900/80 border border-purple-500/20 rounded-2xl shadow-xl max-w-md w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-white" x-text="game?.name"></h3>
                        <p class="text-sm text-gray-400">Pilih item dan metode pembayaran</p>
                    </div>
                    <button @click="show = false" type="button" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Item Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300">Pilih Item</label>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="item in game?.items" :key="item.id">
                            <button type="button" @click="selectedItem = item" :class="{'bg-purple-600': selectedItem?.id === item.id, 'hover:bg-slate-700': selectedItem?.id !== item.id}" class="p-3 text-center border border-slate-700 rounded-xl transition-colors">
                                <div class="text-sm font-bold text-white" x-text="item.name"></div>
                                <div class="text-xs text-gray-400" x-text="`Rp ${new Intl.NumberFormat('id-ID').format(item.price)}`"></div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ([
                            ['value' => 'balance', 'label' => 'Saldo', 'icon' => '💰'],
                            ['value' => 'bank_transfer', 'label' => 'Transfer Bank', 'icon' => '🏦'],
                            ['value' => 'credit_card', 'label' => 'Kartu Kredit', 'icon' => '💳'],
                            ['value' => 'e_wallet', 'label' => 'E-Wallet', 'icon' => '📱']
                        ] as $method)
                            <button type="button" @click="paymentMethod = '{{ $method['value'] }}'" :class="{'bg-purple-600': paymentMethod === '{{ $method['value'] }}', 'hover:bg-slate-700': paymentMethod !== '{{ $method['value'] }}'}" class="p-3 text-center border border-slate-700 rounded-xl transition-colors">
                                <div class="text-xl mb-1">{{ $method['icon'] }}</div>
                                <div class="text-sm text-white">{{ $method['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" :disabled="!selectedItem || !paymentMethod || isSubmitting" class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-purple-500/50 transition-all duration-300">
                        <span x-show="!isSubmitting">🎮 Beli Sekarang</span>
                        <span x-show="isSubmitting">
                            <svg class="w-5 h-5 mx-auto animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>