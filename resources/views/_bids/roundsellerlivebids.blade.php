<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
  <div class="col-md-12">  
   <h4>Bid History</h4>  
   <input type="hidden" id="auth_user" value="{{Auth::user()->id}}">

   <table id="itemTable" class="table order-list">
    <thead>
      <tr>
          <th>Bid History</th>
          <th>Amount</th>
          <th>Time</th>
      </tr>
    </thead>
    <tbody id="livebid_his">  
    @foreach ($bidlist as $key => $bid)
      @php
        $time = strtotime($bid->created_at);
      @endphp
    <tr>
      <td width="20%">Seller {{$bid->user_id}} @if($bid->user_id == Auth::user()->id)(YOU)@endif</td>
      <td width="30%">AED {{$bid->bid_amount}} </td>
      <td width="20%">{{ date("d-m-Y H:i:s", $time) }}</td>    
    </tr>   
    @endforeach  
    </tbody>   
    </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">&nbsp;</div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div id="error_msg" class="text-danger d-none"></div>      
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">    
    <table id="biddertbl" class="table order-list">
    <thead>
      <tr>
        <th>Seller</th>
        <th>Bid Value</th>
        <th>Priority</th>
      </tr>
    </thead>
    <tbody id="round-bidders">  

    @foreach ($biddersUniq as $key => $bid)
      @php
        $mins = $duration_per_user * ($key+1);
        $end  = date("Y-m-d H:i:s", strtotime('+'.$mins.' minutes', strtotime($roundStartime)));
      @endphp
    <input type="hidden" name="userid[]" value="{{ $bid->user_id }}">
    <tr class="row_{{ $bid->user_id }}">
      <td width="20%">Seller {{$bid->user_id}} @if($bid->user_id == Auth::user()->id)(YOU)@endif</td>   
      <td width="20%">
        <div class="input-group" id="bidbox_{{$bid->user_id}}">
          <input type="number" id="bidamt_{{$bid->user_id}}" class="form-control" disabled="true" min="0">
          <div class="input-group-append">
            <button type="button" class="btn btn-primary input-group-text go-bid" disabled="true">GO</button>
          </div>
        </div>
        <div id="loader"></div>
      </td>
      <td width="30%"><div id="bid_timer_{{$bid->user_id}}" data-id="{{$end}}">2:00</div></td>         
    </tr>    
    @endforeach  

    </tbody>   
    </table>
   <!--  <button id="js-startTimer" type="button">Start Countdown</button>
    <button id="js-resetTimer" type="button">Stop &amp; Reset</button> -->
  </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
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
  $('.go-bid').on('click', function(e){ 
    
    e.preventDefault();
    loading = true;

    let formData = {};
    let user = $(this).closest('.input-group').find('input[type=number]'),
      uid = user.attr('id').split("_")[1],
      auctid = {{ $auction->id }};
   
    if(!uid || !auctid){
      $('#error_msg').html("Missing details").removeClass('d-none');
      return false;
    }
    if(!user.val()){
      $('#error_msg').html("Please enter bid amount").removeClass('d-none');
      return false;
    }

    formData.user_id = uid;
    formData.bid_amount = user.val();
    formData.auction_id = auctid;
    formData.auction_type = "{{ $auction->bid_type }}";

    if(loading){
      $('#loader').html('Please wait it will take few seconds to complete...');
    }

    $.ajax({
        type:'POST',
        url:"{{ route('bids.updatebid') }}",
        data: formData,  
        dataType:'JSON',
        // contentType: false,
        // cache: false,
        // processData: false,        
        success:function(data){
          if(data.success){
            loading = false;
            $('#loader').html('');

            $('#bidamt_'+uid).val('');
            $('#error_msg').html('').addClass('d-none');

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

  var container = document.getElementById('round-bidders');
  var userele = container.querySelectorAll("input[type=hidden]");

  var getUserId = function(val){
    return userele[val].value;
  }
  var userCount = function(){
    return userele.length;
  }
  var getDifferenceInMinutes = function(date1, date2){    
    const diffInMs = date2 - date1; 
    return diffInMs / (1000 * 60);
  }

  if(!localStorage.user_pos){
    localStorage.user_pos = 0;
  }
  var next = localStorage.user_pos;
  var countDownDate = new Date($('#end_time').val()).getTime();
  var countStartDate = new Date($('#start_time').val()).getTime();

  var interval = setInterval(function() {
      
      var now = new Date().getTime();
      var start_distance = countStartDate - now;

      if(now > countStartDate && now < countDownDate){   

        if(next < userCount()){ 
          let uid = getUserId(next);
          if(uid == {{ Auth::id() }} ){
            $('#bidbox_'+uid).find('input[type=number]').removeAttr("disabled");
            $('#bidbox_'+uid).find('button').removeAttr("disabled");
          }        
          let user_end_time = $('#bid_timer_'+uid).attr('data-id'); 

          var timer = getDifferenceInMinutes(new Date(), new Date(user_end_time));
          //$('#bid_timer_'+uid).html();
          timer = timer.toFixed(2); 
          timer = timer.split('.');
          var minutes = (timer[0] > 0)? timer[0]: 0;
          var seconds = (timer[1] > 0)? timer[1]: 0;
          
          seconds -= 1;
          if (minutes < 0) { return;}
          else if (seconds < 0 && minutes != 0) {
              minutes -= 1;
              seconds = 59;
          }
          else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;
          

          $('#bid_timer_'+uid).html(minutes + ':' + seconds);
          $('.row_'+uid).css('background','#e0c9a6');

          if (minutes == 0 && seconds == 0){      
               
            if(uid == {{ Auth::id() }} ){
              $('#bidbox_'+uid).find('input[type=number]').prop("disabled", true);
              $('#bidbox_'+uid).find('button').prop("disabled", true);
            }       
            $('.row_'+uid).css('background','inherit');            
            next++;    
            localStorage.user_pos = next;      
          }
                 
        }else{
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
          // If the count down is finished, write some text
          if (distance < 0) { 
            clearInterval(interval);
            document.getElementById("countdown").innerHTML = "EXPIRED"; 
            $('.input-group').find('input[type=number]').prop("disabled", true);
            $('.input-group').find('button').prop("disabled", true); 
            localStorage.removeItem("user_pos");

          }else if(distance < 1000 && $('#auct_order').val() < 3){ 
            // if 1 sec before expiry, auto reload page
            localStorage.user_pos = 0;
         
            location.reload(true);
          }
        }
      }
      

  }, 1000);

});

</script>