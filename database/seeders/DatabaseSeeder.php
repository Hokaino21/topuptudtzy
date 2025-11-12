<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100000 // Starting balance 100K
        ]);

        // Create some test transactions
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => 50000,
            'status' => 'completed',
            'payment_method' => 'bank_transfer',
            'transaction_code' => 'TU-' . strtoupper(uniqid())
        ]);

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'game_name' => 'Mobile Legends',
            'item_name' => 'Diamond Pack L',
            'amount' => 25000,
            'status' => 'completed',
            'payment_method' => 'balance',
            'transaction_code' => 'GP-' . strtoupper(uniqid())
        ]);

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'game_name' => 'Free Fire',
            'item_name' => 'Diamond Pack M',
            'amount' => 15000,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'transaction_code' => 'GP-' . strtoupper(uniqid())
        ]);
    }
}
