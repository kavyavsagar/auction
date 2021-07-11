@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Live Auction - Buyer</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('auctions.index') }}">Auction</a></li>
          <li class="breadcrumb-item active">Live Auction - Buyer</li>
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
            <h5>Buyer Live Auction</h5>
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
            <div id="error_msg" class="text-danger d-none"></div>
            <div id="loader"></div>  
     
            @include('bids.auctionlive')

           

            @include('bids.livebidhistory')  

            <input type="hidden" name="auction_id" id="auction_id" value="{{ $auction->id }}">
            <input type="hidden" id="auction_type" value="{{$auction->bid_type}}">     
            <input type="hidden" id="end_time" value="{{$auction->end_time}}"> 
            <input type="hidden" id="start_time" value="{{$auction->start_time}}">
            <input type="hidden" id="auct_order" value="{{$auctionOrder}}">

            
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
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var loading = false;
  function extendAuction(){
    loading = true;

    if(loading){
      $('#loader').html('Please wait it will take few seconds to complete...');
    }

    let formData = {};
    formData.auction_id =  $('#auction_id').val();
    formData.auction_type = $('#auction_type').val();

    $.ajax({
        type:'POST',
        url:"{{ route('auctions.extendtime') }}",
        data: formData,  
        dataType:'JSON',
        // contentType: false,
        // cache: false,
        // processData: false,        
        success:function(data){
          if(data.success){
            loading = false;
            $('#loader').html('');

            location.reload(true);
          }
        },
        error:function(data){   
            let err_str = '';  
            if(data.responseJSON.errors){
                loading = false; 
                let errs = data.responseJSON.errors;

                $('#loader').html('');         
                err_str = '<dl class="row">';  

                if(typeof errs !== "string"){                
                  $.each(errs, function(key, val){
                      err_str += '<dd class="col-sm-8">'+ val+ '</dd>';
                  });
                }else{ 
                  err_str += '<dd class="col-sm-8">'+ errs+ '</dd>';
                }
                err_str += '</dl>';  

               //toastr.error(err_str);  
               $('#error_msg').html(err_str).removeClass('d-none');
                return;
            }            
        }
    });
  }

  /****************** TIMER ***********************/


  // Set the date we're counting down to
  var countDownDate = new Date($('#end_time').val()).getTime();
  var countStartDate = new Date($('#start_time').val()).getTime();
  // Update the count down every 1 second
  var x = setInterval(function() {

    // Get today's date and time
    var now = new Date().getTime();
    var start_distance = countStartDate - now;

    if(start_distance < 0){
      // Find the distance between now and the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result in the element with id="demo"
      document.getElementById("countdown").innerHTML = 'Time left : '+ days + "d " + hours + "h "
      + minutes + "m " + seconds + "s ";

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";
        
      }else if(distance < 2000 && $('#auct_order').val() < 3){
        // if 2 sec before expiry, auto renew
        extendAuction();
      }
    }else{
      // before time starts
      var sdays = Math.floor(start_distance / (1000 * 60 * 60 * 24));
      var shours = Math.floor((start_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var sminutes = Math.floor((start_distance % (1000 * 60 * 60)) / (1000 * 60));
      var sseconds = Math.floor((start_distance % (1000 * 60)) / 1000);

      document.getElementById("countdown").innerHTML = 'Waiting : '+ sdays + "d " + shours + "h " + sminutes + "m " + sseconds + "s ";
    }
    
  }, 1000);



});
</script>
@endsection