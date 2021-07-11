<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-12">  
   <h4>Auction Result</h4>  
   @if($auction->winner_bid)
    <p class="text-success">Congrats, Buyer already selected a winner</p>
   @endif
   <table id="itemTable" class="table order-list">
    <thead>
        <tr>
            <th>Bidder</th>
            <th>Amount</th>
            @if(!$auction->winner_bid)
            <th>Select Winner</th>
            @endif
        </tr>
    </thead>
    <tbody id="winnerbids">  
    @foreach ($bidlist as $key => $bid)
    <tr>
      <td>Seller {{$bid->id}} - {{$bid->username}}</td>
      <td>AED {{$bid->bid_amount}}</td>
      @if(!$auction->winner_bid)
      <td>
      <form method="post" action="{{ route('savewinner') }}" onsubmit="return confirmBidder();">
       @csrf
        <input type="hidden" name="auction_id" id="auction_id" class="form-control" value={{ $auction->id }}>
        <input type="hidden" name="winner_bid" id="winner_bid" class="form-control" value={{ $bid->id }}>
        <button type="submit"  class="btn btn-secondary">Choose</button>
      </form>
      </td>
      @endif
    </tr>
    @endforeach  
    </tbody>   
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>