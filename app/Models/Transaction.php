<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    use HasFactory;
    protected $fillable = [
        'card_number_id',
        'transaction_type',
        'price',
        'destination',

    ];

    public function cardNumber()
    {
        return $this->belongsTo(CardNumber::class);
    }
}
