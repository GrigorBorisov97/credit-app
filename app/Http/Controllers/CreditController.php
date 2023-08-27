<?php

namespace App\Http\Controllers;

use App\Models\Credits;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Components\Utils;
use Illuminate\Support\Facades\DB;


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

        // Check if user exists
        $user = User::where('name', $name)->first();

        if (is_null($user)) {
            $user = User::create(['name' => $name]);
        }

        // Check if user has more than 80 000bgn credits
        $credits_total_amount = DB::table('credits')
            ->where('credits.user_id', '=', $user->id)
            ->sum('credits.credit_amount');
        
        $credit_limit_exceeded = ((int) $credits_total_amount + $credit_amount) > 80000;

        if ($credit_limit_exceeded) {
            return response()->json([
                'status' => 'error',
                'message' => "Credit limit 80000 exceeded!",
            ]);
        }

        Credits::create([
            'user_id' => $user->id,
            'credit_amount' => $credit_amount,
            'refund_amount' => $total_amount,
            'monthly_tax' => $monthly_tax,
            'return_period' => $return_period,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Credit created successfully!",
        ]);
    }

    public function payOnCredit(Request $request)
    {
        $credit_id = (int) $request->input('userPayment');
        $credit = Credits::where(['id' => $credit_id])->first();

        $payment_amount = (float) $request->input('paymentAmount');

        if ($credit->refund_amount < $payment_amount) {
            $json_resp = [
                'status' => 'success',
                'message' => "Only $credit->refund_amount were charged!",
            ];
            $credit->refund_amount = 0.00;
        } else {
            $credit->refund_amount -= $payment_amount;
            $json_resp = [
                'status' => 'success',
                'message' => "Payment successful.",
            ];
        }
        
        $credit->save();
        return response()->json($json_resp);
    }
}
