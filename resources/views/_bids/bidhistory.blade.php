<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
  <div class="col-md-12">  
   <h4>Bid History</h4>  
   <input type="hidden" id="auth_user" value="{{Auth::user()->id}}">
   <table id="itemTable" class="table order-list">
    <thead>
        <tr>
          <th>Bid History</th>
          <th>Amount</th>
          <th>Time</th>
        </tr>
    </thead>
    <tbody id="livebid_his">  
    @foreach ($bidlist as $key => $bid)
      @php
        $time = strtotime($bid->created_at);
      @endphp
    <tr>
      <td>Seller {{$bid->user_id}} @if($bid->user_id == Auth::user()->id)(YOU)@endif</td>
      <td>AED {{$bid->bid_amount}} </td>
      <td>{{ date("d-m-Y H:i:s", $time) }}</td>         
    </tr>
    @endforeach  
    </tbody>   
    </table>
  </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>