<div class="card">
  <div class="card-header"><h5>Bid History</h5></div>
  <div class="card-body">
  <div class="row">
    <div class="col-md-8">
    <!--   @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
      @endif  -->
      <table id="itemTable" class="table order-list">
        <tbody id="livebid_his">  
        @if(count($bidHistory) > 0)
        @foreach ($bidHistory as $key => $bid)
          @php
            $time = strtotime($bid->created_at);
          @endphp
        <tr>
          <td>Seller {{$bid->user_id}} @if($bid->user_id == Auth::user()->id)(YOU)@endif</td>
          <td>AED {{$bid->bid_amount}} </td>
          <td>{{ date("d-m-Y H:i:s", $time) }}</td>         
        </tr>
        @endforeach 
        @else
        <tr>
          <td colspan="3"></td>
        </tr>
        @endif 
        </tbody>   
      </table>
    </div>
    <div class="col-md-4">    
      <div class="dis_bidding d-none">   
        <div id="error_msg" class="alert alert-danger d-none"></div>
    
        <dl class="row">
          <dd class="col-sm-12">You can submit value lesser or equal to <b class="lowestbid">AED {{$lowestvalue}}</b></dd>
          <dd class="col-sm-12">You are allowed to bid on <b class="allowedbid">{{$bidAllowed}}</b> times</dd>
        </dl>
        <input type="hidden" id="min_step" value="{{ $auction->min_step }}">


        <form id="bidForm" method="post">
          <input type="hidden" name="lowest_bid" id="lowest_bid" value="{{$lowestvalue}}">
          <input type="hidden" name="bid_allowed" id="bid_allowed" value="{{$bidAllowed}}">
          
          <input type="hidden" name="auction_id" id="auction_id" class="form-control" 
          value={{ $auction->id }}>
          <input type="hidden" name="user_id" id="user_id" class="form-control" 
          value={{ Auth::id() }}>
          <input type="hidden" name="bid_type" id="bid_type" class="form-control" value={{ $auction->bid_type }}>
       
          <div class="form-group">                      
            {!! Form::number('bid_amount', null, array('placeholder' => 'Enter Bid Amount','class' => 'form-control', 'id' => 'bid_amount')) !!}             
          </div>

          <div class="form-group text-center"> 
            <div id="loader"></div>         
            <button type="submit" class="btn btn-primary ">Submit</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">&nbsp;</div>
  </div>
  </div>
</div>