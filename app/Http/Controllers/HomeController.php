<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $auctions = Auction::where('user_id', '=', auth()->user()->id)  
                ->whereDate('start_time', '>', Carbon::now())              
                ->orderBy('start_time', 'DESC')
                ->get();

        return view('home', compact('auctions'));
    }
}
