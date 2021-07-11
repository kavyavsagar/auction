@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Live Auction</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('auctions.index') }}">Auction</a></li>
          <li class="breadcrumb-item active">Live Auction</li>
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
            <h5>Live Auction</h5>
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
            <h4 id="warning_msg" class="text-warning text-center"></h4> 
            @include('bids.auctionlive')

            @if($auction->bid_type == 'continuous')

            <form id="bidForm" method="post">
              <input type="hidden" name="user_id" id="user_id" class="form-control" value={{ Auth::id() }}>
              <input type="hidden" name="auction_id" id="auction_id" class="form-control" value={{ $auction->id }}>
              <input type="hidden" name="auction_type" id="auction_type" class="form-control" value={{ $auction->bid_type }}>
              
              @include('bids.submitlivebids')  
            </form>

            @else
              @include('bids.roundsellerlivebids')  
            @endif

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
/********************* POST FORM **************************/
var loading = false;
$('#bidForm').on('submit', function(e){ 
    e.preventDefault();
    loading = true;

    let formData = new FormData($("#bidForm")[0]);

    if(loading){
      $('#loader').html('Please wait it will take few seconds to complete...');
    }

    $.ajax({
        type:'POST',
        url:"{{ route('bids.updatebid') }}",
        data: formData,  
        dataType:'JSON',
        contentType: false,
        cache: false,
        processData: false,        
        success:function(data){
          if(data.success){
            loading = false;
            $('#loader').html('');

            $('#error_msg').html('').addClass('d-none');

           // toastr.success(data.success); 
            //location.reload(true); //reload with time
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

});

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
      document.getElementById("countdown").innerHTML = 'Time left : '+days + "d " + hours + "h "
      + minutes + "m " + seconds + "s ";

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";  
       
      }else if(distance < 1000 && $('#auct_order').val() < 3){
        // if 1 sec before expiry, auto reload page
        location.reload(true);
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