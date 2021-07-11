<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Auction;
use App\Models\User;
use Validator,Response,File;
use Carbon\Carbon;
use DB;
use Mail;
use App\Models\Bid;
use Config;
use DateTime;

class AuctionController extends Controller
{   
    public $continues_extra;
    public $continues_times_limit;
    public $round_times;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->continues_extra = 10; // 10 minutes extend
        $this->continues_times_limit = 3; // 3 continues rounds on auction
        $this->round_times = 3;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     
        //all auctions
        $data = Auction::where('user_id', '=', auth()->user()->id)                
                ->orderBy('start_time', 'DESC')
                ->get();

        return view('auctions.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data = [];

        return view('auctions.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     
        $validator = Validator::make(request()->all(), [
          'title' => 'required',
          'description' => 'required',
          'start_price' => 'required',
          'min_step'    => 'required',
          'start_time'  => 'required',
        ]);

        if(count(array_filter($request->input('item')[0])) == 0) {
         
          $validator->after(function($validator) {            
              $validator->errors()->add('item', 'Required item lists');
          });
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->all();   // dd($input);

        try{
            $start = strtotime($input['start_time']); 
            $duration = ($input['duration'])? $input['duration']: 2;// round auction, 2 min start delay

            // calculate duration
            //if($input['bid_type'] == 'continuous'){
              // for continues
            $end = ($duration >0)? date("Y-m-d H:i:s", strtotime('+'.$duration.' minutes', $start)): '';
            //}
            $tendor_end = date("Y-m-d H:i:s", strtotime('-1 minutes', $start));
            $reference_no = Str::random(30);

            // insert basic details  
            $basicInsert = ['title'       => $input['title'],
                           'bid_type'     => $input['bid_type'],
                           'reference_no' => $reference_no,
                           'description'  => $input['description'],
                           'user_id'      => $input['user_id'],                   
                           'min_step'     => $input['min_step'],
                           'start_price'  => $input['start_price'],
                           'start_time'   => date("Y-m-d H:i:s", $start),
                           'end_time'     => $end,
                           'duration'     => $duration,
                           'tendor_start' => Carbon::now()->addMinutes(90),
                           'tendor_end'   => $tendor_end
                          ];
              
            $auction  = Auction::create($basicInsert);
            $auctionid = $auction->id;

            // save initial auction time 
            $insertTime = ['auction_id' => $auctionid,
                      'title'     => $basicInsert['bid_type'].' 1',
                      'startime'  => $basicInsert['start_time'],
                      'endtime'   => $basicInsert['end_time'],
                      'duration'  => $basicInsert['duration'],
                      'order'     => 1,
                      'allowed_bid' => ($basicInsert['bid_type'] == 'continuous')? 3: 1, // no of allowed bid per user
                      'created_at' => \Carbon\Carbon::now(), 
                      'updated_at' => \Carbon\Carbon::now()
                    ];         
            $this->saveAuctionTime($insertTime);

            // insert auction items
            $this->saveAuctionItems($request, $auctionid);


            return redirect()->route('auctions.show', $reference_no)
                            ->with('success','Your Auction Launched Successfully !');

        }catch(\Exception $exception){

            return redirect()->route('auctions.create')
                        ->with('error',$exception->getMessage());
        }

        
    }

    public function saveAuctionTime($insert){

      return DB::table('auction_times')->insert($insert);
    }

    // insert auction items
    public function saveAuctionItems($request, $auctionid) 
    {
        
        $insertItems = [];
        if (!empty($request->input('item'))) {

            foreach($request->input('item') as $k => $item){

                $fpath = '';

                if ($request->hasFile('item.'.$k.'.doc_path') ) {

                    $file = $request->file('item')[$k]['doc_path'];                    
                   
                    // generate a new filename. getClientOriginalExtension() for the file extension
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $path     = $file->storeAs('itemdocs', $filename, 'public');
                    $fpath    = 'itemdocs/'.$filename;
                } 

                $insertFiles[] = array('auction_id' => $auctionid, 
                                    'brief'       => $item['brief'], 
                                    'quantity'    => $item['quantity'], 
                                    'doc_path'    => $fpath,  
                                    'created_at'  => \Carbon\Carbon::now(), 
                                    'updated_at'  => \Carbon\Carbon::now()
                                    ); 
                 

            }  
           // dd($insertFiles);
            $checkItems = DB::table('auction_items')->insert($insertFiles);         
        }

    }

     // insert auction participants
    public function saveAuctionParticipants(Request $request)
    {

        $validator = Validator::make(request()->all(), [
          'auction_id' => 'required',
          'name' => 'required',
          'email' => 'required',
          'bid_amount'    => 'required'
        ]);
        $input = $request->all();

        $auction = Auction::find($input['auction_id']);

        if($input['bid_amount'] > $auction->start_price){
          $validator->after(function($validator) {            
              $validator->errors()->add('bid_amount', 'Bid Amount should be lesser than auction price');
          });
        }
        $now = Carbon::now()->addMinutes(90);
       
        // if($auction->tendor_end < $now) {

        //   $validator->after(function($validator) {            
        //       $validator->errors()->add('auction_id', 'Bidder registration time is expired');
        //   });
        // }
        if($input['email'] == auth()->user()->email){
          $validator->after(function($validator) {            
              $validator->errors()->add('email', 'Auctioner not allowed to join as bidder');
          });
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        // Get or Create new user
        $user = $this->getUserId($input);

        // Insert Participant
        $insertInvites = []; $invitees = []; $insertBid = [];

        do {
            $token = Str::random(20);
        } while (DB::table('auction_participants')->where('token', $token)->first());

        $insertInvites = array('auction_id'   => $input['auction_id'], 
                            'user_id'         => $user['user_id'],
                            'invite_email'    => $input['email'],  
                            'token'           => $token,  
                            'created_at'      => \Carbon\Carbon::now(), 
                            'updated_at'      => \Carbon\Carbon::now()
                            );          
             
        $checkInvite = DB::table('auction_participants')->insert($insertInvites); 

        // Insert first Bid
        $insertBid = $input;
        $insertBid['user_id'] = $user['user_id'];
        $bid = $this->createBid($insertBid);

        if($auction->bid_type == 'round'){
          // update auction duration and end time

          $duration = $auction->duration + (2 * 1); // 2min * no of participants
          $start = strtotime($auction->start_time);
          $end = ($duration >0)? date("Y-m-d H:i:s", strtotime('+'.$duration.' minutes', $start)): '';

          //update to auction 
          $auction->update(['end_time'    => $end,
                           'duration'     => $duration
                           ]);

          //update to auction times
          $times = DB::table('auction_times')
                    ->where('auction_id', $input['auction_id'])
                    ->where('order', 1)
                    ->update(['endtime'    => $end,
                              'duration'   => $duration,
                              'updated_at' => \Carbon\Carbon::now()
                            ]);
        }
        
        // Invitation email send
        $invitees = $insertInvites;
        $invitees['startdate']  = $auction['start_time'];
        $invitees['title']      = $auction['title'];
        $invitees['password'] = $user['password']? $user['password']: '';
        $invitees['name']     = $input['name'];
        $invitees['url']      = URL::temporarySignedRoute(     
                                  'joinauction', now()->addMinutes(500), ['token' => $token]
                              );

       // $this->sentInvitationMail($invitees);

       return redirect()->back()
                  ->with('success','Bidder Registered Successfully !');    

    } 

    public function sentInvitationMail($invitees)
    {

        $email = $invitees['invite_email'];

        // send mail
        Mail::send('emails.invitations', $invitees, function($message) use ($email) {
            $message->to($email)->subject('Invitation Email Of Auction');

            $message->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
        });

    }

    // CREATE or GET USER
    public function getUserId($input)
    {
      $password = '';

      // check user
      $user = User::where('email', $input['email'])->first();
      if(!$user){        
        $password = Str::random(10);
        
        // register and save user
        $user =  User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($password),
        ]);
      }

      $user_id = $user->id;

      return ['password' => $password, 'user_id' => $user_id];
    }

    // CREATE INITIAL BID
    public function createBid($input)
    {

      $insert = [
            'auction_id' => $input['auction_id'],
            'user_id'    => $input['user_id'],
            'bid_amount' => $input['bid_amount'],
            'turn'       => 0,
            'status'     => 0
          ];
      $bid = Bid::create($insert);
      return $bid;
    }

    // ACCEPT Auction Invitation
    public function acceptInvite(Request $request)
    {

      $validator = Validator::make(request()->all(), [
        'user_id'     => 'required',
        'auction_id'  => 'required',
        'user_token'  => 'required',
        'reference_no' => 'required'
      ]);

      if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->withInput();
      }

      $input = $request->all(); 

      // updated auction participants
      $partup = DB::table('auction_participants')
                    ->where('token', $input['user_token'])
                    ->where('auction_id', $input['auction_id'])
                    ->update(['updated_at' => \Carbon\Carbon::now(),
                              'status' => 1]);
      // updated initial bid
      $bidup = DB::table('bids')
                    ->where('user_id', $input['user_id'])
                    ->where('auction_id', $input['auction_id'])
                    ->update(['updated_at' => \Carbon\Carbon::now(),
                              'status' => 1]);


        return redirect()->route('sellerauction', $input['reference_no'])
                        ->with('success','You have joined successfully !');
    }

