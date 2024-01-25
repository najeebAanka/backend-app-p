
<!DOCTYPE html>
<html lang="en">
    <?php
    $lot = App\Models\AuctionHorseReg::find(Route::input('id'));
    $auction = App\Models\Auction::find($lot->auction_id);
    $horse = App\Models\Horse::find($lot->horse_id);
    $max = \App\Models\Bid::where('lot_id', $lot->id)->where('status', 1)->max('curr_amount');
    ?>
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <?php $currentUser = Auth::user(); ?>
        <title>Dashboard - Lots |  <?= $horse->name_en ?></title>

        @include('dashboard.shared.css')
        <!-- Or for RTL support -->
        <style>

            .lot-finished{
                background-color: #ebffe7;
            }
            .lot-live{
                background-color: #feffe7;
            }
            .list-group .list-group-item {
                border-radius: 0;
                cursor: move;
            }

            .list-group .list-group-item:hover {
                background-color: #f7f7f7;
            }
            .flag-img{
                width: 20px;
            }



            #max-amount {

                margin-top: 1rem;
                color: green;
                text-align: center;
                font-weight: bold;
            }
            span.tmrds {
                color: #aeaeae;
                font-size: 11px;
            }

            .bid-status--1 td{
                text-decoration: line-through;
                background-color: #ffe5ea !important;
            }
            p#ea-label {
                text-align: center;
                font-weight: bold;
                color: #795548;
                font-size: 1.2rem;
            }
        </style>
    </head>

    <body>
        @include('dashboard.shared.nav-top')

        @include('dashboard.shared.side-nav')
        <main id="main" class="main">
            <!-- Extra Large Modal -->

            <div class="bg-trans p-2">

                <div class="pagetitle">


                    <a class="btn btn-sm btn-light" style="float: right" target="blank"
                       
                       href="{{url('lot-tv-banner/'.$lot->id)}}"><i class="fa fa-tv"></i> Show on TV banner</a>
                    <nav>





                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{url('auctions/'.$auction->id)}}"><?= $auction->name ?></a></li>
                            <li class="breadcrumb-item active"><?= $horse->name_en ?></li>
                        </ol>
                    </nav>
                </div><!-- End Page Title -->





                <?php
                $item = $lot;

                $item->status = $item->status_string;

                $item->server_time = Carbon\Carbon::now()->format('Y/m/d h:i a');

                $item->horse = App\Models\Horse::find($item->horse_id);
                if ($item->lot_type == 'online') {
                    $timerDate = null;
                    $item->start_time_formatted = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)->format('Y/m/d h:i a');
                    $item->end_time_formatted = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->format('Y/m/d h:i a');

                    if ($item->status_string == 'started') {
                        $item->status = "live";
                        $item->status_extra_info = "";
                        $item->time_remaining = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->diffInSeconds(now());
                        $timerDate = $item->lot_end_date;
                        ?>

                        <?php
                    }

                    if ($item->status_string == 'created') {
                        $item->status = "upcoming";
                        $item->time_remaining = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)
                                ->diffInSeconds(now());
                        $item->status_extra_info = "";
                        $timerDate = $item->lot_start_date;
                    }
                }
                ?>
                <div class="m-1 p-3 bg-white" style="border-radius: 5px;">
                    <div class="row"  style="    font-size: 1.7rem;
                         font-weight: bold;" >
                        <div class="col-md-4">Lot : <?= $item->order_sn + 1 ?></div>
                        <div class="col-md-4" style="text-align: center"><?= $item->horse->name_en ?>
                            </div>


                  
                        <div class="col-md-4" style="text-transform: capitalize;color: #795548;text-align: right ">
