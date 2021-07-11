@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Choose Winner</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('auctions.index') }}">Auction</a></li>
          <li class="breadcrumb-item active">Choose Winner</li>
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
            <h5>Choose Winner</h5>
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

            @include('bids.auctionlive')

            @include('bids.choosebid')  
     
            </div>     
          </div>
          <!-- /.card -->
        </div>
      </div>
  </div>
</div>
</div>
<script type="text/javascript">
  function confirmBidder(){
    return confirm('Can you confirm this bidder?');
  }
</script>

@endsection