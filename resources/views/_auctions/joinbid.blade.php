@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Auction Details</h1>
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
        <div class="card card-primary card-outline card-outline-tabs">     
          <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="nav-auction-tab" data-toggle="pill" href="#nav-auction" role="tab" aria-controls="nav-auction" aria-selected="true">Auction Summary</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="nav-sellerbid-tab" data-toggle="pill" href="#nav-sellerbid" role="tab" aria-controls="nav-sellerbid" aria-selected="false">Seller Auction</a>
                </li>              
            </ul>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
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
            {!! Form::open(array('route' => 'bids.store','method'=>'POST', 'id' => 'bidForm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off' )) !!}
            <input type="hidden" name="user_id" id="user_id" class="form-control" value={{ Auth::id() }}>
            <input type="hidden" name="auction_id" id="auction_id" class="form-control" value={{ $auction->id }}>
            <div class="tab-content" id="nav-tabContent">
              <!-- Basic Details -->
              <div class="tab-pane fade show active" id="nav-auction" role="tabpanel" aria-labelledby="nav-auction-tab">
               
                  @include('auctions.joinstep1')
              </div>
              <!--  Invite Participants -->
              <div class="tab-pane fade" id="nav-sellerbid" role="tabpanel" aria-labelledby="nav-sellerbid-tab">
                  @if($isBid >0)
                    <p>&nbsp;</p>                    
                    <h5 class="text-danger">You are already submitted your first bid</h5>
                    <p>&nbsp;</p>
                    <a href="{{route('bids.show', $auction->id)}}" class="btn btn-primary btn-lg"> Go to auction !</a>
                     <p>&nbsp;</p>
                  @else
                    @include('auctions.joinstep2')
                  @endif
              </div>
              
            </div>
           {!! Form::close() !!}
          </div>
          <!-- /.card -->
        </div>
      </div>
  </div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
   // Tab Change
  $('.btnNext').on('click', function (e) {
      e.preventDefault();
      let next = $(this).attr('data-id');
      $('#nav-tab a[href="#nav-'+next+'"]').tab('show');       
  });

  $('.btnPrevious').on('click', function (e) {
      e.preventDefault();
      let next = $(this).attr('data-id');
      $('#nav-tab a[href="#nav-'+next+'"]').tab('show');       
  });

});
</script>
@endsection