<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl bg-gradient-to-br from-slate-900 via-purple-900/40 to-slate-900 backdrop-blur-xl border border-purple-500/20 rounded-xl p-4 shadow-lg flex items-center gap-2">
            <span>📜</span> Transaction History
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-purple-950 to-slate-900 text-white p-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-gradient-to-br from-slate-900 via-purple-900/40 to-slate-900 backdrop-blur-xl border border-purple-500/20 rounded-2xl shadow-2xl p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-400 border-b border-purple-700/50">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Kode</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4">Detail</th>
                                <th class="py-3 px-4">Jumlah</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $trx)
                                <tr class="border-b border-purple-700/30 text-sm hover:bg-purple-900/20 transition-colors">
                                    <td class="py-3 px-4 text-purple-200">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 text-pink-400 font-mono">{{ $trx->transaction_code }}</td>
                                    <td class="py-3 px-4">
                                        @if($trx->type === 'topup' || $trx->type === 'topup_saldo')
                                            <span class="px-2 py-1 bg-blue-500/10 text-blue-300 rounded-full text-xs">Top Up Saldo</span>
                                        @else
                                            <span class="px-2 py-1 bg-purple-500/10 text-purple-300 rounded-full text-xs">Pembelian Game</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-white">
                                        @if($trx->type === 'purchase' || $trx->type === 'game_purchase')
                                            {{ $trx->game_name }} - {{ $trx->item_name }}
                                        @else
                                            Top Up Saldo
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-bold bg-gradient-to-r from-yellow-300 to-yellow-500 bg-clip-text text-transparent">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $isSuccess = in_array($trx->status, ['completed', 'success']);
                                        @endphp
                                        @if($trx->status === 'pending')
                                            <div class="flex flex-col gap-1">
                                                <span class="px-2 py-1 bg-yellow-500/10 text-yellow-300 rounded-full text-xs inline-block w-fit">⏳ Pending</span>
                                            </div>
                                        @elseif($isSuccess)
                                            <div class="flex flex-col gap-1">
                                                <span class="px-2 py-1 bg-green-500/10 text-green-300 rounded-full text-xs inline-block w-fit">✅ Sukses</span>
                                            </div>
                                        @elseif($trx->status === 'failed')
                                            <div class="flex flex-col gap-1">
                                                <span class="px-2 py-1 bg-red-500/10 text-red-300 rounded-full text-xs inline-block w-fit">❌ Gagal</span>
                                                @if($trx->failure_reason)
                                                    <span class="text-xs text-red-400 italic">{{ $trx->failure_reason }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-purple-300">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>