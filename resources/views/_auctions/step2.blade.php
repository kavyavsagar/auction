<div class="row">
  <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
  <div class="col-md-12">
    <h4 >List Items</h4>
    <table id="itemTable" class="table order-list">
    <thead>
        <tr>
            <!-- <th width="10%">Item #</th> -->
            <th width="40%">Description</th>
            <th width="10%">Qty</th>
            <th width="30%">Uploads</th>
            <th width="10%">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <!-- <td width="10%">
              {!! Form::number('item[0][part_no]', null, array('placeholder' => 'Number','class' => 'form-control', 'id' => 'part_no', 'min' => 0)) !!}
            </td> -->
            <td width="40%">
              {!! Form::text('item[0][brief]', null, array('placeholder' => 'Description','class' => 'form-control', 'id' => 'brief' )) !!}
            </td>
             <td width="10%">
               {!! Form::number('item[0][quantity]', null, array('placeholder' => 'Quantity','class' => 'form-control', 'id' => 'quantity', 'min' => 0)) !!}
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
  <div class="col-md-4">
  	<label>Invite Multiple Emails:</label><br/>
	  <div class="entry input-group incr">
        <input class="form-control participantids" name="invite_emails[]" type="text" placeholder="Enter Email Address" />
    	<span class="input-group-btn">
            <button class="btn btn-success invite-new" type="button">
                <i class="fas fa-plus-square"></i>
            </button>
        </span>
    </div>
    <div class="clone-priv d-none">
	    <div class="entry input-group decr mt-2">
	        <input class="form-control participantids" name="invite_emails[]" type="text" placeholder="Enter Email Address" />
	    	  <span class="input-group-btn">
	            <button class="btn btn-danger invite-del" type="button">
	                <i class="fas fa-minus-square"></i>
	            </button>
	        </span>
	    </div>
    </div>
  </div>
  <div class="col-md-2"></div>
  <div class="col-md-6">
    <div class="form-group d-none">
        <label>Load Bulk Participants:</label><br/>
        {!! Form::file('participants', null, array('placeholder' => 'Uploads','class' => 'form-control', 'id' => 'participants')) !!}   
        <p class="small text-muted mt-1">Format should be in any of (xls, xlsx)</p>
        <div id="file-error" class="text-danger mt-1"></div>
        <span class="text-danger">{{ $errors->first('participants') }}</span>           
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
           <button type="button" class="btn btn-outline-secondary btn-lg btnPrevious mr-1" data-id="basic">Previous</button>
           <button type="button" class="btn btn-outline-primary btnNext btn-lg" data-id="summary">Preview Summary</button>
        </div>
    </div>
</div>