@extends('layouts.default')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Create Auction</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
          <li class="breadcrumb-item active">Create Auction</li>
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
            <!-- general form elements -->
        <div class="card card-primary card-outline card-outline-tabs">     
          <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="nav-basic-tab" data-toggle="pill" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">Basic Details</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="nav-invite-tab" data-toggle="pill" href="#nav-invite" role="tab" aria-controls="nav-invite" aria-selected="false">Private Auction</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link"  id="nav-summary-tab" data-toggle="pill" href="#nav-summary" role="tab" aria-controls="nav-summary" aria-selected="false">Summary</a>
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
            {!! Form::open(array('route' => 'auctions.store','method'=>'POST', 'id' => 'auctForm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off' )) !!}
            <input type="hidden" name="user_id" id="user_id" class="form-control" value={{ Auth::id() }}>
            <div class="tab-content" id="nav-tabContent">
              <!-- Basic Details -->
              <div class="tab-pane fade show active" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                  @include('auctions.step1')
              </div>
              <!--  Invite Participants -->
              <div class="tab-pane fade" id="nav-invite" role="tabpanel" aria-labelledby="nav-invite-tab">
                  @include('auctions.step2')
              </div>
              <!--  Summary Overview -->
              <div class="tab-pane fade" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
                  @include('auctions.step3')
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
  // Validations

  //  $.validator.setDefaults({
  //   submitHandler: function () {
  //   //  alert( "Form successful submitted!" );
  //   }
  // });
  // $('#auctForm').validate({
  //   rules: {
  //     title: {
  //       required: true
  //     },
  //     reference_no: {
  //       required: true,
  //       minlength: 5
  //     },
  //     description: {
  //       required: true
  //     },
  //     start_price: {
  //       required: true,
  //       digits: true
  //     },
  //     start_time: {
  //       required: true
  //     },
  //   },
  //   messages: {
  //     title: {
  //       required: "Please enter your auction title"
  //     },
  //     reference_no: {
  //       required: "Please provide a reference no",
  //       minlength: "Your reference no must be at least 5 characters long"
  //     },
  //     description: "Please enter auction details",
  //     start_price: {
  //       required: "Please enter your starting price",
  //       digits: "Your price should be number only"
  //     },
  //     start_time: {
  //       required: "Please enter your start date and time"
  //     },
  //   },
  //   errorElement: 'span',
  //   errorPlacement: function (error, element) {
  //     error.addClass('invalid-feedback');
  //     element.closest('.form-group').append(error);
  //   },
  //   highlight: function (element, errorClass, validClass) {
  //     $(element).addClass('is-invalid');
  //   },
  //   unhighlight: function (element, errorClass, validClass) {
  //     $(element).removeClass('is-invalid');
  //   }
  // });

  /*******************************************/

  // multiple file upload
  $(".add-file").click(function(){ 
      let lsthmtl = $(".clone").html();
      $(".increment").append(lsthmtl);
  });
  $("body").on("click",".btn-del-f",function(){ 
    if($('div.decrement').length > 1){
      $(this).parents(".decrement").remove();
    }
  });

  // time picker
  $('#start_time').datetimepicker({
    // format: 'DD/MM/YYYY HH:mm', 
    useCurrent: false,
    showTodayButton: true,
    showClear: true,
    toolbarPlacement: 'bottom',
    sideBySide: true,
    // icons: {
    //   time: "fa fa-clock-o",
    //   date: "fa fa-calendar",
    //   up: "fa fa-arrow-up",
    //   down: "fa fa-arrow-down",
    //   previous: "fa fa-chevron-left",
    //   next: "fa fa-chevron-right",
    //   today: "fa fa-clock-o",
    //   clear: "fa fa-trash-o"
    // }
  });


  // Add Row to the table

  var counter = 1;

  $("#addrow").on("click", function () {
      var newRow = $("<tr>");
      var cols = "";

      // cols += '<td><input type="number" class="form-control" placeholder="Number" name="item['+counter+'][part_no]"/></td>';
      cols += '<td><input type="text" class="form-control" placeholder="Description" name="item['+counter+'][brief]"/></td>';
      cols += '<td><input type="number" class="form-control" placeholder="Quantity" name="item['+counter+'][quantity]"/></td>';
      cols += '<td><input type="file" class="form-control" placeholder="Uploads" name="item['+counter+'][doc_path]"/></td>';

      cols += '<td><button type="button" class="btn btn-danger ibtnDel"><i class="fas fa-minus-square"></i></button></td>';
      newRow.append(cols);
      $("table.order-list").append(newRow);
      counter++;
      $('#itemCount').val(counter);
  });

  $("table.order-list").on("click", ".ibtnDel", function (event) {
      $(this).closest("tr").remove();       
      counter -= 1
      $('#itemCount').val(counter);
  });


  // Dynamic Invites
  $(".invite-new").click(function(){ 
      let clHtml = $(".clone-priv").html();
      $(".incr").append(clHtml);
  });

  $("body").on("click",".invite-del",function(){ 
    if($('div.decr').length > 1){
      $(this).parents(".decr").remove();
    }
  });

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

  // hide duration
  $('.bid_type').on('click', function(e){
     // e.preventDefault();
      if($(this).val() == 'continuous'){
          $('#duration_cont').addClass('d-block');
          $('#duration_cont').removeClass('d-none');
      }else{
          $('#duration_cont').addClass('d-none');
          $('#duration_cont').removeClass('d-block');
      }

  });

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
  /********************* Preview Summary **************************/

  $('.btnNext').on('click', function (e) {
      e.preventDefault();
      let dataid = $(this).attr('data-id');
      let col = '';
      $('#auction_detail').html('');
      $("table > tbody#auction_lists").html('');
      $('#participant_emails').html('');

      if(dataid == 'summary'){
        col += '<dt class="col-sm-4">Bid Type</dt><dd class="col-sm-8">'+$("input[name='bid_type']:checked").val()+'</dd>'; 
        if($('#auction_title').val())
          col += '<dt class="col-sm-4">Auction Title</dt><dd class="col-sm-8">'+$('#auction_title').val()+'</dd>'; 
        if($('#reference_no').val())
          col += '<dt class="col-sm-4">Reference No</dt><dd class="col-sm-8">#'+$('#reference_no').val()+'</dd>'; 
        if($('#description').val())
          col += '<dt class="col-sm-4">Descripion</dt><dd class="col-sm-8">'+$('#description').val()+'</dd>'; 
        if($('#start_price').val())
          col += '<dt class="col-sm-4">Start Price</dt><dd class="col-sm-8">AED '+$('#start_price').val()+'</dd>'; 
        if($('#budget').val())
          col += '<dt class="col-sm-4">Budget</dt><dd class="col-sm-8">AED '+$('#budget').val()+'</dd>'; 
        if($("input[name='start_time']").val())
          col += '<dt class="col-sm-4">Start Time</dt><dd class="col-sm-8">'+$("input[name='start_time']").val()+'</dd>';
        if($('#duration').val())
          col += '<dt class="col-sm-4">Duration</dt><dd class="col-sm-8">'+$('#duration').val()+' hr</dd>'; 
       
        let filemany = '';
       
        $('.filemany').each(function () {
          let str = $(this).val();
          if($(this).val())
            filemany += str.split("\\").reverse()[0]+'<br/> ';
        });  
        if(filemany)      
          col += '<dt class="col-sm-4">File Uploads</dt><dd class="col-sm-8">'+filemany+'</dd>'; 

        $('#auction_detail').append(col);

        /********** LISTS **********/
       
        let itemCount = $('#itemCount').val();

        for(let i=0; i<itemCount; i++){
          let rowItem = $("<tr>"), col1 = '';

          // col1 += '<td>'+$("input[name='item["+i+"][part_no]']").val()+'</td>';
          col1 += '<td>'+$("input[name='item["+i+"][brief]']").val()+'</td>';
          col1 += '<td>'+$("input[name='item["+i+"][quantity]']").val()+'</td>';
          let fp = $("input[name='item["+i+"][doc_path]']").val();
          col1 += '<td>'+fp.split("\\").reverse()[0]+'</td>';

          rowItem.append(col1);
          $("table > tbody#auction_lists").append(rowItem);
        }
        
        /************** Invitees ****************/

        let invitees = '', col2 = '';
       
        $('.participantids').each(function () {
          let email = $(this).val();
          if(email){ 
          
            col2 += '<dd class="col-sm-12">'+$(this).val()+'</dd>'; 
          }          
        }); 
        $('#participant_emails').append(col2);


      }
  });
  
});
</script>
@endsection