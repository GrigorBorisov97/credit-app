<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credits;

class PageController extends Controller
{
    public function home()
    {
        $credits = Credits::get();

        return view('credit-home', [
            'credits' => $credits
        ]);
    }
}
