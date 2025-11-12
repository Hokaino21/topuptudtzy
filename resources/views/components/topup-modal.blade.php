<!-- Top-up Modal -->
<div x-data="topupModal()" x-show="show" x-ref="topupModal" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true"
    x-cloak @keydown.escape.window="closeModal()">
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity cursor-pointer" @click="closeModal()"></div>

    <!-- Modal panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="show" @click.stop
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <form @submit.prevent="submitTopup">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                <div class="flex justify-between items-center">
                                    <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                                        Top Up Saldo
                                    </h3>
                                    <button @click.stop.prevent="closeModal()" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-4 space-y-4">
                                    <!-- Amount Input -->
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700">
                                            Jumlah Top Up (Min: Rp 10.000)
                                        </label>
                                        <div class="mt-1">
                                            <input type="number" name="amount" id="amount" x-model="formData.amount"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="10000" step="1000" required>
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Metode Pembayaran
                                        </label>
                                        <div class="mt-2 space-y-2">
                                            <div class="flex items-center">
                                                <input type="radio" id="bank_transfer" name="payment_method"
                                                    x-model="formData.payment_method" value="bank_transfer"
                                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <label for="bank_transfer" class="ml-3 block text-sm font-medium text-gray-700">
                                                    Transfer Bank
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="radio" id="credit_card" name="payment_method"
                                                    x-model="formData.payment_method" value="credit_card"
                                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <label for="credit_card" class="ml-3 block text-sm font-medium text-gray-700">
                                                    Kartu Kredit
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="radio" id="e_wallet" name="payment_method"
                                                    x-model="formData.payment_method" value="e_wallet"
                                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <label for="e_wallet" class="ml-3 block text-sm font-medium text-gray-700">
                                                    E-Wallet
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error Message -->
                                    <div x-show="error" class="text-red-600 text-sm mt-2" x-text="error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto"
                            :disabled="loading">
                            <span x-show="!loading">Top Up</span>
                            <span x-show="loading">Processing...</span>
                        </button>
                    <button type="button" @click.stop.prevent="closeModal()" x-ref="cancelButton"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto focus:outline-none">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function topupModal() {
        return {
            show: false,
            loading: false,
            error: null,
            formData: {
                amount: 10000,
                payment_method: 'bank_transfer'
            },
            openModal() {
                this.show = true;
                this.error = null;
                this.formData = {
                    amount: 10000,
                    payment_method: 'bank_transfer'
                };
            },
            closeModal() {
                this.show = false;
            },
            async submitTopup() {
                this.loading = true;
                this.error = null;

                try {
                    const response = await fetch('/transactions/topup', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan saat memproses top up.');
                    }

                    // Update the balance in the parent component
                    const dashboardComponent = document.querySelector('[x-data="dashboard"]').__x.$data;
                    dashboardComponent.updateBalance(data.new_balance);

                    // Show success message
                    this.showNotification('Success!', data.message);
                    this.closeModal();

                } catch (error) {
                    this.error = error.message;
                } finally {
                    this.loading = false;
                }
            },
            showNotification(title, message) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        title: title,
                        message: message,
                        type: 'success'
                    }
                }));
            }
        }
    }
</script>