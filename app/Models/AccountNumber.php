<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasMany(CardNumber::class);
    }
}
