<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_number',
        'account_number_id',
    ];

    public function accountNumber()
    {
        return $this->belongsTo(AccountNumber::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
