@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">List Your Bids</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item active">List Bids</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="container">
      <div class="row">
        <div class="col-md-12">  
         <div class="card">
          <div class="card-header">
            <h3 class="card-title">Manage Your Bids</h3>         
          </div>
        <!-- /.card-header -->
        <div class="card-body">        
          <table id="bid-seller-tbl" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>Auction</th>
              <th>Type</th>
              <th>Start Price</th>
              <th>Start On</th>
              <th>Bid Amount</th>
              <th>Last Bid On</th> 
              <th>Action</th>               
            </tr>
            </thead>
            <tbody> 
            @foreach ($auctions as $auct)   
              @php
                $start = strtotime($auct->start_time);
                $start_time = date("j M Y, g:i a", $start); 

                $lastbidtime = date("j M Y, g:i a", strtotime($auct->created_at)); 
              @endphp    
            <tr>
              <td>{{ $auct->title }}</td>
              <td>{{ $auct->bid_type }}</td>
              <td>AED {{ $auct->start_price }}</td>
              <td>{{ $start_time }}</td>
              <td>AED {{ $auct->bid_amount }}</td>
              <td>{{ $lastbidtime }}</td>  
              <td>
              <a href="{{ route('bids.show', $auct->auctionid) }}" class="btn btn-info" title="View Live Auction"><i class="fas fa-eye"></i></a>
              </td>        
            </tr>
             @endforeach  
            </tbody>
            <tfoot>
            <tr>
              <th>Auction</th>
              <th>Type</th>
              <th>Start Price</th>
              <th>Start On</th>
              <th>Bid Amount</th>
              <th>Last Bid On</th>  
              <th>Action</th>                 
            </tr>                  
            </tr>
            </tfoot>
          </table>
        </div>
      </div><!-- card -->
        </div>
      </div>
  </div>
</div>
@endsection