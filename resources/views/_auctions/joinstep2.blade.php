<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-6">
    <div class="form-group">
      <label for="bid_amount">Enter Your First Bid</label>                      
      {!! Form::text('bid_amount', $auction->start_price, array('placeholder' => 'Enter Bid Amount','class' => 'form-control', 'id' => 'bid_amount')) !!}
      <small class="text-muted mt-1">Amount should be lesser than <b>{{$auction->start_price}} AED</b></small>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6"></div>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="form-group">
    <label for="description">Document Upload</label> 
    <small class="text-muted mt-1">(Format should be any of pdf, jpeg, jpg, xls, xlsx)</small>
    <input type="file" name="file_doc" class="form-control">
  </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6"></div>
</div> 
<div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <div class="icheck-primary">
          <input type="checkbox" id="agreeTerms" name="terms" value="agree">
          <label for="agreeTerms">
           I agree to the <a href="#">terms and conditions</a> of this auction and have read and understood them
          </label>
        </div>
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
            <button type="button" class="btn btn-outline-secondary btnPrevious btn-lg mr-1" data-id="auction">Previous</button>
            <button type="submit" class="btn btn-primary btn-lg">Enter Starting Bid !</button>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>