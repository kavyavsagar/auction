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
          <li class="breadcrumb-item active">Bidding</li>
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
                   <!--  <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">                  
                      @if($auction->status == 0)<span class="badge badge-warning">Waiting</span>
                      @else @endif
                    </dd> -->
                  </dl>
                  <div class="row">       
                    <div class="col-xs-12 col-sm-12 col-md-12">&nbsp;</div>
                  </div>
              </div>
              <div class="col-4">            
             
                <input type="hidden" id="fromDateTime" value="{{$auctiontiming->startime}}"/>
                <input type="hidden" id="toDateTime" value="{{$auctiontiming->endtime}}"/>
                <input type="hidden" id="auction_order" value="{{$auctiontiming->order}}"/>
                <div  class="clockdiv d-none">
                  <p class="text-title">Until your turn</p>
                  <div id="countdown">                  
                    <div class="d-none"><span class="days"></span><small class="word">D</small></div>
                    <div><span class="hours"></span><span class="word">H</span></div>
                    <div><span class="minutes"></span><span class="word">M</span></div>
                    <div><span class="seconds"></span><span class="word">S</span></div>
                  </div>
                </div>
              </div>
            </div><!-- row -->
            <div class="row">       
              <div class="col-xs-12 col-sm-12 col-md-12"><hr/></div>
            </div>
            <div class="row">       
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h5>Price Offers</h5>
                <p></p>
                @if(count($bidoffers) > 0)
                  @foreach($bidoffers as $bids)
                    <dl class="row">
                      <dt class="col-sm-6">Seller #{{$bids->user_id}}
                      {{$bids->user_id == Auth::id()? '(You)': ''}}</dt>
                      <dd class="col-sm-6">AED {{$bids->bid_amount}}</dd> 
                    </dl> 
                  @endforeach
                @endif
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          @include('bids.submitbid') 
        </div>
      </div>       
  </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {

  // Scheduled Timers
  const schedule = [document.getElementById('fromDateTime').value, 
                    document.getElementById('toDateTime').value,
                    addInterval(new Date(document.getElementById('toDateTime').value), 2)
                    ];

  const scheduleTitle = ['Time to start', 
                        'Until your turn end',
                        'Waiting for extension'
                        ];

  const timerOrder = document.getElementById('auction_order').value;

  const clock = document.getElementById('countdown');
  const daysSpan = clock.querySelector('.days');
  const hoursSpan = clock.querySelector('.hours');
  const minutesSpan = clock.querySelector('.minutes');
  const secondsSpan = clock.querySelector('.seconds');
  const titleSpan = $('.text-title');

  function ArrayPlusDelay(array, delay) {
    var i = 0

     // seed first call and store interval (to clear later)
    var interval = setInterval(function() {
        // each loop, call passed in function
        const t = getTimeRemaining(array[i]);

       
        if (t.total > 0) {   // show timer
          if($('.clockdiv').hasClass('d-none')){
            $('.clockdiv').removeClass('d-none');
          }

          if(i == 1){ //untill your turn
            
            if($('#bid_allowed').val() <= 0)$('.dis_bidding').addClass('d-none');          
            else if($('.dis_bidding').hasClass('d-none'))$('.dis_bidding').removeClass('d-none');
          }
          
          
          daysSpan.innerHTML = t.days;
          hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
          minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
          secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
          titleSpan.text(scheduleTitle[i]);

        }else{ // end timer

          // increment, and if we're past array, clear interval
          if (i >= array.length - 1){
            clearInterval(interval);
            $('.clockdiv').addClass('d-none');
            $('.dis_bidding').addClass('d-none');

            if((i == (array.length - 1)) && timerOrder < 3){
              //  auto extend auction
              extendAuction();
            }else{
              alert('Thank you for the participation. Auction Winner will be informed later.');
            }
          }
          i++;
        }
        
          
    }, delay)

    return interval
  }

  var inter =  ArrayPlusDelay(schedule, 1000);


  /************** Submit Bid ***************/
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  //


  /********************* POST FORM **************************/
  var loading = false;
  $('#bidForm').on('submit', function(e){  
    e.preventDefault();
    loading = true;

    let formData = new FormData($("#bidForm")[0]);

    if(formData.get('bid_amount') > formData.get('lowest_bid')){
      $('#error_msg').html('Your bid value is higher').removeClass('d-none');
      return;
    }
    if(formData.get('bid_allowed') <= 0){
      $('#error_msg').html('You have reached the limit of bidding on this round').removeClass('d-none');
      return;
    }

    if(loading){
      $('#loader').html('Please wait it will take few seconds to place bid.');
    }

    $.ajax({
        type:'POST',
        url:"{{ route('bids.updatebid') }}",
        data: formData,  
        dataType:'JSON',
        contentType: false,
        cache: false,
        processData: false,        
        success:function(res){
          if(res.success){
            loading = false;
            $('#loader').html('');

            $('#error_msg').html('').addClass('d-none');
            if(res.data){
              let data = res.data;

              $('.allowedbid').text(data.allowedbid);              
              $('#bid_allowed').val(data.allowedbid);

              if(data.allowedbid <= 0){
                $('.dis_bidding').addClass('d-none');
              }else{
                if($('.dis_bidding').hasClass('d-none')) $('.dis_bidding').addClass('d-none');
              }

              // if(data.currentuser == $('#user_id').val()){
              //     $('.dis_bidding').removeClass('d-none');
              // }else{
              //     $('.dis_bidding').addClass('d-none');
              // }
            }

           // toastr.success(res.success); 
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


  /****************** Extend Time ********************/
  var loading = false;
  function extendAuction(){
    loading = true;

    if(loading){
      $('#loader').html('Please wait it will take few seconds to complete...');
    }

    let formData = {};
    formData.auction_id =  $('#auction_id').val();
    formData.bid_type = $('#bid_type').val();

    $.ajax({
        type:'POST',
        url:"{{ route('auctions.extendtime') }}",
        data: formData,  
        dataType:'JSON',
        // contentType: false,
        // cache: false,
        // processData: false,        
        success:function(res){
          if(res.success){
            loading = false;
            $('#loader').html('');

             window.location.reload(true);
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
});
</script>
@endsection