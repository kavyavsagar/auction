@include('bids.bidhistory')

<div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-6">
    <div class="form-group">
      <label for="bid_amount" class="mr-3">Your Final Budget</label>                      
      AED {{$auction->budget}}    
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6">    
  </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>

@if($auctionExpires && count($bidlist) > 0)    
  <div class="row">       
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group float-right">              
          <a href="{{ route('choosewinner', $auction->id) }}" class="btn btn-primary btn-lg">Choose Winner</a>
        </div>
      </div>
  </div>
@endif 
<div id="winner-btn"> </div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>