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
          <li class="breadcrumb-item active"><a href="{{ route('bids.index') }}">List Bids</a></li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col">

        <div class="card">
        <div class="card-header">
          <h3 class="card-title">View Your Bids</h3>                   
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
          @endif          
          <table id="buyer-auction-tbl" class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>#</th>
              <th>Auction Title</th>
              <th>Type</th>
              <th>Start Price</th>
              <th>Start Time</th>
              <th>Action</th>                  
            </tr>
            </thead>
            <tbody> 

             @foreach ($auctions as $key => $item)
              @php
                $current_time = strtotime(Carbon\Carbon::now()->addMinutes(90));
                $start = strtotime($item->start_time);

                $start_time = date("j M Y, g:i a", $start); 
                $end_time = date("j M Y, g:i a", strtotime($item->end_time)); 
              @endphp 
              <tr>
                <td>{{ ($key+1) }}</td>
                <td>{{ $item->title }}                 
                  @if($start > $current_time)
                    <span class="badge badge-primary">Upcoming</span>
                  @elseif(date('Y-m-d', $start) == date('Y-m-d', $current_time) )
                    <span class="badge badge-warning">Today</span>
                  @endif
                </td>
                <td>{{ $item->bid_type }}</td>
                <td>AED {{ $item->start_price }}</td>               
                <td>{{ $start_time }}</td>
                <td>
                 
                  <a href="{{ route('bids.show', $item->reference_no) }}" class="btn btn-info" title="View Live Auction"><i class="fas fa-eye"></i></a>                                               
                </td>
              </tr>
             @endforeach 
            </tbody>
            <tfoot>
            <tr>
              <th>#</th>
              <th>Auction Title</th>
              <th>Type</th>
              <th>Start Price</th>
              <th>Start Time</th>
              <th>Action</th>                       
            </tr>
            </tfoot>
          </table>
        </div>
        <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- <div class="col">&nbsp;</div> -->
    </div>
  </div>
</section> 
<!-- /.content -->

@endsection