
<!DOCTYPE html>
<html lang="en">
<?php $auction = App\Models\Auction::find(Route::input('id')); 
$live = 0;
if($auction->status == 1)
   $live = \App\Models\AuctionHorseReg::where('auction_id' ,$auction->id)
                        ->whereRaw('(now() between lot_start_date and lot_end_date)')
                        ->count();

?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Auction report |  <?=$auction->name?></title>
  <?php $currentUser = Auth::user(); ?>
@include('dashboard.shared.css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Or for RTL support -->
<style>

            .lot-sold ,.lot-unsold-no-bids,.lot-unsold-low-reservation ,.lot-unsold{
                background-color: #ebffe7;
            }
            .lot-started{
                background-color: #feffe7;
            }
              .lot-stopped{
                  background-color: #ffdddd;
            }
.list-group .list-group-item {
  border-radius: 0;
  cursor: move;
}

.list-group .list-group-item:hover {
  background-color: #f7f7f7;
}

	.blk-bg{
		animation: blinkingBackground 2s infinite;
	}
	@keyframes blinkingBackground{
		0% {
    background-color: #beffb1;
    font-weight: bold;
}
50% {
    background-color: #d3ffd5;
    color: #075f0a;
    font-weight: bold;
}
100% {
    background-color: #beffb1;
    font-weight: bold;
}
	}

        .swas tr td ,.swas tr th{
            vertical-align: top;
        }
        
        .bv{
            float: right;
    font-size: 0.8rem !important;
    font-weight: normal;
    padding: 6.2px;
    background: #ffeaea;
    border-radius: 5px;
    margin-right: 5px;
        }
        
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
    <!-- Extra Large Modal -->
    <div class="bg-trans p-2">
       
       
  
       
      @if($currentUser->can('edit-auctions'))   
  
      <a  href="{{url('auctions/'.$auction->id)}}" class="btn btn-warning m-1" style="float: right" >
             <i class="fa fa-gavel"></i> Auction lots
     </a> 
       @endif
       
       
       
    
    
   
    
    <div class="pagetitle">
      
        
      <h1><?=$auction->name?></h1>
         @if($auction->status==-1)
         <p>Not published yet !</p> 
         
         @endif
      <nav>
          
          

          
          
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{url('auctions')}}">Auctions</a></li>
          <li class="breadcrumb-item active">Report of <?=$auction->name?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->    
        
        
        
    </div> 
  

    <section class="section dashboard ">
         @include('dashboard.shared.messages')
        
      <div class="row">
         
          <div class="col-md-6">
              
              
            
              
              
              <div class="card p-2 table-container">
                  <p>Lots sold</p>
                  
                  <?php 
                  
                  
                  $users =  \App\Models\LotWinningRecord::where('auction_id' ,$auction->id)
                    ->select(['winner_id' ,'winner_type'])->groupBy(['winner_id' ,'winner_type'])->get();
    foreach ($users as $u){
        $name = "";
        $email = "";
        if($u->winner_type == 'user'){
              $user = \App\Models\User::find($u->winner_id);
              $name = $user->name;
              $email = $user->email;
        }else{
           $name = "Hall bidder (". $u->winner_id.")";
        }
                   ?> 
                  
                  <table class="table table-bordered bg-white mb-5">
                      <tr>
                          <th colspan="100%" >
                         
                              <p style="Execludefont-size: 1.5rem">  Mr <?=$name?> </p>  
                          
                          </th>
                      </tr>
                      <tr>
                           <tr>
                               <th>Horse name</th>
                               <th>Lot number</th>
                               <th>Owner</th>
                               <th>Amount</th>
                              
                      </tr>
                      </tr>
                      <?php
                          $all_mixed = \App\Models\LotWinningRecord::where('auction_id' ,$auction->id)
                    ->where('winner_id' ,$u->winner_id)->where('winner_type' ,$u->winner_type)->orderBy('id')->get(); 
                      foreach ($all_mixed as $a){ 
                          $horse = \App\Models\Horse::find($a->horse_id);
                          $lot = App\Models\AuctionHorseReg::find($a->lot_id);
                         
                          ?>
                      <tr>
                          <td>
                              
                           <small><?=$a->selling_type == 'breeding-right' ? ($horse->gender == 'mare' ? "Embryo of : " : "Breeding right from : ") : "" ?>
                 </small><br /><?= $horse->name_en?>
                    <a style="font-size: 12px" href="{{url('horse-details/'.$horse->id)}}?redirected=auction&auction_id={{$auction->id}}">More details</a>
                          
                          
                          </td>
                          <td>{{$lot->order_sn+1}}</td>
                          <td>{{$horse->owner_name}}</td>
                          <td>{{$a->amount}} {{$a->currency}}</td>
                      </tr>
                      <?php } ?>
                      <tr>
                          <td colspan="100%">
                              
                                   <form method="get" target="blank" action="{{url('remote-operations/invoices/generate-lots-winner-invoice/'.$u->winner_id.'/'.$u->winner_type.'/'.$auction->id)}}">
                              <button class="btn btn-success btn-sm"   style="float: right">Generate invoice</button>
                              <span class="bv">Exclude deposit <input type='checkbox' name="exc-deposit"  /></span>
                              <p  > Invoice options </p>  
                              </form>
                            
                              
                          </td>
                      </tr>
                      
                      <tr>
                          <td colspan="100%">
                         
                              <p style="font-weight: bold">Generated invoices</p>
                              
                              <?php 
                              $all = App\Models\Invoice::where('user_id' ,$u->winner_id)
                                      ->where('user_type' ,$u->winner_type)->where('auction_id' ,$auction->id)->get() ;
                              if(count($all) == 0) echo "<p>Invoice is not generated yet !</p>";
                              foreach ($all as $i){ ?>
                              <div class="p-3" style="background-color: #f9f9f9">
                                  <a class="btn btn-link btn-sm" href="{{url('remote-operations/invoices/view/'.$i->id)}}" style="float: right">View</a>
                                  
                              <p  >{{$i->gen_id}}</p>
                              @if($email!="")
                                 <form method="post" action="{{url('remote-operations/invoices/email-lots-winner-invoice/'.$i->id)}}">
                            {{csrf_field()}}
                                     <table class="table table-bordered">
                                         <tr>
                                             <td> <p  >Email invoice </p> </td>
                                             <td><input   type="email" name="email" value=" {{$email}}" class="form-control" /></td>
                                             <td style="text-align: right">   <button class="btn btn-primary  btn-block"  >Send</button></td>
                                             
                                         </tr>
                                         
                                     </table>
                                  
                                   
                              </form>
                              @endif
                              <p style="font-weight: bold">Email history</p>
                              <ul>
                              <?php 
                              $hist = \App\Models\InvoiceEmailHistory::where('inv_id' ,$i->id)->get();
                              if(count($hist) == 0)echo "<li>This invoice was not emailed before</li>";
                              foreach ( $hist as $h){ ?>
                                  <li>Sent to {{$h->email}} at  {{$h->created_at}}</li>
                              <?php } ?>
                                  </ul>
                              </div>
                                      <?php } ?>
                          </td>
                      </tr>
                      
                      
                  </table>
                  
                  
                  
                <?php  
   } ?> 
                  
              </div>
              
              
      
  
          </div>
   
      </div>
    </section>

  
  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>

</html>