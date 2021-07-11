@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Join Auction</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('auctions.index') }}">Auction</a></li>
          <li class="breadcrumb-item active">Join Auction</li>
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
              <div class="col-8">
                  @if ($message = Session::get('success'))
                  <div class="alert alert-success">
                      <p>{{ $message }}</p>
                  </div>
                  @endif 
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
                  <dl class="row">
                    <dd class="col-sm-12">{{$auction->description}}</dd>
                    <dt class="col-sm-4">Start Price</dt>
                    <dd class="col-sm-8">AED {{$auction->start_price}}</dd>  
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">                  
                      @if($auction->status == 0)<span class="badge badge-warning">Waiting</span>
                      @else @endif
                    </dd>
                  </dl>
                  <div class="row">       
                    <div class="col-xs-12 col-sm-12 col-md-12">&nbsp;</div>
                  </div>
              </div>
              <div class="col-4">              
                <!--- TIMER -->
                <input type="hidden" id="fromDateTime" value="{{$auction->start_time}}"/>
                <div class="clockdiv d-none">
                  <p class="text-success">Auction Starts in</p>
                  <div id="countdown" >                  
                    <div class="d-none"><span class="days"></span><small class="word">D</small></div>
                    <div><span class="hours"></span><span class="word">H</span></div>
                    <div><span class="minutes"></span><span class="word">M</span></div>
                    <div><span class="seconds"></span><span class="word">S</span></div>
                  </div>
                </div>
                <!--- TIMER -->
              </div>
            </div><!-- row -->
            <div class="row">       
              <div class="col-xs-12 col-sm-12 col-md-12"><hr/></div>
            </div>
            <div class="row">       
              <div class="col-xs-12 col-sm-12 col-md-12">
                {!! Form::open(array('route' => 'acceptinvite','method'=>'POST', 'id' => 'acceptForm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off' )) !!}

                <input type="hidden" name="user_token" class="form-control" value={{ $token }}>
                <input type="hidden" name="user_id" class="form-control" value={{ Auth::id() }}>
                <input type="hidden" name="auction_id" class="form-control" value={{ $auction->id }}>
                <input type="hidden" name="reference_no" class="form-control" value={{ $auction->reference_no }}>
                
                <h4> Do you want to agree below terms and conditions ? </h4>
             
                <p class="mt-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                
                <div class="form-group">
                    <a href="{{route('home')}}" class="btn btn-danger  mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary" {{$errorFlag? 'disabled': ''}}>I Agree</button>
                </div>
                {!! Form::close() !!}
              </div>
            </div>
          </div>
          <!-- /.card -->
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
</script>
@endsection