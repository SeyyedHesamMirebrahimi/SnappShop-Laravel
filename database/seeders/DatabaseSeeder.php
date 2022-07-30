<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AccountNumber;
use App\Models\CardNumber;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $myBankCardNumbers = range(111111, 9999999);
        User::factory(5)->create();
        foreach (User::all() as $user) {
            for ($x = 1; $x <= 2; $x++) {
                $accountNumber = AccountNumber::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'account_number' => random_int(11111111111, 99999999999)
                ]);
                foreach (AccountNumber::where(['user_id' => $user->id])->get() as $accountNumberObject) {
                    for ($x = 0; $x < 2; $x++) {
                        $card = CardNumber::create([
                            'cart_number' => $myBankCardNumbers[$x],
                            'account_number_id' => $accountNumberObject->id
                        ]);
                        unset($myBankCardNumbers[$x]);
                        $trans= \App\Models\Transaction::create([
                            'card_number_id' => $card->id,
                            'transaction_type' => 1,
                            'price' => random_int(1000000 , 50000000),
                            'description' => 'واریز وجه اولیه برای تست',
                        ]);
                        $accountNumber->balance = $accountNumber->balance + $trans->price;
                        $accountNumber->update();
                    }
                    $myBankCardNumbers = array_values($myBankCardNumbers);
                }
            }
        }
        Fee::create(['type' => 'transfer' , 'price'  =>'500']);
    }
}
