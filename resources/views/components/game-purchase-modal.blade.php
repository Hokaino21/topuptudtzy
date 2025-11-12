@props(['show' => false, 'game' => null])

<div x-data="{
    show: @js($show),
    game: @js($game),
    selectedItem: null,
    paymentMethod: '',
    loading: false,
    error: null,
    
    async handlePurchase() {
        if (!this.selectedItem || !this.paymentMethod) {
            this.error = 'Please select an item and payment method';
            return;
        }

        this.loading = true;
        this.error = null;

        try {
            const response = await fetch('{{ route('transactions.purchase') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({
                    game_name: this.game.name,
                    item_name: this.selectedItem.name,
                    amount: this.selectedItem.price,
                    payment_method: this.paymentMethod
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to process purchase');
            }

            // Success - close modal and refresh page
            this.show = false;
            window.location.reload();

        } catch (err) {
            this.error = err.message;
        } finally {
            this.loading = false;
        }
    }
}" 
    x-show="show" 
    class="fixed inset-0 z-50 overflow-y-auto" 
    x-cloak
    @keydown.escape.window="show = false"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true" @click="show = false"></div>

        <!-- Modal -->
        <div class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-br from-slate-900 to-purple-900/80 rounded-2xl border border-purple-500/20 shadow-xl sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                        <span class="text-2xl">🎮</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white" x-text="game?.name || 'Select Game'"></h3>
                        <p class="text-sm text-gray-400">Choose your package</p>
                    </div>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Error Message -->
            <div x-show="error" x-cloak class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm">
                <span x-text="error"></span>
            </div>

            <form @submit.prevent="handlePurchase">
                <!-- Package Selection -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-300">Select Package</label>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="item in [
                            { id: 1, name: 'Small Pack', price: 10000, diamonds: '100 💎' },
                            { id: 2, name: 'Medium Pack', price: 20000, diamonds: '250 💎' },
                            { id: 3, name: 'Large Pack', price: 50000, diamonds: '750 💎' },
                            { id: 4, name: 'XL Pack', price: 100000, diamonds: '1500 💎' }
                        ]" :key="item.id">
                            <button type="button"
                                @click="selectedItem = item"
                                :class="{'ring-2 ring-purple-500': selectedItem?.id === item.id}"
                                class="relative p-4 bg-slate-800/50 rounded-xl border border-slate-700/50 hover:border-purple-500/50 transition-all duration-300">
                                <div class="text-sm font-bold text-white" x-text="item.diamonds"></div>
                                <div class="text-xs text-gray-400 mt-1" x-text="'Rp ' + item.price.toLocaleString()"></div>
                                <div class="absolute -top-2 -right-2" x-show="selectedItem?.id === item.id">
                                    <div class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mt-6 space-y-4">
                    <label class="block text-sm font-medium text-gray-300">Payment Method</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                            @click="paymentMethod = 'balance'"
                            :class="{'ring-2 ring-purple-500': paymentMethod === 'balance'}"
                            class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50 hover:border-purple-500/50 transition-all">
                            <div class="text-xl mb-1">💰</div>
                            <div class="text-sm font-medium text-white">Account Balance</div>
                            <div class="text-xs text-gray-400">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</div>
                        </button>
                        <button type="button"
                            @click="paymentMethod = 'bank_transfer'"
                            :class="{'ring-2 ring-purple-500': paymentMethod === 'bank_transfer'}"
                            class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50 hover:border-purple-500/50 transition-all">
                            <div class="text-xl mb-1">🏦</div>
                            <div class="text-sm font-medium text-white">Bank Transfer</div>
                            <div class="text-xs text-gray-400">Direct Transfer</div>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                        :disabled="!selectedItem || !paymentMethod || loading"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg hover:shadow-purple-500/50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">Confirm Purchase</span>
                        <span x-show="loading" class="flex items-center justify-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processing...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>