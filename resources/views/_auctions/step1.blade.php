<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="form-group clearfix">
        <div class="icheck-primary d-inline mr-2">
          <input type="radio" id="continuous" name="bid_type" value="continuous" checked="" class="bid_type">
          <label for="continuous">Continuous Auction </label>
        </div>
        <div class="icheck-primary d-inline">
          <input type="radio" id="round" name="bid_type" value="round" class="bid_type">
          <label for="round">Round Auction</label>
        </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="auction_title">Auction Title</label>                       
      {!! Form::text('title', null, array('placeholder' => 'Enter Auction Title','class' => 'form-control', 'id' => 'auction_title')) !!}
    </div>
  </div>
  <div class="col-md-6">
<!--     <div class="form-group">
      <label for="reference_no">Reference No</label>                      
      {!! Form::text('reference_no', null, array('placeholder' => 'Enter Reference No','class' => 'form-control', 'id' => 'reference_no')) !!}
    </div> -->
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
<!-- <div class="row">
  <div class="col-md-12">
   <div class="form-group">
    <label for="description">Document Upload</label> 
    <small class="text-muted mt-1">(Format should be any of pdf, jpeg, jpg, xls, xlsx) </small>
    <div class="input-group control-group increment" >
      <input type="file" name="filenames[]" class="form-control filemany">
      <div class="input-group-append"> 
        <button class="btn btn-success add-file" type="button"><i class="fas fa-plus-square"></i></button>
      </div>
    </div>
    <div class="clone d-none">
      <div class="control-group input-group decrement mt-3">
        <input type="file" name="filenames[]" class="form-control filemany">
        <div class="input-group-append"> 
          <button class="btn btn-danger btn-del-f" type="button"><i class="fas fa-minus-square"></i></button>
        </div>
      </div>
    </div>

  </div>
  </div>
</div>  -->
<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label for="start_price">Start Price (AED)</label>                       
      {!! Form::number('start_price', null, array('placeholder' => 'Enter Start Price','class' => 'form-control', 'id' => 'start_price', 'min' => 0)) !!}
    </div>
  </div>
   <div class="col-md-4">
    <div class="form-group">
      <label for="start_price">Minimum Step (AED)</label>                       
      {!! Form::number('min_step', null, array('placeholder' => 'Minimum difference b/w bid amounts','class' => 'form-control', 'id' => 'start_price', 'min' => 0)) !!}
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="budget">Budget (AED)</label>                      
      {!! Form::number('budget', null, array('placeholder' => 'Enter Budget','class' => 'form-control', 'id' => 'budget', 'min' => 0)) !!}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="start_time">Start Time</label>                       
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
      <label for="duration">Duration (HRS)</label>                      
      {!! Form::text('duration', null, array('placeholder' => 'Enter Auction Duration (Hrs)','class' => 'form-control', 'id' => 'duration')) !!}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div> 
<div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="form-group float-right">          
          <button type="button" class="btn btn-outline-primary btnNext btn-lg" data-id="invite">Next</button>
      </div>
  </div>
</div>