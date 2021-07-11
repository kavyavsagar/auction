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

    }

    public function sellerAuctionBids()
    {
        // all seller bids
        $now =  new Carbon();  
        $auctions = [];
        $query = DB::table('bids as b')
                ->join('auctions as a', 'b.auction_id', '=', 'a.id')
                ->join('users as u', 'b.user_id', '=', 'u.id')
                ->select('u.name as username', 'a.title', 'a.bid_type', 'a.start_price', 'a.start_time', 'b.bid_amount', 'b.created_at', 'b.auction_id as auctionid')
                ->where('b.user_id','=', auth()->user()->id);      

        $auctions = $query->get();

        return view('bids.sellerbids', compact('auctions'));   
    } 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'auction_id' => 'required',
            'user_id'    => 'required',
            'bid_amount' => 'required',
            'terms'      => 'required'
        ]);
    
        $input = $request->all();

        $insert = [
            'auction_id' => $input['auction_id'],
            'user_id'    => $input['user_id'],
            'bid_amount' => $input['bid_amount']
        ];

        if ($request->hasfile('file_doc')) {
            $file = $request->file('file_doc');          

            $filename = $input['auction_id'].'-'.$input['user_id'].'-'.time().'.'.$file->getClientOriginalExtension();                
            $path = $file->storeAs('sellerbid', $filename, 'public');
            $fpath = 'sellerbid/'.$filename;

            $insert['file_doc'] = $fpath;            
        }

        $bid = Bid::create($insert);

        return redirect()->route('bids.show', $input['auction_id'])
                        ->with('success','Congrats, You have made a starting bid !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        $arrData = $this->fetchAuction($id);

        $auction = $arrData['auction'];
        $bidlist = $arrData['bidlist'];
        $auctionExpires = $arrData['auctionExpires'];
        $auctionOrder = $arrData['auctionOrder'];
        $roundStartime = $arrData['bid_startime'];
        
        $biddersUniq = $arrData['bidderslist'];

        $duration_per_user = $this->bidding_time;
        
        return view('bids.sellerlivebid', compact('auction', 'bidlist', 'auctionExpires',
         'auctionOrder', 'biddersUniq', 'roundStartime', 'duration_per_user')); 
    }

    public function updateLastBid(Request $request)
    {   
        $this->validate($request, [
            'auction_id' => 'required',
            'user_id'    => 'required',
            'bid_amount' => 'required'
        ]);

        $insert = $request->all();        
        /****** Check for limit *******/
        $count = Bid::where('auction_id', '=', $insert['auction_id'])
                ->where('user_id', '=', $insert['user_id'])
                ->count();  

        $lastTimeRec = $this->lastAuctionTime($insert['auction_id']);
        
        //$bidAllowed = $this->bids_allowed;
        if($insert['auction_type'] == 'continuous'){
            $bidAllowed = 3;
            switch($lastTimeRec->order){
                case 1:
                    $bidAllowed = 3;break;
                case 2:
                    $bidAllowed = 4;break;
                case 3:
                    $bidAllowed = 5;break;
            }
        }else if($insert['auction_type'] == 'round'){
            $bidAllowed = 1;
            switch($lastTimeRec->order){
                case 1:
                    $bidAllowed = 1;break;
                case 2:
                    $bidAllowed = 2;break;
                case 3:
                    $bidAllowed = 3;break;
            }
        }

        if($count > $bidAllowed)
        {               
            return response()->json(array(
                'success' => false,
                'errors' => 'You have reached the limit('.$bidAllowed.') of bidding on this round'

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        /****** Low bid than previous *******/
        $lastrow = Bid::where('auction_id', '=', $insert['auction_id'])
                ->orderBy('bid_amount', 'ASC')->first();

        if($insert['bid_amount'] > $lastrow->bid_amount )
        {               
            return response()->json(array(
                'success' => false,
                'errors' => 'Your should bid with lower amount than existing bids'

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        
        $auc_data = $this->fetchAuction($insert['auction_id']);
        $this->bid_reduction = $auc_data['min_step'];
        
        /****** Check for reduction *******/
        $diff = ($lastrow->bid_amount - $insert['bid_amount']);

        if($diff > 0 && $diff < $this->bid_reduction){
            return response()->json(array(
                'success' => false,
                'errors' => 'Your are allowed to enter bid with reduction of AED'.$this->bid_reduction

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        
        $bid = Bid::create($insert);

        
        // want to broadcast NewMessageNotification event
        // event(new MyPusher($auc_data['bidlist']));
        event(new NewBid($auc_data['bidlist']));
       
        if($bid){
           return response()->json(array('success' => true, 'success'=>'You have successfully updated a bid.'), 200);
        }
        
        return response()->json(array(
                'success' => false,
                'errors' => 'There is an error occured while updating an order.'

            ), 400); // 400 being the HTTP code for an invalid request.
    }

    public function buyerLiveAuction($id)
    {

        $arrData = $this->fetchAuction($id);

        $auction = $arrData['auction'];
        $bidlist = $arrData['bidlist'];
        $auctionExpires = $arrData['auctionExpires'];
        $auctionOrder = $arrData['auctionOrder'];
        

        return view('bids.buyerliveauction', compact('auction', 'bidlist', 'auctionExpires', 'auctionOrder')); 
    }

    public function fetchAuction($id){

        $auction    = Auction::find($id);
        $bidderslist = [];
        $sql   = Bid::where('auction_id', '=', $id);

        if($auction->bid_type == 'round'){
            $sql->orderBy('bid_amount', 'ASC');
        }else{
            $sql->orderBy('created_at', 'ASC');
        }
        $bidlist =  $sql->get();
                
        $bidderslist =  $sql->select('user_id', 'bid_amount')->distinct()->get();

        $lastRec    = $this->lastAuctionTime($id);

        $auctionExpires = false;
        //Gulf ST
        $now = \Carbon\Carbon::now();
        $now = strtotime($now);// + 60*60*4;
        if(strtotime($auction->end_time) < $now){
            $auctionExpires = true;
        }
        $auctionOrder = $lastRec->order;

        return ['auction'       => $auction, 
                'bidlist'       => $bidlist, 
                'auctionExpires'=> $auctionExpires, 
                'auctionOrder'  => $auctionOrder,
                'bidderslist'   => $bidderslist,
                'bid_startime'=> $lastRec->startime   
                ];
    }

    public function lastAuctionTime($id){

        $lastRec = DB::table('auction_times')->where('auction_id', '=', $id)
                    ->orderBy('id', 'DESC')
                    ->first();

        return $lastRec;
    }

    public function chooseWinner($id)
    {
        $auction =  DB::table('auctions as a')
                    ->leftJoin('bids as b', 'a.winner_bid', '=', 'b.id')
                    ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
                    ->select('u.name as winner', 'b.bid_amount as closed_amount', 'a.*')
                    ->where('a.id', '=', $id)
                    ->get();
        $auction = $auction[0];

        $bidlist    = DB::table('bids as b')
                        ->join('users as u', 'b.user_id', '=', 'u.id')
                        ->select('u.name as username', 'b.id', 'b.bid_amount', 'b.created_at')
                        ->where('auction_id', '=', $id)
                        ->orderBy('bid_amount', 'ASC')
                        ->get();

        //Bid::where('auction_id', '=', $id)->orderBy('bid_amount', 'ASC')->get();   

        $auctionExpires = true;
        //Gulf ST
        $now = \Carbon\Carbon::now();
        $now = strtotime($now);// + 60*60*4;       
        // if auction not end
        if(strtotime($auction->end_time) > $now){
            $auctionExpires = false;
            return redirect()->route('liveauction', $id)
                        ->with('success','Auction not finished');
        }

        return view('bids.choosewinner', compact('auction', 'bidlist', 'auctionExpires')); 
    }

    public function saveWinner(Request $request)
    {
        $input = $request->all();

        $auction = Auction::find($input['auction_id']);

        $auction->update([
            'winner_bid' => $input['winner_bid']
            ]);

        // send result to all participants
        $this->sendMailByResult($input['auction_id']);
        
        return redirect()->back()
                        ->with('success','Congrats, You have done with bidding !');
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
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
