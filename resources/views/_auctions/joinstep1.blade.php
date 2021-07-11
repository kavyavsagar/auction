  <div class="row">
    <div class="col-md-12">&nbsp;</div>
  </div>   
  <div class="row">       
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">
             <i class="far fa-file-alt mr-2"></i>
              Auction Details
            </h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">  
            @php
              $start_time = date("j M Y, g:i a", strtotime($auction->start_time)); 
              $end = strtotime($auction->end_time);
              $end_time = date("j M Y, g:i a", $end); 
              $current_time = strtotime(Carbon\Carbon::now());
            @endphp 
            <dl class="row">
             <dt class="col-sm-4">Bid Type</dt><dd class="col-sm-8">
            @if($auction->bid_type == 'continuous')
              {{$auction->bid_type}} auction
            @else
              aunction in {{$auction->bid_type}}
            @endif
             </dd>
             <dt class="col-sm-4">Auction Title</dt><dd class="col-sm-8">{{$auction->title}}</dd>
             <dt class="col-sm-4">Reference No</dt><dd class="col-sm-8">{{$auction->reference_no}}</dd>
             <dt class="col-sm-4">Descripion</dt><dd class="col-sm-8">{{$auction->description}}</dd>
             <dt class="col-sm-4">Start Price</dt><dd class="col-sm-8">AED {{$auction->start_price}}</dd>  
             <dt class="col-sm-4">Start Time</dt><dd class="col-sm-8">{{$start_time}} (Duration : {{$auction->duration}} hr)</dd>
             <dt class="col-sm-4">End Time</dt><dd class="col-sm-8">{{$end_time}}</dd>
             @if($end < $current_time)<dt class="col-sm-4">Time Left</dt><dd class="col-sm-8">Expired</dd>@endif

             <dt class="col-sm-4">File Uploads</dt><dd class="col-sm-8">
               @foreach ($auctionFiles as $key => $files)
                  @if($files->file_path)
                    <a href="{{ asset('storage/'.$files->file_path) }}" target="_blank"><i class="fas fa-external-link-alt"></i> Doc {{$key +1}}</a>
                    <br/>                  
                  @endif
               @endforeach 
             </dd>
            </dl>
          </div>
          <!-- /.card-body -->    
        </div>
    </div>
    </div>
    <div class="row"> 
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                 <i class="fas fa-list mr-2"></i>
                  List Items
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body"> 
                <table class="table">
                <thead>
                    <tr>
                        <th width="40%">Description</th>
                        <th width="10%">Qty</th>
                        <th width="30%">Uploads</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($auctionItem as $key => $item)
                  <tr>
                 
                    <td>{{$item->brief}} </td>
                    <td>{{$item->quantity}} </td>
                    <td>@if($item->doc_path)
                      <a href="{{ asset('storage/'.$item->doc_path) }}" target="_blank"><i class="fas fa-external-link-alt"></i> View Doc</a>
                    @else
                      --
                    @endif
                    </td>
                  </tr>
                  @endforeach 
                </tbody>
                </table>
              </div>
          <!-- /.card-body -->    
        </div>
    </div>     
  </div>  
   <div class="row">
    <div class="col-md-12">&nbsp;</div>
  </div> 
  <div class="row">       
  <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="form-group float-right">          
          <button type="button" class="btn btn-outline-primary btnNext btn-lg" data-id="sellerbid">Next</button>
      </div>
  </div>
</div>
