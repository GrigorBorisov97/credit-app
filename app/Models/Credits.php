<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credits extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit_amount',
        'refund_amount',
        'monthly_tax',
        'return_period',
    ];
}
