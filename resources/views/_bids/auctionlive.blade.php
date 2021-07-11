<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>   
<div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
           <i class="far fa-file-alt mr-2"></i>
            Auction Details
          </h3>
        </div>   
        <!-- /.card-header -->
        <div class="card-body">   
          @php
            $start_time = date("j M Y, g:i a", strtotime($auction->start_time)); 
            $end_time = date("j M Y, g:i a", strtotime($auction->end_time)); 
          @endphp       
          <dl class="row">
<!--            <dt class="col-sm-4">Bid Type</dt><dd class="col-sm-8">
            @if($auction->bid_type == 'continuous')
              {{$auction->bid_type}} Auction
            @else
              Aunction in {{$auction->bid_type}}
            @endif
            </dd> -->
           <dt class="col-sm-4">Auction Title</dt>
           <dd class="col-sm-8"><h5>{{$auction->title}}</h5></dd>
          <!--  <dt class="col-sm-4">Reference No</dt><dd class="col-sm-8">{{$auction->reference_no}}</dd> -->
           <dt class="col-sm-4">Descripion</dt>
           <dd class="col-sm-8">{{$auction->description}}</dd>
           <dt class="col-sm-4">Start Price</dt>
           <dd class="col-sm-8">AED {{$auction->start_price}}</dd>
          <!--  <dt class="col-sm-4">Start Time</dt><dd class="col-sm-8"><span class="text-info">{{$start_time}}</span></dd>
           <dt class="col-sm-4">End Time</dt><dd class="col-sm-8"><span class="text-warning">{{$end_time}}</span></dd> -->
           <dt class="col-sm-4">Time Left</dt>
           <dd class="col-sm-8"><div id="countdown"></div> 
           </dd>

            @if(($auction->user_id == Auth::user()->id) && $auctionExpires) 
            <!-- If buyer & auction expires -->
            <dt class="col-sm-4">Auction Budget</dt><dd class="col-sm-8">AED {{$auction->budget}}</dd> 
            <dt class="col-sm-4">Auction Closing Price</dt><dd class="col-sm-8">@if($auction->closed_amount) AED {{$auction->closed_amount}}@endif</dd>
            <dt class="col-sm-4">Auction Winner</dt><dd class="col-sm-8">@if($auction->winner) {{$auction->winner}}@endif</dd>  
            @endif
          </dl>
        </div>
        <!-- /.card-body -->    
      </div>
  </div>
  </div>
 <div class="row">
  <div class="col-md-12">&nbsp;</div>
</div> 