<?= $item->status_string; ?>
                            <!--<--> </div>
                        
                              @if($item->lot_type == 'online')

                              <div class="col-md-12" style="font-size: 14px">
                                  
                                  <p style="font-size: 12px;float: right;color: green">Visited : {{$item->visits}} times
                                  </p>
                                  from <b><?= $item->lot_start_date ?> </b> to <b><?= $item->lot_end_date ?></b></div>



                        @endif

                    </div> </div>    





            </div>
            <section class="section dashboard">

                @include('dashboard.shared.messages')
                <div class="row">
                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-12 col-sm-12">

    @if($currentUser->can('e-bid-in-lot') && $item->lot_type != 'online' && $item->status_string == 'started' )
                        <div class="card  p-2 text-center" >  
                            <p style="    font-weight: bold;
                               color: gray;"><i class="fa fa-gavel"></i> E Auctioneer tools 
                            
                            </p>

                                <span id="ea-step" style="display: none">1</span> <span id="ea-holder" style="display: none">0</span>
                        
                            <p id="ea-label">Enter (Madrab) number and press enter</p>
                            <input style=" padding: 1.8rem;
                                   text-align: center;
font-weight: bold;
    font-size: 1.7rem;
                                   border: none;
                                   background: transparent;" class="form-control" type="number" id="ea-input" placeholder="Hall bidder number" />



                        </div>
@endif


                        <div class="card  p-2" >   
<?php
$img = $auction->buildPoster();
$pf = \App\Models\HorseMultimedia::where('horse_id', $lot->horse_id)->first();
if ($pf) {
    $img = url('storage/horses-gallery/' . $pf->media_link);
}
?>

                            <img class="responsive-img" style="width: 100%;max-width: 100px;" src="{{ $img }}" />







                            <p style="color: #795548;
                               font-weight: bold;
                               margin-top: 1rem;
                               ">Seller information</p>
