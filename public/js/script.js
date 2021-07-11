$(document).ready(function(){

  /****************** TABLE ***********************/
	$('#buyer-auction-tbl').DataTable({
	  "responsive": true,
      "autoWidth": false,
      "order": [[ 5, "desc" ]],
    });
    $('#bid-seller-tbl').DataTable({
	  "responsive": true,
      "autoWidth": false,
      "order": [[ 5, "desc" ]],
    });



});

  /************************ PUSHER ***********************************/
 //Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;

  var pusher = new Pusher('fc47f3860b36fb138fea', {
    cluster: 'ap2'
  });

  var channel = pusher.subscribe('bid-channel');
  channel.bind('BidEvent', function(data) { 
    let hisHtml = '';
    if(data.message){ 
      let his  = data.message;
      if($('#bid_type').val() == 'continuous'){
          //  fix format
        let dt = new Date(his.created_at); 
        let date_time = getFormattedDate(dt);

        hisHtml += '<tr><td>Seller'+ his.user_id;  
        if($('#user_id').val() == his.user_id){
          hisHtml += ' (YOU)';
        }
        hisHtml += '</td>';
        hisHtml += '<td>AED '+ his.bid_amount +'</td><td> '+ date_time +'</td></tr>';
        $('#livebid_his').append(hisHtml);
      }else{

        let rowid = 'r'+his.turn+'_p'+his.user_id,
          timeslot = $('#'+ rowid).find('.time-slot').text();

        hisHtml += 'AED '+his.bid_amount +'<span class="time-slot d-none">'+timeslot+'</span>';
        $('#'+ rowid).find('.bamount').html(hisHtml);
      }
    
      let lowestvalue = his.bid_amount - $('#min_step').val();
      $('.lowestbid').text('AED '+ lowestvalue);
      $('#lowest_bid').val(lowestvalue);

      
      $('#bid_amount').val('');
    }  
      //alert(JSON.stringify(data));
     // location.reload(true);
     //console.log(data);
  });



// LARAVEL ECHO SERVER
// var i = 0;
// window.Echo.channel('bid365_database_bid-channel').listen('.BidEvent', (data) => {
//   let hisHtml = '';
  
//   if(data['message'].length > 0){
//     data['message'].forEach((his) => {
//       //  fix format
//       let dt = new Date(his.created_at);  
//       let cur_dt = dt.getFullYear() + '-' + (dt.getMonth()+1) + '-' + dt.getDate();
//       let cur_tim = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
//       let date_time = cur_dt + ' ' + cur_tim;

//       hisHtml += '<tr><td>Seller'+ his.user_id;  
//       if($('#auth_user').val() == his.user_id){
//         hisHtml += ' (YOU)';
//       }
//       hisHtml += '</td>';
//       hisHtml += '<td>AED '+ his.bid_amount +'</td><td> '+ date_time +'</td></tr>';
//     });

//     $('#livebid_his').html(hisHtml);
//     $('#bid_amount').val('');
//   }  

// });  