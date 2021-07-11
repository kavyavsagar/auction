<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Str;
use App\Models\Auction;
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
    public $continues_times;
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
        $this->continues_times = 3; // 3 continues rounds on auction
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
         // 'reference_no' => 'required',
          'description' => 'required',
          'start_price' => 'required',
          'budget'      => 'required',
          'min_step'    => 'required',
          'start_time'  => 'required',
        ]);

        if(count(array_filter($request->input('item')[0])) == 0) {
         
          $validator->after(function($validator) {            
              $validator->errors()->add('item', 'Required item lists');
          });
        }

        if(count(array_filter($request->input('invite_emails'))) == 0) {
         
          $validator->after(function($validator) {            
              $validator->errors()->add('invite_emails', 'Required participants');
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
            $duration = ($input['duration'])? $input['duration']: 0;

            // calculate duration, if it is round
            if($input['bid_type'] == 'round' && !empty($request->input('invite_emails'))){
              $numofpart = count(array_filter($request->input('invite_emails')));

              // duration = 3 min(interval) + (no of participants * 2 min) + (no of participants - 1) * 2 min(interval)
              $round = 3 + ($numofpart * 2) + ($numofpart - 1) * 2; 
              $minutes = $round * 3; // 3 rounds 

              $end = date("Y-m-d H:i:s", strtotime('+'.$minutes.' minutes', $start));
              // Find Duration
              $dteStart = new DateTime($input['start_time']);
              $dteEnd   = new DateTime($end); 
              $dteDiff  = $dteStart->diff($dteEnd); 
              $duration = $dteDiff->format("%H.%I");

            }else{
              // for continues
              $end = ($duration >0)? date("Y-m-d H:i:s", strtotime('+'.$duration.' hours', $start)): '';
            }
            
            $input['reference_no'] = Str::random(30);
            // insert basic details  
            $basicInsert = ['title'       => $input['title'],
                           'bid_type'     => $input['bid_type'],
                           'reference_no' => $input['reference_no'],
                           'description'  => $input['description'],
                           'user_id'      => $input['user_id'],
                           'budget'       => $input['budget'],
                           'min_step'     => $input['min_step'],
                           'start_price'  => $input['start_price'],
                           'start_time'   => date("Y-m-d H:i:s", $start),
                           'end_time'     => $end,
                           'duration'     => $duration                  
                           ];
                  
            $auction  = Auction::create($basicInsert);
            $auctionid = $auction->id;



            // save initial auction time
            $insert_tm = ['auction_id' => $auctionid,
                   'startime'  => date("Y-m-d H:i:s", $start),
                   'endtime'   => $end,
                   'duration'  => $duration,
                   'order'     => 1,
                   'created_at' => \Carbon\Carbon::now(), 
                   'updated_at' => \Carbon\Carbon::now()
                  ];
            $this->saveAuctionTime($insert_tm);

            // insert auction files
         //   $this->saveAuctionFiles($request, $auctionid);

            // insert auction items
            $this->saveAuctionItems($request, $auctionid);

            // insert auction participants
            $this->saveAuctionParticipants($request, $auctionid);

            return redirect()->route('auctions.index')
                            ->with('success','Your Auction Launched Successfully !');

        }catch(\Exception $exception){

            return redirect()->route('auctions.create')
                        ->with('error',$exception->getMessage());
        }

        
    }

    public function saveAuctionTime($insert){

        DB::table('auction_times')->insert($insert);

    }

    // insert auction files
    public function saveAuctionFiles($request, $auctionid){
       
        $insertFiles = [];
        if (!empty($request->hasfile('filenames'))) {

            foreach($request->file('filenames') as $file){

                $filename = $request->input('title').rand(1,15).'-' . time() . '.' . $file->getClientOriginalExtension();                
                $path = $file->storeAs('auction', $filename, 'public');
                $fpath = 'auction/'.$filename;

                $insertFiles[] = array('auction_id' => $auctionid, 
                                    'file_path'    => $fpath,  
                                    'created_at'   => \Carbon\Carbon::now(), 
                                    'updated_at'   => \Carbon\Carbon::now()
                                    );          
            }  
            $checkUpload = DB::table('auction_files')->insert($insertFiles);         
        }

    }


    // insert auction items
    public function saveAuctionItems($request, $auctionid) {
        
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
    public function saveAuctionParticipants($request, $auctionid){

        $insertInvites = []; $invitees = [];
        if (!empty($request->input('invite_emails'))) {
            
            foreach($request->input('invite_emails') as $invite){
                if(!$invite) continue;

                do {
                    $token = Str::random(20);
                } while (DB::table('auction_participants')->where('token', $token)->first());


                $invitees[$token] = $invite;

                $insertInvites[] = array('auction_id' => $auctionid, 
                                    'invite_email'    => $invite,  
                                    'token'           => $token,  
                                    'created_at'      => \Carbon\Carbon::now(), 
                                    'updated_at'      => \Carbon\Carbon::now()
                                    );          
            }  
            $checkInvite = DB::table('auction_participants')->insert($insertInvites);   

            // Invitation email send
            //$this->sentInvitationMail($invitees, $request);
        }
    } 

    public function sentInvitationMail($invitees, $request){

        $data = array("name"=> 'Seller', 
            "startdate" => $request->input('start_time'), 
            "title" => $request->input('title')
            );

        foreach ($invitees as $token => $email) {

            $data['url'] = URL::temporarySignedRoute(     
                'joinauction', now()->addMinutes(500), ['token' => $token]
            );
            
            Mail::send('emails.invitations', $data, function($message) use ($email) {
                $message->to($email)->subject('Invitation Email Of Auction');


                $message->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
            });
        }

    }

    public function acceptInvite($token){
        $auct_part = DB::table('auction_participants')->where('token', $token)->first();
       
        // updated
        $updated = DB::table('auction_participants')
                      ->where('id', $auct_part->id)
                      ->update(['updated_at' => \Carbon\Carbon::now(),
                                'status' => 1]);

        
        return redirect()->route('viewauction', $auct_part->auction_id)
                        ->with('success','You have joined successfully !');
    }

    public function updateAuctionTime(Request $request)
    {

        $this->validate($request, [
            'auction_id' => 'required',
            'auction_type'  => 'required'
        ]);

        $input = $request->all(); 

        $lastRec = DB::table('auction_times')->where('auction_id', '=', $input['auction_id'])
                    ->orderBy('id', 'DESC')
                    ->first();
        
        $start = strtotime($lastRec->endtime); 
        
        if($input['auction_type'] == 'continuous'){

          if($lastRec->order >= $this->continues_times ){
              // not allowed for another round
              return response()->json(array(
                  'success' => false,
                  'errors' => 'You have reached the limit('.$this->continues_times.') of auction. So you cannot extend auction anymore.'

              ), 400); // 400 being the HTTP code for an invalid request.
          }
          //add 10 minutes to time        
          $end =  date("Y-m-d H:i:s", strtotime('+'.$this->continues_extra." minutes", $start));
          $durat = '00.'.$this->continues_extra;

        }else if($input['auction_type'] == 'round'){

          if($lastRec->order >= $this->round_times){
              // not allowed for another round
              return response()->json(array(
                  'success' => false,
                  'errors' => 'You have reached the limit('.$this->round_times.') of auction. So you cannot extend auction anymore.'

              ), 400); // 400 being the HTTP code for an invalid request.
          }

          $tm = explode(".",$lastRec->duration);
          $h = ($tm[0] >0)? $tm[0]: 0;
          $m = ($tm[1] >0)? $tm[1]: 0;

          //add 10 minutes to time                  
          $end =  date("Y-m-d H:i:s", strtotime('+'.$h.' hours +'.$m.' minutes', $start));
          $durat = $lastRec->duration;
        }

        $insert = ['auction_id' => $input['auction_id'],
                   'startime'  => date("Y-m-d H:i:s", $start),
                   'endtime'   => $end,
                   'duration'  => $durat,
                   'order'     => ($lastRec->order + 1),
                   'created_at' => \Carbon\Carbon::now(), 
                   'updated_at' => \Carbon\Carbon::now()
                ];

        $this->saveAuctionTime($insert);

        Auction::find($input['auction_id'])->update([
            'end_time' => $end                
            ]);

        return response()->json(array('success' => true, 'success'=>'You have successfully updated a auction time.'), 200);
        
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    public function sellerView($id)
    {
        // 
        $auction      = Auction::find($id);
        $auctionItem  = DB::table('auction_items')->where('auction_id', '=', $id)->get();
        $auctionFiles = DB::table('auction_files')->where('auction_id', '=', $id)->get();
        $auctionPart  = DB::table('auction_participants')->where('auction_id', '=', $id)->get();

        $isBid = Bid::where('auction_id', '=', $id)
                    ->where('user_id', '=', auth()->user()->id)
                    ->count(); 


        return view('auctions.joinbid', compact('auction', 'auctionItem', 'auctionFiles', 'auctionPart', 'isBid'));
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

        if($count >= $this->bids_allowed)
        {               
            return response()->json(array(
                'success' => false,
                'errors' => 'You have reached the limit('.$this->bids_allowed.') of bidding'

            ), 400); // 400 being the HTTP code for an invalid request.
        }
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