<?php
$seller = \App\Models\User::find($horse->seller_id);
if ($seller) {
    ?>
                                <table class="table table-bordered bg-white">
                                    <tr>
                                        <th>Seller name</th>
                                        <td>{{$seller->name}}</td>

                                    </tr>     
                                    <tr>
                                        <th>Join date</th>
                                        <td>{{$seller->created_at}}</td>

                                    </tr> 

                                    <tr>
                                        <th>Phone </th>
                                        <td>{{$seller->phone}}</td>

                                    </tr> 
                                    <tr>
                                        <th>Email </th>
                                        <td>{{$seller->email}}</td>

                                    </tr> 



                                </table> 
<?php } ?>


                            <p style="color: #795548;
                               font-weight: bold;
                               margin-top: 1rem;
                               ">Horse statistics</p>

                                <table class="table table-bordered bg-white">
                              
                                        
                                                 <?php if($item->is_pregnant){ ?>
                                            
                                   <tr><td colspan="100%">               
                        <br /> Pregnant <br /> From {{$item->pregnant_from}}
                        <br /> Due date {{$item->pregnant_due_date}}
                        
                           </td></tr>
                        
                       <?php } ?>
                                            
                                     
                                    <tr>
                                        <th>Auctions joined</th>
                                        <td><?= App\Models\AuctionHorseReg::where('horse_id', $horse->id)->count() ?></td>

                                    </tr>     

                                    <tr>
                                        <th>Lowest Bid Achieved</th>
                                        <td><?=
    DB::select('SELECT min(bids.curr_amount) as v from bids join auction_horse_regs on auction_horse_regs.id '
            . '= bids.lot_id WHERE bids.status=1 and auction_horse_regs.horse_id=?'
            , [$horse->id])[0]->v;
    ?></td>

                                    </tr>     

                                    <tr>
                                        <th>Highest Bid Achieved</th>
                                        <td><?=
                                        DB::select('SELECT max(bids.curr_amount) as v from bids join auction_horse_regs on auction_horse_regs.id = bids.lot_id '
                                                . 'WHERE  bids.status=1 and auction_horse_regs.horse_id=?'
                                                , [$horse->id])[0]->v;
    ?></td>

                                    </tr>      


                                </table> 

                                 @if($currentUser->can('edit-lots'))   
                                 <p> Minimum reservation : </p>
                                 <form method="post" action="{{url('operations/lot/edit-lot')}}"  >
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="id" value="{{$item->id}}" />
                                     
                                                                    <input required class="form-control" type="number" name="min_reservation" value="{{$item->min_reservation}}" />    
                              
                                                                    <button type="submit" class="btn btn-sm btn-success m-1">Update value</button>
                                 </form>
                             
                                
                                        @endif



                        </div>


                    </div>
                    <div class="col-xxl-9 col-xl-6 col-lg-6 col-md-12 col-sm-12">

                        <div class="row">
                            <div class="col-xxl-5 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                  @if($currentUser->can('extend-lot-time'))
                                @if($item->lot_type == 'online' && $item->status == 'live')
                                <div class="card p-3 " >
                                    <p style="    color: black;
                                       font-weight: bold;"><i class="fa fa-clock"></i> Extend lot time</p>
                                    <table>
                                        <tr>
                                            <td>Minutes to extend</td>
                                            <td><input class="form-control" id="extend_amount" type="number" min="1" max="1440" value="60" placeholder="Minutes ?" /></td>
                                            <td><button class="btn btn-success btn-sm m-1" onclick="extend(this)">Extend</button></td>
                                        </tr>
                                    </table>

                                </div>     
                                @endif  
                                 @endif  
                                 
                                 
                                 
                                     @if($currentUser->can('count-and-finish-lot')) 
                                @if($item->lot_type != 'online' && $item->status_string == 'started')
                                <div class="card p-3 " >
                                    <p>Lot operations</p>
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td><button id="count1-btn" class="count-btn btn btn-light"><i class="fa fa-gavel"></i> First count</button></td>   
                                                <td><button id="count2-btn"  class="count-btn btn btn-light" disabled><i class="fa fa-gavel"></i> Second count</button></td>   
                                                <td><button id="count3-btn"  class="count-btn btn btn-light" disabled><i class="fa fa-gavel"></i> Third count</button></td>   


                                            </tr> 


                                            <tr>
                                                <td colspan="100%">
                                                    <hr />

                                                    <p>Finishing this lot will allow you to start a new one</p>
                                                    <table style="width: 100%">
                                                        <tr>
                                                            <td>     <form method="post" action="{{url('operations/auction/finish-lot')}}" id="identifier">
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="lot_id" value="{{$item->id}}" />
                                                                    <input type="hidden" name="status" value="" />

                                                                    <button onclick="return confirm('Are you sure  ? ')" type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-flag"></i> Set as finished (Sold)</button>

                                                                </form></td>
                                                            <td style="text-align: right">

                                                                <form method="post" action="{{url('operations/auction/finish-lot')}}" id="identifier">
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="lot_id" value="{{$item->id}}" />
                                                                    <input type="hidden" name="status" value="unsold" />

                                                                    <button onclick="return confirm('Are you sure  ? ')" type="submit" class="btn btn-danger  btn-sm  btn-block"><i class="fa fa-flag"></i> Set as finished (Unsold)</button>

                                                                </form>     

                                                            </td>

                                                        </tr>

                                                    </table> 


                                                </td>

                                            </tr>

                                        </table>

                                    </div>


                                </div>



                                @endif
 @endif




                                <div class="card p-3 " style="    max-height: 100rem;
                                     overflow-y: auto;">

                                    @if($item->status_string == 'sold' )
                                    <h2>Auction closed </h2>     

