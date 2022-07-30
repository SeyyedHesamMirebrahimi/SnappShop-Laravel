<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\MoneyTransferRequest;
use App\Models\CardNumber;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use App\SMS\KaveNegar;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;

class MoneyController extends BaseController
{
    public function send(MoneyTransferRequest $request)
    {
        $input = $request->all();
        $from = CardNumber::where(['cart_number' => $input['from']])->first();
        $fee = Fee::where(['type' => 'transfer'])->first()->price;
        $priceWithFee = $input['price'] + $fee;
        if (!$from->accountNumber->balance >= $priceWithFee) {
            return $this->sendError(__('messages.notenoughbalance'));
        }
        $mainTransAction = Transaction::create([
            'card_number_id' => $from->id,
            'transaction_type' => 0,
            'price' => $input['price'],
            'destination' => $input['to'],
            'description' => 'انتقال وجه'
        ]);

        $feeTransAction = Transaction::create([
            'card_number_id' => $from->id,
            'transaction_type' => 0,
            'price' => $fee,
            'description' => 'کارمزد انتقال وجه'
        ]);
        $from->accountNumber->balance = $from->accountNumber->balance - $priceWithFee;
        $from->accountNumber->update();
        $sms = new KaveNegar($from->accountNumber->user->mobile, __('sms.decrease', [
            'balance' => $from->accountNumber->balance,
            'cardNumber' => $from->cart_number,
            'accountNumber' => $from->accountNumber->account_number,
            'price' => $priceWithFee,
        ]));
        return $this->sendResponse([], __('messages.transaction_successful'));
    }

    /**
     * @throws \Exception
     */
    #[NoReturn] public function most_recent()
    {
        $start = new \DateTime('-10 Minutes');
        $data = DB::table('users')
            ->join('account_numbers', 'users.id', '=', 'account_numbers.user_id')
            ->join('card_numbers', 'account_numbers.id', '=', 'card_numbers.account_number_id')
            ->join('transactions', 'card_numbers.id', '=', 'transactions.card_number_id')
            ->select(
                DB::raw('count(account_numbers.user_id) as total'),
                'users.id',
//                'transactions.created_at'
            )
            ->groupBy('users.id')
            ->where('transactions.created_at', '>', $start->format('Y-m-d H:i:s'))
            ->get();
        $array = [];
        foreach ($data as $key => $datum) {
            $userId = $datum->id;
            $transactions = DB::table('transactions')
                ->join('card_numbers', 'transactions.id', '=', 'card_numbers.id')
                ->join('account_numbers', 'card_numbers.id', '=', 'account_numbers.id')
                ->join('users', 'account_numbers.id', '=', 'users.id')
                ->where('users.id' , $userId)
                ->select(
                    'transactions.price as price',
                    'transactions.description as description'
                )->get();
            $array[] = [
                'count' => $datum->total,
                'user' => User::find($userId)->name,
                'transactions' => $transactions
            ];
        }
        return $this->sendResponse($array, '');
    }
}
