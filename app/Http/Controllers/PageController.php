<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credits;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function home()
    {
        $credits = DB::table('credits')
            ->select('credits.*', 'users.name')
            ->leftJoin('users', 'credits.user_id', '=', 'users.id')
            ->get()
            ->toArray();
        
        $credits = json_decode(json_encode($credits), true);

        return view('credit-home', [
            'credits' => $credits
        ]);
    }
}
