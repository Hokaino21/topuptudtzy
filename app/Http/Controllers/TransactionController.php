<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display the user's transaction history.
     */
    public function history(): View
    {
        $transactions = auth()->user()->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('transactions.history', compact('transactions'));
    }

    /**
     * Process a top-up transaction.
     *
     * @throws ValidationException
     */
    public function topupSaldo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,credit_card,e_wallet']
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'topup',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'transaction_code' => 'TU-' . strtoupper(Str::random(8))
            ]);

            // You would integrate with a payment gateway here
            // For now, we'll simulate a successful payment
            $transaction->status = 'completed';
            $transaction->save();

            // Update user's balance
            $user = auth()->user();
            $user->balance += $validated['amount'];
            $user->save();

            return $transaction;
        });

        return response()->json([
            'message' => 'Top up saldo berhasil!',
            'transaction' => $transaction->load('user'),
            'new_balance' => auth()->user()->balance
        ]);
    }

    /**
     * Process a game purchase transaction.
     *
     * @throws ValidationException
     */
    public function purchaseGame(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_name' => ['required', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'payment_method' => ['required', 'string', 'in:balance,bank_transfer,credit_card,e_wallet']
        ]);

        return DB::transaction(function () use ($validated) {
            $user = auth()->user();

            // Check if balance is sufficient for balance payment method
            if ($validated['payment_method'] === 'balance') {
                if ($user->balance < $validated['amount']) {
                    // Record failed transaction
                    $failedTransaction = Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'purchase',
                        'game_name' => $validated['game_name'],
                        'item_name' => $validated['item_name'],
                        'amount' => $validated['amount'],
                        'status' => 'failed',
                        'payment_method' => $validated['payment_method'],
                        'transaction_code' => 'GP-' . strtoupper(Str::random(8)),
                        'failure_reason' => 'Saldo tidak mencukupi'
                    ]);

                    return response()->json([
                        'message' => 'Gagal: Saldo tidak mencukupi untuk melakukan pembelian ini.',
                        'transaction' => $failedTransaction->load('user'),
                        'current_balance' => $user->balance,
                        'required_amount' => $validated['amount'],
                        'shortage' => $validated['amount'] - $user->balance
                    ], 422);
                }

                $user->balance -= $validated['amount'];
                $user->save();
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'game_name' => $validated['game_name'],
                'item_name' => $validated['item_name'],
                'amount' => $validated['amount'],
                'status' => $validated['payment_method'] === 'balance' ? 'completed' : 'pending',
                'payment_method' => $validated['payment_method'],
                'transaction_code' => 'GP-' . strtoupper(Str::random(8))
            ]);

            return response()->json([
                'message' => $validated['payment_method'] === 'balance' 
                    ? 'Pembelian berhasil!'
                    : 'Pembelian sedang diproses!',
                'transaction' => $transaction->load('user'),
                'new_balance' => $user->balance
            ]);
        });
    }
}