    // extend auction time
    public function updateAuctionTime(Request $request)
    {

        $this->validate($request, [
            'auction_id' => 'required',
            'bid_type'  => 'required'
        ]);

        $input = $request->all(); 

        $lastRec = DB::table('auction_times')->where('auction_id', '=', $input['auction_id'])
                    ->latest()
                    ->first();
        
        $start = strtotime($lastRec->endtime); 
        
        if($input['bid_type'] == 'continuous'){

          if($lastRec->order >= $this->continues_times_limit){
              // not allowed for another round
              // return response()->json(array(
              //     'success' => false,
              //     'errors' => 'You have reached the limit('.$this->continues_times_limit.') of auction. So you cannot extend auction anymore.'

              //), 400); // 400 being the HTTP code for an invalid request.
          }else{
            //add 10 minutes to time        
            $end =  date("Y-m-d H:i:s", strtotime("+".$this->continues_extra." minutes", $start));
            $durat = $this->continues_extra;
          }

        }else if($input['bid_type'] == 'round'){

          if($lastRec->order >= $this->round_times){ die('here');
          //     // not allowed for another round
          //     return response()->json(array(
          //         'success' => false,
          //         'errors' => 'You have reached the limit('.$this->round_times.') of auction. So you cannot extend auction anymore.'

          //     ), 400); // 400 being the HTTP code for an invalid request.
          }else{
            $durat = (int)$lastRec->duration;

            // $tm = explode(".",$lastRec->duration);
            // $m = (count($tm) >0)? $tm[0]: 0;
            $end =  date("Y-m-d H:i:s", strtotime("+".$durat." minutes", $start));
          }

        }
        // save auction extended time
        $insert = ['auction_id' => $input['auction_id'],
                   'title'      => $input['bid_type'].' '.($lastRec->order+1),
                   'startime'   => date("Y-m-d H:i:s", $start),
                   'endtime'    => $end,
                   'duration'   => $durat,
                   'order'      => ($lastRec->order + 1),
                   'allowed_bid'=> 1,
                   'created_at' => \Carbon\Carbon::now(), 
                   'updated_at' => \Carbon\Carbon::now()
                ];

        $this->saveAuctionTime($insert);
        // auction update
        $auction = Auction::find($input['auction_id']);
        $auction->update(['end_time' => $end]);

        // get lowest bid amount
        // $lowestvalue = Bid::where('auction_id', '=', $input['auction_id'])->min('bid_amount');

        // $ardata = $insert;
        // $ardata['lowestbid'] = $lowestvalue;


        return response()->json(array('success' => true, 'success'=>'You have successfully updated a auction time.', 'data' => $insert), 200);
        
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

      $auction  = Auction::where('reference_no', '=',$id)->first();
      $auctionItem  = DB::table('auction_items')->where('auction_id', '=', $auction->id)->get();
      
      $participants = DB::table('auction_participants as ap')
                      ->join('users as u', 'ap.user_id', '=', 'u.id')
                      ->select('u.name', 'u.email', 'ap.created_at', 'ap.token', 'u.id as userid')
                      ->where('ap.auction_id', '=', $auction->id)
                      ->get();

      $userids = $participants->pluck('userid')->toArray();

      $initialbids = Bid::where('auction_id', '=', $auction->id)
              ->whereIn('user_id', $userids)
              ->where('turn', 0)
              ->pluck('bid_amount', 'user_id')->toArray();

      $bidhis =  DB::table('bids as b')
                  ->join('users as u', 'u.id', '=', 'b.user_id')
                  ->select('b.*', 'u.name as username')
                  ->where('b.turn','>', 0)
                  ->where('b.auction_id', '=', $auction->id)
                  ->orderBy('b.bid_amount', 'ASC')
                  ->get();

      
      return view('auctions.show', compact('auction', 'auctionItem', 'participants', 'initialbids', 'bidhis'));

    }

    // Seller Join View
    public function sellerJoinAuction($token)
    {
        // 
        $participant = DB::table('auction_participants')
                        ->where('token', $token)
                        ->where('status','=', 0)
                        ->first();
        $errorFlag = false;
        if(!$participant){
          $errorFlag = true;
          return back()->withErrors('Token is not valid');
        } 
        $auction = Auction::find($participant->auction_id); 

        if($auction->start_time <= Carbon::now() ){
          $errorFlag = true;
          return back()->withErrors('Time expired, you are not allowed to join');
        }

        return view('auctions.joinauction', compact('auction', 'token', 'errorFlag'));
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
        DB::table('auction_files')->where('auction_id', '=', $id)->delete();
        DB::table('auction_items')->where('auction_id', '=', $id)->delete();        
        DB::table('auction_participants')->where('auction_id', '=', $id)->delete();  

        DB::table('bids')->where('auction_id', '=', $id)->delete(); 

        DB::table('auctions')->where('id', '=', $id)->delete();

        return redirect()->route('auctions.index')
                        ->with('success','Auctions deleted successfully');
    }
}
