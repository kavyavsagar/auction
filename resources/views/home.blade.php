@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">I want to :</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item active">{{ __('Dashboard') }} </li>
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
            <div class="col-md-8">
                <a href="{{ route('auctions.create') }}" class="btn btn-primary">START AUCTION</a>
            </div>
          
        </div>
        <div class="row">
            <div class="col-md-8">
              &nbsp;
            </div>
          
        </div>
        <div class="row"> 
          <div class="col">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="card-title m-0">UPCOMING AUCTION</h5>
            </div>
            <div class="card-body">
              <table id="buyer-auction-tbl" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Start Price</th>          
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Action</th>                  
                </tr>
                </thead>
                <tbody> 

                 @foreach ($auctions as $key => $item)
                  @php
                    $current_time = strtotime(Carbon\Carbon::now());
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
                    <td>{{ $end_time }} <span class="badge badge-secondary">{{$item->duration}} HRS</span></td>
                    <td>
                      @if($item->winner_bid)
                        <a href="{{ route('choosewinner', $item->id) }}" class="btn btn-info" title="View  Winner"><i class="fas fa-eye"></i></a>
                      @else
                        <a href="{{ route('liveauction', $item->id) }}" class="btn btn-info" title="View Live Auction"><i class="fas fa-eye"></i></a>
                      @endif
                      {!! Form::open(['method' => 'DELETE','route' => ['auctions.destroy', $item->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                      {!! Form::close() !!}                  
                    </td>
                  </tr>
                 @endforeach 
                </tbody>
                <tfoot>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Start Price</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Action</th>                       
                </tr>
                </tfoot>
              </table>
            </div>
          </div>
          </div>
        </div>

    </div>
</div>
<!-- /.content -->
@endsection