<?php
$winnerBid = App\Models\Bid::where('lot_id', $item->id)->where('status', 1)->orderBy('curr_amount', 'desc')->first();
if ($winnerBid) {
    if($winnerBid->bid_source==""){
    $user = \App\Models\User::find($winnerBid->user_id);
    if($user){
    ?>

                                        <p style="background-color: #4CAF50;
                                           color: #fff;
                                           padding: 1rem;
                                           border-radius: 5px;">Winner :     {{$user->name ." (".$max." ".$auction->currency.") "}} </p>
<?php }else{ ?>
        <p style="background-color: #4CAF50;
                                           color: #fff;
                                           padding: 1rem;
                                           border-radius: 5px;">Winner :     Deleted Account ! </p>
    

                                        
                                        
                                        <?php
    
    
    
    
} }else{   ?>
          <p style="background-color: #4CAF50;
                                           color: #fff;
                                           padding: 1rem;
                                           border-radius: 5px;">Winner : Hall bidder No ({{$winnerBid->bid_source}}){{" (".$max." ".$auction->currency.") "}} </p>
    
 <?php   
}}
                                       ?>
                                    @endif


                                    @if($item->status_string == 'unsold-no-bids' ) 
                                    <h2 style="background-color: #ffeaea;
                                        padding: 1rem;
                                        color: red;
                                        border-radius: 5px;">No one visited this lot ! , Horse is unsold</h2>


                                    @endif 







                                   
                                    <div style="max-height: 20rem;    background-color: #fff;
                                         padding: 1rem;border-radius: 5px;
                                         overflow-y: auto;">
                                        
                                         <p style="    color: #795548;
                                       font-weight: bold;">Lot updates</p>
                                    <hr />
                                        
                                        <ul id="lot-updates" style="text-align: left!important">

                                            <?php
                                            $events = App\Models\LotUpdate::where('lot_id', $item->id)->orderBy('id', 'desc')->get();
                                            if (count($events) == 0)
                                                echo '<p>No updated so far..</p>';
                                            foreach ($events as $e) {
                                                if ($e->text_color == 'normal') {
                                                    ?>    
                                                    <li><span>{{$e->contents}} </span><span class="tmrds"><i class="fa fa-clock"></i> <?php
                                                            echo \App\Http\Controllers\Helpers\TimeUtils::humanTiming($e->created_at);
                                                            ?> ago.</span></li>
                                                <?php } else {
                                                    ?>
                                                    <li><b style="color: green">Announcement : {{$e->contents}}</b> <span class="tmrds"><i class="fa fa-clock"></i> <?php
                                                            echo \App\Http\Controllers\Helpers\TimeUtils::humanTiming($e->created_at);
                                                            ?> ago.</span></li>   
                                                    <?php
                                                }
                                            }
                                            ?>

                                        </ul>
                                    </div>

                                    
                                      @if($currentUser->can('send-announcement-in-lot')) 
                                    @if($item->status_string == 'started' )

                                    <hr />  


                                    <p style="    color: #795548;
                                       font-weight: bold;"><i class="fa fa-radio"></i> Send an announcement</p>

                                    <textarea class="form-control" rows="2" placeholder="Send a message to bidders" id="announce-text"></textarea>
                                    <button class="btn btn-primary btn-sm mt-2" onclick="sendUpdate(this)">Send</button>

                                    @endif     @endif
                                </div>

      <div     class="card p-2  table-container">
                                        
                              
          <p style="font-weight: bold">Interested bidders </p>
               <hr />
                                 <ul>
                                     <?php
                                     
                                         foreach (App\Models\User::whereIn('id' ,
                                                 \App\Models\Favourite::
                                                 
                                                    where(function($q) use ($item){
            $q->where('target_id' ,$item->auction_id)->where('target_type' ,1);
       })
       ->orWhere(function($q) use ($item){
            $q->where('target_id' ,$item->id)->where('target_type' ,2);
       })
                                                 ->select(['user_id'])->groupBy('user_id')
                                             ->pluck('user_id')->toArray())->get() as $u){
                                     
                                     
                                     ?>
                                     <li><a href="{{url('users/'.$u->id)}}">{{$u->name}}</a></li>
                                             <?php } ?>
                                     
                                 </ul>
                                    </div> 

                            </div>    
                            <div class="col-xxl-7 col-xl-12 col-lg-12 col-md-12 col-sm-12">
  @if($currentUser->can('start-stop-lot')) 
                                <?php
                                $all = \App\Models\AuctionHorseReg::where('auction_id', $auction->id)
                                                ->where('lot_type', 'offline')
                                                ->orderBy('order_sn')->get();
                                ?>             
                                @if(count($all) > 0)

                                <form method="post" action="{{url('operations/auction/edit')}}" id="identifier">
                                    {{csrf_field()}}
                                    <input type="hidden" name="auctionId" value="{{$auction->id}}" />
                                    <input type="hidden" name="redrirect_self" value="true" />
                                    <table class="table table-bordered">
                                        <tr>

                                            <td>Start new lot</td>
                                            <td> <select name="offline_lot_id" style="    padding: 3px;
                                                         margin: 0px;
                                                         border-radius: 5px;
                                                         border: none;
                                                         ">
                                                             <?php
                                                             foreach ($all as $a) {
                                                                 $a->horse = App\Models\Horse::select(['name_en'])->find($a->horse_id);
                                                                 ?>
                                                        <option {{$a->status_string == 'sold' ? 'disabled' : '' }} value="{{$a->id}}">Lot# {{$a->order_sn+1}} {{$a->horse->name_en}} ({{$a->status_string}})</option>
<?php } ?> 
                                                </select></td>
                                            <td>   <button class="btn btn-info m-2 btn-sm" type="submit">Start selected lot</button> </td>
                                        </tr>

                                    </table> 




                                </form>
                                @endif

   @endif
                                <div class="card p-2 table-container" style="    max-height: 100rem;
                                     overflow-y: auto;">
                                    
                                    <div >
                                        @if($currentUser->can('edit-lots'))   
                                        <div style="float: right"  >
                                            <input name='lot_id'   type="hidden" value="{{$item->id}}" />
                                            Allow outbid :  <input onchange="updateBidPermValue(this)" type='checkbox' {{$item->can_outbid=='-1' ? "" : "checked"}} />
                                        </div>
                                        @endif
                                        
                                    <?php if ($max) { ?>
                                        <h2 ><span id="max-amount"><?= $max ?></span> {{$auction->currency}}</h2>
                                    <?php } else { ?>
                                        <h2 ><span id="max-amount">-/-</span> {{$auction->currency}}</h2>


                                        <?php }
                                    ?>
                                    </div>



                                    <table class="table  table-borderless table-striped bg-white">     

                                        <tr>

                                            <th colspan="2">Bidder</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Source</th>
                                            <th>Amount</th>
                                            <th></th>

                                        </tr>
                                        <tbody id="bidders-table">


                                        </tbody>

                                    </table>  
                            

                                </div> 
     



                            </div>    

                        </div>



                    </div>



                </div>
            </section>




        </main><!-- End #main -->

        @include('dashboard.shared.footer')
        @include('dashboard.shared.js')
        <!-- SortableJS CDN -->
        <script>



        </script>

        @if($currentUser->can('count-and-finish-lot') &&  $item->lot_type != 'online' && $item->status_string == 'started')
        <script>
              

            function resetCounters(){
            $('.count-btn ').attr('disabled', true);
            $('#count1-btn').attr('disabled', false);
            }

            $('#count1-btn').click(function(){
            $(this).attr('disabled', true);
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/send-update')}}",
                    data:{"lot_id" : {{$item -> id}}, "text" : "Auctioneer count One !" },
                    success:function(data){
                    $('#count2-btn').attr('disabled', false);
                    }
            });
            });
            $('#count2-btn').click(function(){
            $(this).attr('disabled', true);
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/send-update')}}",
                    data:{"lot_id" : {{$item -> id}}, "text" : "Auctioneer count Two !" },
                    success:function(data){
                    $('#count3-btn').attr('disabled', false);
                    }
            });
            });
            $('#count3-btn').click(function(){
            $(this).attr('disabled', true);
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/send-update')}}",
                    data:{"lot_id" : {{$item -> id}}, "text" : "Auctioneer count Three !" },
                    success:function(data){

                    }
            });
            });
        </script> 






        @endif


 @if($currentUser->can('cancel-bid-of-user'))
        <script>




            function cancelBid(id, c){
            if (confirm('Are you sure you want to cancel this bid ? ')){
            c.disabled = true;
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/bids/cancel')}}",
                    data:{"id" : id },
                    success:function(data){
                    resetCounters();
                    if ($.isEmptyObject(data.error)){
                    console.log(data.success);
                    } else{
                    console.log(data.error);
                    }
                    }
            });
            }

            }
            
            
        </script><!-- comment -->
  @endif      
        
        <script>
            
            function renderBids(data) {
            console.log(data);
            let str = "";
            if (data.length > 0){
            for (var i = 0; i < data.length; i++) {
            str += "<tr class='bid-status-" + data[i].status + "'><td><img class='flag-img' src='" + data[i].country_flag +
                    "' /></td><td>\n\
        ";
    str+= (data[i].source == 'Online' ?  "<a style=\"font-weight: bold\" target=\"blank\" href=\"{{url('users')}}/" +
            data[i].user_id + "\">" + data[i].name + "</a>" : data[i].name) ;
    str+="</td>\n\
              <td>" + data[i].date + "</td><td>" + data[i].time + "</td><td>" + data[i].source + "</td><td>" + data[i].amount + "</td>";
            @if ($currentUser->can('cancel-bid-of-user') && $item -> status_string == 'started')
                    str += "<td><button class='btn btn-outline-danger btn-sm btn-cancel'><i class='fa fa-multiply' onclick='cancelBid(" + data[i].id + " ,this)'></i></button></td>";
            @endif
                    str += "</tr>";
            }
            } else{
            str = "<tr><td style='text-align: center;font-weight: bold;color: gray;font-size : 1.7rem' colspan='100%'><hr /><p>No bidders so far</p></td></tr>";
            }
            $('#bidders-table').html(str);
            }



            function renderUpdates(data){
            let str = "";
            for (var i = 0; i < data.length; i++) {
            if (data[i].text_color == 'normal')
                    str += "<li><span>" + data[i].contents + " </span><span class=\"tmrds\"><i class=\"fa fa-clock\"></i> " + data[i].time_formatted + " </span></li>";
            else
                    str += "<li><span><b style=\"color: green\">Announcement : " + data[i].contents + " </b> </span><span class=\"tmrds\"><i class=\"fa fa-clock\"></i> " + data[i].time_formatted + " </span></li>";
            }
            $('#lot-updates').html(str);
            }
