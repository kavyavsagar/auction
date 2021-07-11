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
        <div class="card card-primary">     
          <div class="card-header">
            <h3 class="card-title">Create Auction</h3>
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
              <input type="hidden" name="user_id" id="user_id" class="form-control" 
              value={{ Auth::id() }}>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group clearfix">
                      <div class="icheck-primary d-inline mr-2">
                        <input type="radio" id="continuous" name="bid_type" value="continuous" checked="" class="bid_type">
                        <label for="continuous">Continuous Auction </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="round" name="bid_type" value="round" class="bid_type">
                        <label for="round">Reverse Auction</label>
                      </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="auction_title">Auction Title</label>                       
                    {!! Form::text('title', null, array('placeholder' => 'Enter Auction Title','class' => 'form-control', 'id' => 'auction_title')) !!}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                 <div class="form-group">
                  <label for="description">Description</label>                      
                  {!! Form::textarea('description', null, ['placeholder' => 'Enter Auction Description','class' => 'form-control','id' => 'description', 'rows' => 4, 'cols' => 54]) !!}
                </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="start_price">Start Price (AED)</label>                       
                    {!! Form::number('start_price', null, array('placeholder' => 'Enter Start Price','class' => 'form-control', 'id' => 'start_price', 'min' => 0)) !!}
                  </div>
                </div>
                 <div class="col-md-6">
                  <div class="form-group">
                    <label for="start_price">Minimum Step (AED)</label>                       
                    {!! Form::number('min_step', null, array('placeholder' => 'Minimum reduction of bid amounts','class' => 'form-control', 'id' => 'start_price', 'min' => 0)) !!}
                  </div>
                </div>
                <div class="col-md-4">
                  
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="start_time">Specific Time</label>                       
                     <div class="input-group date" id="start_time" data-target-input="nearest">
                          <input type="text" class="form-control datetimepicker-input" data-target="#start_time" name="start_time"/>
                         
                          <div class="input-group-append" data-target="#start_time" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" id="duration_cont">
                    <label for="duration">Auction Duration (Minutes)</label>                      
                    {!! Form::text('duration', null, array('placeholder' => 'Enter Auction Duration (Mins)','class' => 'form-control', 'id' => 'duration')) !!}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">&nbsp;</div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <h5><b>LIST ITEMS</b></h5>
                  <table id="itemTable" class="table order-list">
                  <thead>
                      <tr>
                          <th width="40%">Description</th>
                          <th width="10%">Qty</th>
                          <th width="30%">Uploads</th>
                          <th width="10%">Action</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <td width="40%">
                            {!! Form::text('item[0][brief]', null, array('placeholder' => 'Description','class' => 'form-control', 'id' => 'brief' )) !!}
                          </td>
                           <td width="10%">
                             {!! Form::number('item[0][quantity]', 1, array('placeholder' => 'Quantity','class' => 'form-control', 'id' => 'quantity', 'min' => 0)) !!}
                          </td>
                          <td width="30%">
                            {!! Form::file('item[0][doc_path]', null, array('placeholder' => 'Uploads','class' => 'form-control', 'id' => 'doc_path')) !!}   
                          </td>
                          <td width="10%">      
                            <button  type="button" class="btn btn-info" id="addrow"><i class="fas fa-plus-square"></i> </button> 
                          </td>
                      </tr>
                  </tbody>
                  </table>
                  <input type="hidden" id="itemCount" name="itemCount" value="1" />
              </div>
              </div>
              <div class="row">
                <div class="col-md-12">&nbsp;</div>
              </div>
              <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Create Auction !</button>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">&nbsp;</div>
              </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

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

  // Specific time picker
  $('#start_time').datetimepicker({
    // format: 'DD/MM/YYYY HH:mm', 
    useCurrent: false,
    showTodayButton: true,
    showClear: true,
    toolbarPlacement: 'bottom',
    sideBySide: true,
  });


  // Add Row to the Item table
  var counter = 1;

  $("#addrow").on("click", function () {
      var newRow = $("<tr>");
      var cols = "";

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

  // copy tile to decription
  $('#auction_title').on('keyup', function(e){
    let title = $(this).val();
    $('#brief').val(title);
  });

});
</script>
@endsection