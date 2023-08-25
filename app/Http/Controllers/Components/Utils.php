<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;

class Utils extends Controller
{
    public static function verifyCredit($credit, $return_period, $pay_amount)
    {
        $monthly_tax = (float) $credit / 100 * 7.9 / 12;
        $total_amount = $credit + ((float) $monthly_tax * (int) $return_period);

        return $total_amount === $pay_amount;
    }
}