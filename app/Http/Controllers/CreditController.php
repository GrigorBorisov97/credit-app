<?php

namespace App\Http\Controllers;

use App\Models\Credits;
use Illuminate\Http\Request;
use App\Http\Controllers\Components\Utils;


class CreditController extends Controller
{
    public function add(Request $request) {
        $name = $request->input('userName');
        $total_amount = $request->input('totalAmount');
        $monthly_tax = $request->input('monthlyPayment');
        $credit_amount = $request->input('creditAmount');
        $return_period = $request->input('returnPeriod');
        
        // Verify credit amount
        if (!Utils::verifyCredit($credit_amount, $return_period, $total_amount)) {
            abort(400, 'Bad params');
        }

        Credits::create([
            'name' => $name,
            'amount' => $total_amount,
            'monthly_tax' => $monthly_tax,
            'return_period' => $return_period,
        ]);
    }

    public function payOnCredit(Request $request)
    {
        $credit_id = (int) $request->input('userPayment');
        $credit = Credits::where(['id' => $credit_id])->first();

        $payment_amount = (float) $request->input('paymentAmount');

        if ($credit->amount < $payment_amount) {
            abort(400, 'Payment is greater than credit amount');
        }

        $credit->amount -= $payment_amount;
        $credit->save();
    }
}