</script>


   @if($currentUser->can('send-announcement-in-lot')) 
   
   <script>

            function sendUpdate(c){
            let text = $('#announce-text').val();
            if (text.trim() != ""){
            c.disabled = true;
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/send-update')}}",
                    data:{"lot_id" : {{$item -> id}}, "text" : text },
                    success:function(data){
                    c.disabled = false;
                    if ($.isEmptyObject(data.error)){
                    console.log(data.success);
                    } else{
                    console.log(data.error);
                    }
                    }
            });
            }
            $('#announce-text').val("");
            }
</script> 
@endif
   @if($currentUser->can('edit-lots')) 
   
   <script>

            function updateBidPermValue(c){
                console.log(" changing to  : "  + (c.checked ? 1:-1) );
            c.disabled = true;
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/change-outbid-permission')}}",
                    data:{"lot_id" : {{$item -> id}}, "can_outbid" : c.checked ? 1:-1 },
                    success:function(data){
                    c.disabled = false;
                    if ($.isEmptyObject(data.error)){
                    console.log(data.success);
                    } else{
                    console.log(data.error);
                    }
                    }
            });
        
            }
</script> 
@endif



   @if($currentUser->can('extend-lot-time')) 
<script>
            function extend(c){
            let text = $('#extend_amount').val();
            if (text.trim() != ""){
            c.disabled = true;
            $.ajax({
            type:'POST',
                    url:"{{url('operations/auction/lots/extend')}}",
                    data:{"id" : {{$item -> id}}, "amount" : text },
                    success:function(data){
                    c.disabled = false;
                    if ($.isEmptyObject(data.error)){
                    console.log(data.success);
                    } else{
                    console.log(data.error);
                    }
                    }
            });
            }


            }
