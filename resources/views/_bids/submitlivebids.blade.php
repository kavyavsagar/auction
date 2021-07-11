@include('bids.bidhistory')

@if($auctionExpires )

<div class="card">
  <div class="card-header"><h5>Enter Your Bid Amount</h5></div>
  <div class="card-body">
  <div class="row">
    <div class="col-md-12">
      <div id="error_msg" class="text-danger d-none"></div>
    </div>
  </div>
  <div class="row">       
    <div class="col-xs-12 col-sm-12 col-md-6">
      <div class="form-group">                      
        {!! Form::number('bid_amount', null, array('placeholder' => 'Enter Bid Amount','class' => 'form-control', 'id' => 'bid_amount')) !!}     
        <p class="text-muted mt-2 small">Amount should be lesser than <b>{{$auction->start_price}} AED</b></p> 
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6">
      <div class="form-group "> 
          <div id="loader"></div>         
          <button type="submit" class="btn btn-primary">Submit</button>
      </div>
      
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">&nbsp;</div>
  </div>
  </div>
</div>
@endif
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>