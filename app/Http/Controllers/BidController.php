<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\Bid;
use Validator,Response,File;
use Carbon\Carbon;
use DB;
use Mail;
use Config;
// use App\Events\MyPusher;
use App\Events\NewBid;

class BidController extends Controller
{   

    protected $bids_allowed;
    protected $bid_reduction;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->bids_allowed = 5;
        $this->bid_reduction = 50;
        $this->bidding_time = 2; // mins/user
    }  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now =  new Carbon();  
        $auctions = [];
        $query = DB::table('auctions as a')
                ->join('bids as b', 'b.auction_id', '=', 'a.id')
                ->select('a.*')
                ->where('b.user_id','=', auth()->user()->id);      

        $auctions = $query->distinct()->get();

        return view('bids.index', compact('auctions'));  
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewSellerAuction($id)
    {
        //
        $auction  = Auction::where('reference_no', '=',$id)->first();
        $auctiontiming = $this->lastAuctionTime($auction->id);

        // for bidders initial values
        $bidoffers = $this->initialBids($auction->id);

        //Bid History
        $bidHistory = $this->bidHistory($auction->id);

        // find lowest value
        $lowestvalue = $this->lowestBid($auction->id);
        $lowestvalue = $lowestvalue - $auction->min_step;

        // num of allowed count of user bidding 
        $bidAllowed = 0; $view = ''; $bidRoundHistory = [];
        if($auction->bid_type == 'continuous'){
            $view = 'bids.sellercontinuousauction';

            switch ($auctiontiming->order) {
                case 1:
                    $allowed = 3;
                    break;
                case 2:
                    $allowed = 4;
                    break;
                case 3:
                    $allowed = 5;
                    break;
            }

            //$this->totalAuctionAllowed($auction->id);      
        }else{
            switch ($auctiontiming->order) {
                case 1:
                    $allowed = 2;
                    break;
                case 2:
                    $allowed = 3;
                    break;
                case 3:
                    $allowed = 4;
                    break;
            }
            $view = 'bids.sellerroundauction';

            foreach ($bidHistory as $bid) {
                $bidRoundHistory[$bid->turn][$bid->user_id] = $bid->bid_amount;
            }
        }
        $userbcount = $this->userBidCount($auction->id);  
        $bidAllowed = ($allowed - $userbcount);
        $bidAllowed = ($bidAllowed < 0)? 0: $bidAllowed;


        // auction participants
        $participants = $this->auctionParticipants($auction->id);

        return view($view, compact('auction', 'bidoffers', 'auctiontiming', 'lowestvalue', 'bidAllowed', 'bidHistory', 'participants', 'bidRoundHistory'));
    }
    
    // Update Bid
    public function updateLastBid(Request $request)
    {   
        $this->validate($request, [
            'auction_id' => 'required',
            'user_id'    => 'required',
            'bid_amount' => 'required'
        ]);

        $insert = $request->all(); 
       
        // user last bid
        $mylastbid = $this->userLastBid($insert['auction_id']);

        /****** Check for limit *******/
        $auctiontiming = $this->lastAuctionTime($insert['auction_id']);
        // num of allowed count of user bidding 
        $bidAllowed = 0; 
        if($insert['bid_type'] == 'continuous'){

            switch ($auctiontiming->order) {
                case 1:
                    $allowed = 3;
                    break;
                case 2:
                    $allowed = 4;
                    break;
                case 3:
                    $allowed = 5;
                    break;
            }
    
        }else{
            switch ($auctiontiming->order) {
                case 1:
                    $allowed = 2;
                    break;
                case 2:
                    $allowed = 3;
                    break;
                case 3:
                    $allowed = 4;
                    break;
            }
        }
        $userbcount = $this->userBidCount($insert['auction_id']);
       // $allowed = $this->totalAuctionAllowed($insert['auction_id']);
        $bidAllowed = ($allowed - $userbcount);
        $bidAllowed = ($bidAllowed < 0)? 0: $bidAllowed;

        
        if($bidAllowed <= 0)
        {               
            return response()->json(array(
                'success' => false,
                'errors' => 'You have reached the limit of bidding on this round'

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        /****** Low bid than previous *******/
        $lowestvalue = $this->lowestBid($insert['auction_id']);

        $auc_data = Auction::find($insert['auction_id']);
        $lowestbid = $lowestvalue - $auc_data->min_step;

        if($insert['bid_amount'] > $lowestbid)
        {               
            return response()->json(array(
                'success' => false,
                'errors' => 'Your bid value is higher'

            ), 400); // 400 being the HTTP code for an invalid request.
        }
      
        $input = [
            'auction_id' => $insert['auction_id'],
            'user_id'    => $insert['user_id'],
            'bid_amount' => $insert['bid_amount'],
            'turn'       => $auctiontiming->order,
            'status'     => 1
        ];
        $bid = Bid::create($input);

         // want to broadcast NewMessageNotification event
        if($insert['bid_type'] == 'continuous'){            
            // event(new MyPusher($bid));
            event(new NewBid($bid));
        }else{
            //reverse         
            event(new NewBid($input));
        }
        

        $nextbid = $bidAllowed -1;
        $arData = ['allowedbid' => ($nextbid >0)? $nextbid: 0,
                   'currentuser'=> auth()->user()->id   
                  ];
       
        if($bid){
           return response()->json(array('success' => true, 'success'=>'You have successfully updated a bid.', 'data' => $arData), 200);
        }
        
        return response()->json(array(
                'success' => false,
                'errors' => 'There is an error occured while updating an order.'

            ), 400); // 400 being the HTTP code for an invalid request.
    }

    public function saveWinner(Request $request)
    {
        $input = $request->all();

        $auction = Auction::find($input['auction_id']);

        $auction->update([
            'winner_bid' => $input['winner_bid'],
            'status'     => 1
            ]);

        // send result to all participants
       // $this->sendMailByResult($input['auction_id']);
        
        return redirect()->back()
                        ->with('success','Congrats, successfully finished the auction !');
    }

    public function sendMailByResult($id){

        // Send email to all participants
        $auction =  DB::table('auctions as a')
                    ->leftJoin('bids as b', 'a.winner_bid', '=', 'b.id')
                    ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
                    ->select('u.name as winner', 'b.bid_amount as closed_amount', 'a.*')
                    ->where('a.id', '=', $id)
                    ->get();

        $auction = $auction[0];
  
        $data = array("name"=> 'Seller', 
                    "winner" => $auction->winner, 
                    "amount" => $auction->closed_amount, 
                    "title" => $auction->title
                );
        $invitees = DB::table('auction_participants')->where('auction_id', $id)->get();

        foreach ($invitees as $invite) {

            $email = $invite->invite_email;
            
            Mail::send('emails.winner', $data, function($message) use ($email) {
                $message->to($email)->subject('Winner Of Auction');

                $message->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
            });
        }
    }

    public function initialBids($auction_id){
        return Bid::where('auction_id', '=', $auction_id)
                ->where('turn', '=', 0)->orderBy('id', 'asc')->get();
        
    }

    public function lowestBid($auction_id){
        return Bid::where('auction_id', '=', $auction_id)->min('bid_amount');
        
    }

    public function userBidCount($auction_id){
        return Bid::where('auction_id', '=', $auction_id)
                ->where('user_id', '=', auth()->user()->id)->count();
        
    }

    public function userLastBid($auction_id){
        return Bid::where('auction_id', '=', $auction_id)
                ->where('user_id', '=', auth()->user()->id)
                ->latest()->first();
    }

    public function bidHistory($auction_id){ // all bids except initial
        return Bid::where('auction_id', '=', $auction_id)
                ->where('turn', '>', 0)->orderBy('id', 'asc')->get();
        
    }

    public function lastAuctionTime($id){
        return DB::table('auction_times')->where('auction_id', '=', $id)
                    ->latest()
                    ->first();
    }

    public function totalAuctionAllowed($id){
        return DB::table('auction_times')->where('auction_id', '=', $id)
                    ->sum('allowed_bid');
    }

    public function auctionParticipants($id){
        return DB::table('auction_participants as ap')
                    ->join('users as u', 'ap.user_id', '=', 'u.id')
                    ->where('ap.auction_id', '=', $id)
                    ->select('ap.*', 'u.name as fullname')
                    ->get();
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     
        $auction  = Auction::where('reference_no', '=',$id)->first();
     
        $bidhis =  DB::table('bids as b')
                  ->join('users as u', 'u.id', '=', 'b.user_id')
                  ->select('b.*', 'u.name as username')
                  ->where('b.turn','>', 0)
                  ->where('b.auction_id', '=', $auction->id)
                  ->orderBy('b.bid_amount', 'ASC')
                  ->get();

      
      return view('bids.show', compact('auction', 'bidhis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