</script>
@endif


<script>

<?php
$bids = \App\Models\Bid::where('lot_id', $item->id)->orderBy('id', 'desc')->get();
$list = [];
foreach ($bids as $b) {
    $s = $b->buildObject();
    $list[] = $s;
}
?>
            let initalData = JSON.parse('<?= json_encode($list) ?>');
            renderBids(initalData);
        </script>





        @if($item->status == 'live' || $item->status_string == 'started' )

        <script>
            window.laravel_echo_port = '6001';
        </script>
        <script src="https://test.com:6001/socket.io/socket.io.js"></script>
        <script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
        <script type="text/javascript">
        
    
    @if($currentUser->can('e-bid-in-lot') && $item->lot_type != 'online' || $item->status_string == 'started' )
            $('#ea-input').keypress(function (e) {
            var key = e.which;
            if (key == 13)  // the enter key code
            {
            if ($('#ea-input').val().trim() == "")return false;
            let step = $('#ea-step').html().trim();
            console.log(step)
                    if (step == "1"){
            $('#ea-label').html("Enter (Bidding) amount and press enter");
            $('#ea-holder').html($('#ea-input').val());
            $('#ea-input').val("");
            $('#ea-input').attr("placeholder", "Bid Amount?");
            $('#ea-step').html("2")
            }
            
            
            if (step == "2"){

             let amount = parseFloat($('#ea-input').val());
            let target = parseFloat($('#ea-holder').html());
            $('#ea-holder').html(0);
            let mx = parseFloat($('#max-amount').html() != "-/-" ?  $('#max-amount').html() : 0);
            $.ajax({
            type:'POST',
                    url:"{{url('operations/lots/submitBid')}}",
                    data:{"bid_source" :  target, "lot_id" : {{$item -> id}}, "inc_amount" : amount, "target_amount" : (amount + mx) },
                    success:function(data){

                    if ($.isEmptyObject(data.error)){

                    $('#ea-label').html("Enter (Madrab) number and press enter");
                    $('#ea-input').attr("placeholder", "Hall bidder number");
                    $('#ea-input').val("");
                    $('#ea-step').html("1")

                    } else{
                    console.log(data.error);
                    }
                    }
            });
            }
            return false;
            }
            });
        @endif    
            
            
            var i = 0;
            window.Echo.channel('auction-rooms-<?= $item->id ?>')
                    .listen('.NewBidPlaced', (data) => {
                    i++;
                    //            $("#notification").append('<div class="alert alert-success">'+i+'.'+data.title+'</div>');

                    renderBids(data.payload.bids_list);
                    console.log(data.payload)
                            $('#max-amount').html(data.payload.top_bid);
                    @if ($item -> lot_type != 'online' && $item -> status_string == 'started')
                            resetCounters();
                    @endif

                    })
                    .listen('.LotUpdatePublished', (data) => {
                    i++;
                    //            $("#notification").append('<div class="alert alert-success">'+i+'.'+data.title+'</div>');
                    console.log(data.payload)
                            renderUpdates(data.payload);
                    //   $('#max-amount').html(data.payload.top_bid + " AED");
                    })
                    .listen('.LotTimeExtended', (data) => {

                    console.log("data lot extedned")
                            console.log(data)



                            //   $('#max-amount').html(data.payload.top_bid + " AED");
                    });









        </script>
        @endif


    </body>

</html>