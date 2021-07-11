@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Auction</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('auctions.index') }}">Auction</a></li>
          <li class="breadcrumb-item active">View Auction</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container">
      <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
        <div class="card card-primary">     
          <div class="card-header">
            <h5>{{$auction->title}}</h5>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif 
                @php
                  $startd = date('Y-m-d', strtotime($auction->start_time));
                @endphp 
                <dl class="row">
                  <dt class="col-sm-4">Bid Type</dt>
                  <dd class="col-sm-8 text-capitalize">                  
                    {{$auction->bid_type}} Auction                  
                  </dd>
                  <dt class="col-sm-4">Status</dt>
                  <dd class="col-sm-8">                  
                    @if($auction->status == 0)<span class="badge badge-warning">Waiting</span>
                    @else @endif
                  </dd>
                  <dt class="col-sm-4">Start Time</dt>
                  <dd class="col-sm-8">{{$auction->start_time}}</dd>
                  <dt class="col-sm-4">Start Price</dt>
                  <dd class="col-sm-8">AED {{$auction->start_price}}</dd>
                  <dt class="col-sm-4">Minimum Step</dt>
                  <dd class="col-sm-8">AED {{$auction->min_step}}</dd>
                  <dd class="col-sm-12">{{$auction->description}}</dd>
                </dl>
              </div>
              <div class="col-md-4">
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                       @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                       @endforeach
                    </ul>
                </div>
                @endif 
                <!--- TIMER -->
                <input type="hidden" id="fromDateTime" value="{{$auction->start_time}}"/>
                <div class="clockdiv d-none">
                  <p class="text-success">Time to Start</p>
                  <div id="countdown">
                    <div class="d-none"><span class="days"></span><small class="word">D</small></div>
                    <div><span class="hours"></span><span class="word">H</span></div>
                    <div><span class="minutes"></span><span class="word">M</span></div>
                    <div><span class="seconds"></span><span class="word">S</span></div>
                  </div>
                </div>
                <!--- TIMER -->
                <p>Bidder registration will be closed 10 minutes before auction start.</p>

                {!! Form::open(array('route' => 'auction.saveInvites','method'=>'POST', 'id' => 'auctInForm',  'autocomplete' => 'off' )) !!}
                  <input type="hidden" name="auction_id" id="auction_id" class="form-control" 
                  value={{ $auction->id }}>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        {!! Form::text('name', null, array('placeholder' => 'Enter Bidder Name','class' => 'form-control', 'id' => 'name')) !!}
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">                      
                        {!! Form::text('email', null, array('placeholder' => 'Enter Bidder Email','class' => 'form-control', 'id' => 'email')) !!}
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">                     
                        {!! Form::number('bid_amount', $auction->start_price, array('placeholder' => 'Enter Bidder Amount (AED)','class' => 'form-control', 'id' => 'bid_amount')) !!}
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-primary">Register Bidder</button>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      &nbsp;
                    </div>
                  </div>
                {!! Form::close() !!}

              </div>
            </div>
      
          </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">&nbsp;</div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h5><b>List Items</b></h5>
            </div>
          <div class="card-body">
            <table id="auct_item" class="table table-hover">
            <thead>
            <tr>
              <th>Item</th>
              <th>Quantity</th>           
            </tr>
            </thead>
            <tbody> 
             @foreach ($auctionItem as $key => $item)             
              <tr>
                <td>{{ $item->brief }}</td>
                <td>{{ $item->quantity }}</td>
              </tr>
             @endforeach 
            </tbody>         
          </table>
          </div>
          </div>
            
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">&nbsp;</div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5><b>Registered Bidders</b></h5>
              </div>
            <div class="card-body">
              <table id="auct_item" class="table table-hover">
              <thead>
              <tr>
                <th>Bidder</th>
                <th>Submission On</th>      
                <th>Bid Amount</th>      
                <th>Join Link</th>           
              </tr>
              </thead>
              <tbody> 
               @foreach ($participants as $key => $user)
                @php
                  $time = strtotime($user->created_at) + 60*60*4;; 
                @endphp              
                <tr>
                  <td>{{ $user->name }}<br/>{{ $user->email }}</td>
                  <td>{{ date("d-m-Y H:i:s", $time) }}</td>
                  <td>AED {{ $initialbids[$user->userid] }}</td>
                  <th><a href="{{route('joinauction', $user->token)}}">JOIN</a></th>  
                </tr>
               @endforeach 
              </tbody>         
            </table>
            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">&nbsp;</div>
        </div>
        <div class="row">
          <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h5><b>Auction Result</b></h5>
            </div>
            <div class="card-body">            
             <table id="resultTable" class="table order-list">
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
              @foreach ($bidhis as $key => $bid)
              <tr bgcolor={{ ($auction->winner_bid == $bid->id)? 'green': ''}}>

                <td>{{$bid->username}}
                <span class="badge badge-danger ml-2">{{ ($auction->winner_bid == $bid->id)? 'WINNER': ''}}</span>
                </td>
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
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">&nbsp;</div>
        </div>
      </div>
  </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  const deadline = document.getElementById('fromDateTime').value;

  initializeClock('countdown', deadline);

  
});
 function confirmBidder(){
    return confirm('Can you confirm this bidder?');
  }
</script>
@endsection