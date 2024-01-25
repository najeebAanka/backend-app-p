<!DOCTYPE html>
<html lang="en">
<?php $auction = App\Models\Auction::find(Route::input('id'));
$live = 0;
if ($auction->status == 1) {
    $live = \App\Models\AuctionHorseReg::where('auction_id', $auction->id)
        ->whereRaw('(now() between lot_start_date and lot_end_date)')
        ->count();
}

?>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - Auctions | <?= $auction->name ?></title>
    <?php $currentUser = Auth::user(); ?>
    <?php echo $__env->make('dashboard.shared.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Or for RTL support -->
    <style>
        .lot-sold,
        .lot-unsold-no-bids,
        .lot-unsold-low-reservation,
        .lot-unsold {
            background-color: #ebffe7;
        }

        .lot-started {
            background-color: #feffe7;
        }

        .lot-stopped {
            background-color: #ffdddd;
        }

        .list-group .list-group-item {
            border-radius: 0;
            cursor: move;
        }

        .list-group .list-group-item:hover {
            background-color: #f7f7f7;
        }

        .blk-bg {
            animation: blinkingBackground 2s infinite;
        }

        @keyframes  blinkingBackground {
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

        .swas tr td,
        .swas tr th {
            vertical-align: top;
        }
    </style>
</head>

<body>
    <?php echo $__env->make('dashboard.shared.nav-top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('dashboard.shared.side-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <main id="main" class="main">
        <!-- Extra Large Modal -->
        <div class="bg-trans p-2">
            <?php if($currentUser->can('create-lots') && $auction->status == -1): ?>
                <?php if($auction->status == -1): ?>
                    <button type="button" onclick="$('#publishMaster').submit()" class="btn btn-success m-1"
                        style="float: right">
                        <i class="fa fa-globe"></i> Publish auction
                    </button>
                <?php endif; ?>

                <button type="button" class="btn btn-primary m-1" style="float: right" data-bs-toggle="modal"
                    data-bs-target="#ExtralargeModal">
                    <i class="fa fa-plus"></i> Add lots
                </button>
            <?php endif; ?>

            <?php if($currentUser->sellsHorses()): ?>

                <?php if(\Carbon\Carbon::now()->between($auction->entry_start_datetime, $auction->entry_end_datetime)): ?>



                    <button type="button" class="btn btn-success m-1" style="float: right" data-bs-toggle="modal"
                        data-bs-target="#ExtralargeModal">
                        <i class="fa fa-plus"></i> Register a horse in this auction
                    </button>



                    <div class="modal fade" id="ExtralargeModal">
                        <div class="modal-dialog modal-xl">
                            <form method="post" action="<?php echo e(url('operations/auction/lots/add-reg-request')); ?>">
                                <?php echo e(csrf_field()); ?>

                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Register a horse in auction</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Horizontal Form -->

                                        <div class="row row g-3">

                                            <input type="hidden" name="auctionId" value="<?php echo e($auction->id); ?>" />
                                            <div class="col-md-12">
                                                <label for="inputName5" class="form-label">Select horses to add</label>
                                                <select onchange="checkHorseGender()" id="select-horses-multi"
                                                    name="horse_id">
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="inputName5" class="form-label">Select horse lot type</label>
                                                <div class="row">
                                                    <?php if($auction->accepts_online_lots == 1): ?>
                                                        <div class="col-md-4"> <input class="form-radio" checked
                                                                type="radio" name="lot_type" value="online"> Online
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if($auction->accepts_offline_lots == 1): ?>
                                                        <div class="col-md-4"> <input class="form-radio" type="radio"
                                                                name="lot_type" value="offline"> Offline</div>
                                                    <?php endif; ?>
                                                    <?php if($auction->accepts_silent_lots == 1): ?>
                                                        <div class="col-md-4"> <input class="form-radio" type="radio"
                                                                name="lot_type" value="silent"> Silent</div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="inputName5" class="form-label">Select Selling type</label>
                                                <div class="row">

                                                    <div class="col-md-6"> <input class="form-radio" checked
                                                            type="radio" name="target_type" id="will_sell_horse"
                                                            value="horse">Horse</div>


                                                    <div class="col-md-6"> <input class="form-radio" type="radio"
                                                            name="target_type" value="breeding-right"> Breeding right
                                                    </div>



                                                </div>
                                            </div>



                                            <div class="col-md-6" id="horse-preg-container" style="display: none">
                                                <label for="inputName5" class="form-label">Horse Foul
                                                    status</label><br />
                                                <div id="horse-status-container">
                                                    <label for="inputName5" class="form-label mt-1">Status</label><br />
                                                    <input type="checkbox" name="is_pregnant" /> Is Pregnant <br />
                                                    <label for="inputName5" class="form-label mt-1">Pregnant
                                                        from</label><br />
                                                    <input type="text" class="form-control mt-1"
                                                        placeholder="Source" name="pregnant_from" />
                                                    <label for="inputName5" class="form-label mt-1">Delivery due
                                                        date</label><br />
                                                    <input type="date" class="form-control  mt-1"
                                                        placeholder="Due date" name="pregnant_due_date" />

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputName5" class="form-label">Extra notes :
                                                </label><br />
                                                <textarea name="notes" rows="9" class="form-control" placeholder="Any extra notes regarding this horse"></textarea>
                                                <label for="inputName5" class="form-label">Minimum reservation :
                                                </label><br />
                                                <input type="number" name="min_reservation" class="form-control"
                                                    placeholder="Amount" value="0" />
                                            </div>



                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Add selected</button>
                                    </div>
                                </div>
                            </form><!-- End Horizontal Form -->
                        </div>
                    </div><!-- End Extra Large Modal-->
                <?php else: ?>
                    <button type="button" class="btn btn-success m-1" disabled=" "style="float: right">
                        <i class="fa fa-plus"></i> Entry is not available now
                    </button>

                <?php endif; ?>
            <?php endif; ?>

            <?php if($currentUser->can('edit-auctions')): ?>
                <button type="button" class="btn btn-secondary m-1" style="float: right" data-bs-toggle="modal"
                    data-bs-target="#ExtralargeModalSettings">
                    <i class="fa fa-gear"></i> Settings
                </button>
                <a href="<?php echo e(url('tv-banner/' . $auction->id)); ?>" target="_blank" class="btn btn-light m-1" style="float: right">
                    <i class="fa fa-tv"></i> Tv Banner
                </a>
                <a href="<?php echo e(url('tv-banner-and-results/' . $auction->id)); ?>" target="_blank" class="btn btn-light m-1" style="float: right">
                    <i class="fa fa-list"></i> Bids & Tv Banner
                </a>
                <a href="<?php echo e(url('hall-bidders/' . $auction->id)); ?>" class="btn btn-light m-1" style="float: right">
                    <i class="fa fa-users"></i> Hall bidders
                </a>
                <?php if($auction->status == 1): ?>
                    <a href="<?php echo e(url('auction-report/' . $auction->id)); ?>" class="btn btn-light m-1"
                        style="float: right">
                        <i class="fa fa-trophy"></i> Winners
                    </a>

                    <a href="<?php echo e(url('operations/auction-log-generator/' . $auction->id)); ?>" class="btn btn-light m-1"
                        style="float: right">
                        <i class="fa fa-history"></i> Complete log
                    </a>

                <?php endif; ?>
            <?php endif; ?>







            <div class="pagetitle">
                <form method="post" id="publishMaster" action="<?php echo e(url('operations/auction/publish')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <input type="hidden" name="auctionId" value="<?php echo e($auction->id); ?>" />
                </form>

                <h1><?= $auction->name ?></h1>
                <?php if($auction->status == -1): ?>
                    <p>Not published yet !</p>

                <?php endif; ?>
                <nav>





                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('home')); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(url('auctions')); ?>">Auctions</a></li>
                        <li class="breadcrumb-item active"><?= $auction->name ?></li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->



        </div>


        <section class="section dashboard ">
            <?php echo $__env->make('dashboard.shared.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="row">

                <div class="col-md-8">


                    <?php if($auction->status == -1): ?>
                        <?php if(!$currentUser->can('accept-reject-horses')): ?>
                            <?php $lst = App\Models\HorseRegRequest::where('auction_id' ,$auction->id)
                      ->where('status'  ,'<>','accepted') ->where('sent_by' ,$currentUser->id)->get();
                      if(count($lst) > 0 ) {?>
                            <div class="card p-2 ">
                                <p>My pending requests</p>
                                <table class="swas table table-bordered bg-white">
                                    <tr>
                                        <th>Horse</th>
                                        <th>Sent</th>
                                        <th>Type</th>
                                        <th>Selling</th>
                                        <th>Extra notes</th>
                                    <tr>
                                        <?php foreach ($lst as $req){ 
                  $horse=\App\Models\Horse::find($req->horse_id);
                  ?>
                                    <tr>
                                        <td>
                                            <?= $horse->name_en ?><br />
                                            <small><?= $req->selling_type == 'breeding-right' ? ($horse->gender == 'mare' ? 'Embryo of : ' : 'Breeding right from : ') : '' ?>
                                            </small>
                                            <a style="font-size: 12px"
                                                href="<?php echo e(url('horse-details/' . $horse->id)); ?>?redirected=auction&auction_id=<?php echo e($auction->id); ?>">More
                                                details</a>

                                        </td>
                                        <td><?= $req->created_at ?></td>
                                        <td><?= \App\Models\User::find($req->sent_by)->name ?></td>
                                        <td><?= $req->lot_type ?></td>
                                        <td><?= $req->selling_type ?></td>
                                        <td><?= $req->notes ?> <?php if($req->is_pregnant){ ?>
                                            , Pregnant , From <?php echo e($req->pregnant_from); ?>

                                            , Due date <?php echo e($req->pregnant_due_date); ?>

                                            <?php } ?>
                                        </td>





                                    </tr>

                                    <?php } ?>
                                </table>
                            </div>
                            <?php } ?>
                        <?php else: ?>
                            <?php $lst = App\Models\HorseRegRequest::where('auction_id' ,$auction->id)
                      ->where('status' ,'pending')->get();
                      if(count($lst) > 0 ) {?>
                            <div class="card p-2 ">
                                <p>Pending requests</p>
                                <table class="swas table table-bordered bg-white">
                                    <tr>
                                        <th>Horse</th>
                                        <th>Sent</th>
                                        <th>Seller</th>
                                        <th>Type</th>
                                        <th>Selling</th>
                                        <th>Status</th>
                                        <th>Extra notes</th>
                                        <th>Auction</th>

                                    </tr>
                                    <?php foreach ($lst as $req){ 
                  $horse=\App\Models\Horse::find($req->horse_id);
                  ?>
                                    <tr>
                                        <td>
                                            <?= $horse->name_en ?><br />
                                            <small><?= $req->selling_type == 'breeding-right' ? ($horse->gender == 'mare' ? 'Embryo of : ' : 'Breeding right from : ') : '' ?>
                                            </small>
                                            <a style="font-size: 12px"
                                                href="<?php echo e(url('horse-details/' . $horse->id)); ?>?redirected=auction&auction_id=<?php echo e($auction->id); ?>">More
                                                details</a>

                                        </td>
                                        <td><?= $req->created_at ?></td>
                                        <td><?= \App\Models\User::find($req->sent_by)->name ?></td>
                                        <td><?= $req->lot_type ?></td>
                                        <td><?= $req->selling_type ?></td>
                                        <td><?= $req->status ?></td>
                                        <td><?= $req->notes ?> <?php if($req->is_pregnant){ ?>
                                            , Pregnant , From <?php echo e($req->pregnant_from); ?>

                                            , Due date <?php echo e($req->pregnant_due_date); ?>

                                            <?php } ?>
                                        </td>
                                        <td>
                                            <form method="post"
                                                action="<?php echo e(url('operations/auction/accept-lot-join-request')); ?>">
                                                <?php echo e(csrf_field()); ?>

                                                <input type="hidden" name="id" value="<?php echo e($req->id); ?>" />
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>


                                        </td>




                                    </tr>

                                    <?php } ?>
                                </table>
                            </div>
                            <?php } ?>

                        <?php endif; ?>

                    <?php endif; ?>


                    <div class="card p-2 table-container">
                        <p>Approved lots</p>

                        <?php 
                  $all_mixed = \App\Models\AuctionHorseReg::join('horses' ,'horses.id' ,'auction_horse_regs.horse_id')->
                          select(['auction_horse_regs.*' ,'horses.name_en'])->
                          where('auction_id' ,$auction->id)
                 ->orderBy('lot_type')->orderBy('order_sn')->get(); 
                  
                  
                  
                  if(count($all_mixed) ==0){
                      ?>

                        <div class="text-center p-5">

                            <h3>Not lots approved yet
                                <?php if($currentUser->can('create-lots')): ?>

                                    ,get started by adding some lots
                            </h3>


                            <br />
                            <button class="btn btn-primary mt-4" data-bs-toggle="modal"
                                data-bs-target="#ExtralargeModal"><i class="fa fa-plus"></i> Add now </button>
                        <?php else: ?>
                            <h3 />
                            <?php endif; ?>
                        </div>


                        <?php  
                  } 
                  
                  
                  $statuses =[];
                  if($auction->accepts_online_lots =='1')$statuses[]='online';
                  if($auction->accepts_offline_lots =='1')$statuses[]='offline';
                  if($auction->accepts_silent_lots =='1')$statuses[]='silent';
         foreach ($statuses as $status){
             
               $all = \App\Models\AuctionHorseReg::where('auction_id' ,$auction->id)
                 ->where('lot_type' ,$status)
                 ->orderBy('order_sn')->get();
              foreach ($all as $a){
                             $a->horse = App\Models\Horse::find($a->horse_id);
              }
                 if(count($all) > 0) {
             
                  ?>

                        <p style="font-weight: bold;"><?php echo e(ucfirst($status)); ?> lots</p>


                        <?php if($currentUser->can('start-stop-lot') && $status == 'offline'): ?>

                            <form method="post" action="<?php echo e(url('operations/auction/edit')); ?>" id="identifier">
                                <?php echo e(csrf_field()); ?>

                                <input type="hidden" name="auctionId" value="<?php echo e($auction->id); ?>" />


                                <select name="offline_lot_id"
                                    style="    padding: 3px;
    margin: 0px;
    border-radius: 5px;
    border: none;
    ">
                                    <?php foreach ($all as $a){
                          
                            ?>
                                    <option <?php echo e($a->status_string == 'sold' ? 'disabled' : ''); ?>

                                        value="<?php echo e($a->id); ?>">Lot# <?php echo e($a->order_sn + 1); ?>

                                        <?php echo e($a->horse->name_en); ?> (<?php echo e($a->status_string); ?>)</option>
                                    <?php } ?>
                                </select>

                                <button class="btn btn-info m-2 btn-sm" type="submit">Start selected lot</button>
                            </form>
                        <?php endif; ?>

                        <?php if(
                            $currentUser->can('start-stop-lot') &&
                                $status == 'online' &&
                                $auction->status == 1 &&
                                \App\Models\AuctionHorseReg::where('auction_id', $auction->id)->where('status_string', 'created')->count() > 0): ?>

                            <form method="post" action="<?php echo e(url('operations/auction/start-lot-manually')); ?>">
                                <?php echo e(csrf_field()); ?>

                                <b>Manual start : </b> <select name="online_lot_id"
                                    style="    padding: 3px;
    margin: 0px;
    border-radius: 5px;
    border: none;
    ">
                                    <?php foreach ($all as $a){
                          
                            ?>
                                    <option <?php echo e($a->status_string != 'created' ? 'disabled' : ''); ?>

                                        value="<?php echo e($a->id); ?>">Lot# <?php echo e($a->order_sn + 1); ?>

                                        <?php echo e($a->horse->name_en); ?> (<?php echo e($a->status_string); ?>)</option>
                                    <?php } ?>
                                </select>

                                <button class="btn btn-info m-2 btn-sm" type="submit">Start selected lot</button>
                            </form>
                        <?php endif; ?>



                        <table class="table  table-bordered bg-white">
                            <tr>
                                <th>Lot No</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Selling</th>
                                <th>Start/End</th>
                                <th>Bidders</th>
                                <th>
                            </tr>



                            <?php
         
      
       
         foreach ($all as $item){
            
             if($item->lot_type == 'online'){
             

            $item->status = "finished";
            $item->time_remaining = 0;
            $item->status_extra_info = "Sold for 15k AED for Dubai Stud LLC.";
            $item->server_time = Carbon\Carbon::now()->format('Y/m/d h:i a');
             $timerDate = null;

            if ($item->status_string == 'started') {
                $item->status = "live";
                $item->status_extra_info = "";
                $item->time_remaining = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->diffInSeconds(now());
                $timerDate = $item->lot_end_date;
                
            }

           if ($item->status_string == 'created') {
                $item->status = "upcoming";
                  if($item->lot_start_date!=""){
                $item->time_remaining = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)
                        ->diffInSeconds(now());
                      $timerDate = $item->lot_start_date;
                  }else{
                      $timerDate = $auction->start_date;
                  }
                $item->status_extra_info = "";
               
            }

}             
                
             
             ?>
                            <tr
                                class="lot-<?php echo e($item->status_string); ?> <?php echo e($item->status_string == 'started' ? 'blk-bg' : ''); ?>  ">
                                <td><?= $item->order_sn + 1 ?></td>
                                <td><small><?= $item->target_type == 'breeding-right' ? ($item->horse->gender == 'mare' ? 'Embryo of : ' : 'Breeding right from : ') : '' ?>
                                    </small><br /><?= $item->horse->name_en ?>
                                    <a style="font-size: 12px"
                                        href="<?php echo e(url('horse-details/' . $item->horse_id)); ?>?redirected=auction&auction_id=<?php echo e($auction->id); ?>">More
                                        details</a>

                                    <?php if($item->is_pregnant){ ?>
                                    , In Foal , From <?php echo e($item->pregnant_from); ?>

                                    , Due date <?php echo e($item->pregnant_due_date); ?>

                                    <?php } ?>




                                </td>
                                <td>
                                    <?php if($item->lot_type == 'online'): ?>
                                        <i style="opacity: 0.5;
    font-size: 13px;
    margin-right: 5px;"
                                            class="fa fa-volume-up"></i> Online
                                    <?php endif; ?>

                                    <?php if($item->lot_type == 'offline'): ?>
                                        <i style="opacity: 0.5;
    font-size: 13px;
    margin-right: 5px;"
                                            class="fa fa-flag"></i> Offline
                                    <?php endif; ?>


                                    <?php if($item->lot_type == 'silent'): ?>
                                        <i style="opacity: 0.5;
    font-size: 13px;
    margin-right: 5px;"
                                            class="fa fa-volume-off"></i> Silent
                                    <?php endif; ?>

                                    <br />
                                    <small style="color: #5d5d5d"> <?= strtoupper($item->status_string) ?></small>
                                </td>


                                <td><?= $item->target_type ?></td>




                                <td>
                                    <?php echo $item->lot_start_date . '<br />' . $item->lot_end_date; ?>
                                </td>


                                <td><?= App\Models\Bid::where('lot_id', $item->id)->count() ?></td>





                                <td>
                                    <a href="<?php echo e(url('lot-edit/' . $item->id)); ?>"
                                        class="btn btn-warning btn-sm btn-block"><i class="fa fa-edit"></i> Lot
                                        settings</a>
                                    <?php if($item->status_string == 'created'){  ?>


                                    <?php } ?>

                                    <?php if($item->status_string == 'sold' || $item->status_string == 'unsold' || $item->status_string == 'unsold-no-bids'){  ?>
                                    <a href="<?php echo e(url('lot/' . $item->id)); ?>" class="btn btn-success btn-sm btn-block"><i
                                            class="fa fa-trophy"></i> Results</a>
                                    <?php } ?>


                                    <?php if($item->status_string == 'started'){  ?>
                                    <a href="<?php echo e(url('lot/' . $item->id)); ?>"
                                        class="btn btn-primary btn-sm  btn-block"><i class="fa fa-users"></i>
                                        Standings</a>
                                    <?php } ?>


                                    <?php if($item->lot_type == "silent" ){  ?>





                                    <?php if($item->status_string == "created" || $item->status_string == "stopped" ){  ?>
                                    <form method="post" action="<?php echo e(url('operations/auction/start-silent-lot')); ?>">
                                        <?php echo e(csrf_field()); ?>

                                        <input type="hidden" name="lot_id" value="<?php echo e($item->id); ?>" />
                                        <button type="submit"
                                            class="btn btn-success  mt-1 btn-sm  btn-block">Start</button>
                                    </form>
                                    <?php } ?>
                                    <?php if($item->status_string == "started" ){  ?>

                                    <form method="post" action="<?php echo e(url('operations/auction/pause-silent-lot')); ?>">
                                        <?php echo e(csrf_field()); ?>

                                        <input type="hidden" name="lot_id" value="<?php echo e($item->id); ?>" />
                                        <button type="submit"
                                            class="btn btn-danger mt-1 btn-sm  btn-block">Pause</button>

                                    </form>



                                    <?php } ?>

                                    <?php } ?>






                                </td>

                            </tr>

                            <?php
         }
         
         ?>

                        </table>
                        <?php } } ?>

                    </div>




                </div>

                <div class="col-md-4">



                    <div class="card  p-2">
                        <img class="responsive-img" style="width: 100%" src="<?php echo e($auction->buildPoster()); ?>" />







                        <?php $cnt = 0; ?>
                        <?php if($currentUser->can('edit-lots')): ?>
                            <?php if($auction->status == -1): ?>


                                <button class="btn btn-primary mt-3" onclick="reCalculateTimes(this)"><i
                                        class="fa fa-clock"></i> Calculate Times</button>

                                <p style="text-align: center;
    padding: 1rem;
    color: brown;"><strong>Reorder
                                        lots</strong></p>


                                <div id="sortablelist" class="list-group mb-4 mt-3" data-id="list1">

                                    <?php
   
        foreach ($all_mixed as $item){
            $cnt++;
            ?>
                                    <div class="list-group-item d-flex align-items-center justify-content-between"
                                        data-id="<?php echo e($item->id); ?>">
                                        <div>
                                            <p class="mb-0 d-inline-flex align-items-center">
                                                <?php echo e($item->order_sn + 1); ?>) <?php echo e($item->name_en); ?> (<?php echo e($item->lot_type); ?>)
                                            </p>
                                        </div>
                                    </div>
                                    <?php } ?>


                                </div>





                                <button class="btn btn-success mt-3" onclick="updateLotsOrder(this)">Save
                                    changes</button>
                            <?php else: ?>
                                <p
                                    style="background-color: #ffe5e5;
    color: red;
    margin-top: 1rem;
    padding: 1rem;
    text-align: center;">
                                    You can not change order of lots when auction is live</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>


                </div>

            </div>
        </section>


        <?php if($currentUser->can('create-lots')): ?>
            <div class="modal fade" id="ExtralargeModal">
                <div class="modal-dialog modal-xl">
                    <form method="post" action="<?php echo e(url('operations/auction/lots/add-bulk')); ?>" id="identifier">
                        <?php echo e(csrf_field()); ?>

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add lots to auction</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Horizontal Form -->

                                <div class="row row g-3">

                                    <input type="hidden" name="auctionId" value="<?php echo e($auction->id); ?>" />


                                    <div class="col-md-12">
                                        <label for="inputName5" class="form-label">Select lots type</label>
                                        <div class="row">
                                            <?php if($auction->accepts_online_lots == 1): ?>
                                                <div class="col-md-4"> <input class="form-radio" checked
                                                        type="radio" name="lots_type" value="online"> Online</div>
                                            <?php endif; ?>
                                            <?php if($auction->accepts_offline_lots == 1): ?>
                                                <div class="col-md-4"> <input class="form-radio" type="radio"
                                                        name="lots_type" value="offline"> Offline</div>
                                            <?php endif; ?>
                                            <?php if($auction->accepts_silent_lots == 1): ?>
                                                <div class="col-md-4"> <input class="form-radio" type="radio"
                                                        name="lots_type" value="silent"> Silent</div>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="inputName5" class="form-label">Select Selling type</label>
                                        <div class="row">

                                            <div class="col-md-6"> <input class="form-radio" checked type="radio"
                                                    name="target_type" id="will_sell_horse" value="horse"> Horse
                                            </div>


                                            <div class="col-md-6"> <input class="form-radio" type="radio"
                                                    name="target_type" value="breeding-right"> Breeding right</div>



                                        </div>
                                    </div>



                                    <div class="col-md-12">
                                        <label for="inputName5" class="form-label">Select horses to add</label>
                                        <select id="select-horses-multi" name="horses[]" multiple="multiple">
                                        </select>
                                    </div>



                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add selected</button>
                            </div>
                        </div>
                    </form><!-- End Horizontal Form -->
                </div>
            </div><!-- End Extra Large Modal-->
        <?php endif; ?>

        <?php if($currentUser->can('edit-auctions')): ?>

            <div class="modal fade" id="ExtralargeModalSettings">
                <div class="modal-dialog modal-xl">
                    <form method="post" action="<?php echo e(url('operations/auction/edit')); ?>" onsubmit="populateQuiil()"
                        enctype="multipart/form-data">
                        <?php echo e(csrf_field()); ?>

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Auction settings</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Horizontal Form -->
                                <input type="hidden" name="auctionId" value="<?php echo e($auction->id); ?>" />




                                <div class="row">
                                    <div class="col-md-8">


                                        <?php if($auction->status == -1): ?>
                                            <div
                                                style="    border: solid 1px #ccc;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 1rem;
    background-color: #fff6f3;">
                                                <div class="row row g-3">
                                                    <div class="col-md-6">
                                                        <label for="inputEmail5" class="form-label">Auction Start
                                                            date</label>
                                                        <input type="date" class="form-control"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?> name="start_date"
                                                            value="<?= Carbon\Carbon::parse($auction->start_date)->format('Y-m-d') ?>">
                                                    </div>

                                                    <div class="col-md-6 "> <label for="inputEmail5"
                                                            class="form-label">Auction Start time </label>
                                                        <input type="time" class="form-control"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?>

                                                            value="<?= Carbon\Carbon::parse($auction->start_date)->format('H:i') ?>"
                                                            name="start_time">
                                                    </div>



                                                    <div class="col-md-6">
                                                        <label for="inputName5" class="form-label">Auction lot
                                                            duration (Minutes)</label>
                                                        <input type="number" name="lot_duration"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?> class="form-control"
                                                            placeholder="Lot duration in minutes"
                                                            value="<?php echo e($auction->lot_duration); ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="inputAddress5" class="form-label">Lots interval
                                                            (Minutes)</label>
                                                        <input type="number" name="auction_interval"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?> class="form-control"
                                                            placeholder="Lot interval in minutes"
                                                            value="<?php echo e($auction->auction_interval); ?>">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="inputName5" class="form-label">Remind before start
                                                            (Minutes)</label>
                                                        <input type="number" name="remind_start_before"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?> class="form-control"
                                                            placeholder="Lot before start in minutes"
                                                            value="<?php echo e($auction->remind_start_before); ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="inputAddress5" class="form-label">Remind before
                                                            finish (Minutes)</label>
                                                        <input type="number" name="remind_end_before"
                                                            <?php echo e($live == 0 ? '' : 'disabled'); ?> class="form-control"
                                                            placeholder="Remind before end in minutes"
                                                            value="<?php echo e($auction->remind_end_before); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="row row g-3">


                                            <div class="col-md-12">
                                                <label for="inputName5" class="form-label">Auction title</label>
                                                <input type="text" class="form-control" name="title"
                                                    value="<?= $auction->name ?>">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputAddress5" class="form-label">Short
                                                    description</label>
                                                <input type="text" class="form-control" name="description"
                                                    placeholder="Auction description"
                                                    value="<?= $auction->description ?>">
                                            </div>



                                            <div class="col-md-6">
                                                <label for="inputEmail5" class="form-label">Entry Start date</label>
                                                <input type="date" class="form-control"
                                                    value="<?= Carbon\Carbon::parse($auction->entry_start_datetime)->format('Y-m-d') ?>"
                                                    name="entry_start_date">
                                            </div>


                                            <div class="col-md-6">
                                                <label for="inputEmail5" class="form-label">Entry Start time</label>
                                                <input type="time" class="form-control"
                                                    value="<?= Carbon\Carbon::parse($auction->entry_start_datetime)->format('H:i') ?>"
                                                    name="entry_start_time">
                                            </div>



                                            <div class="col-md-6">
                                                <label for="inputPassword5" class="form-label">Entry End date</label>
                                                <input type="date" class="form-control"
                                                    value="<?= Carbon\Carbon::parse($auction->entry_end_datetime)->format('Y-m-d') ?>"
                                                    name="entry_end_date">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputEmail5" class="form-label">Entry End time</label>
                                                <input type="time" class="form-control"
                                                    value="<?= Carbon\Carbon::parse($auction->entry_end_datetime)->format('H:i') ?>"
                                                    name="entry_end_time">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="inputCity" class="form-label">Entry fee (Sellers)</label>
                                                <input type="number" class="form-control"
                                                    value="<?= $auction->entry_fee ?>" name="entry_fee">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="inputCity" class="form-label">Deposit (Bidders)</label>
                                                <input type="number" class="form-control"
                                                    value="<?= $auction->required_deposit ?>" name="deposit">
                                            </div>



                                            <div class="col-md-3">
                                                <label for="inputZip" class="form-label">Vat (%)</label>
                                                <input type="number" class="form-control"
                                                    value="<?= $auction->vat ?>" name="vat">
                                            </div>


                                            <div class="col-md-3">
                                                <label for="inputZip" class="form-label">Currency</label>
                                                <input type="text" class="form-control"
                                                    value="<?= $auction->currency ?>" name="currency">
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                <label for="inputZip" class="form-label">Auction poster</label>
                                                <input type="file" class="form-control" name="poster">
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                              <label class="form-label">Tv Banner Background</label>
                                              <input type="file" class="form-control"
                                                  name="tv_banner_bg">
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                              <label class="form-label">Live Bids Page Background</label>
                                              <input type="color" class="form-control" value="<?= $auction->live_bids_main_bg ?>"
                                                  name="live_bids_main_bg">
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                              <label class="form-label">Live Bids Modal Background</label>
                                              <input type="color" class="form-control" value="<?= $auction->live_bids_bg ?>"
                                                  name="live_bids_bg">
                                            </div>

                                            <div class="col-md-12">
                                                <label for="inputZip" class="form-label">Live stream link</label>
                                                <input type="text" class="form-control"
                                                    value="<?= $auction->stream_url ?>" name="stream_url">
                                            </div>








                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inputZip" class="form-label">Terms and conditions</label>
                                        <div id="editor" style="       max-height: 25rem;"><?php echo $auction->terms; ?>

                                        </div>

                                        <textarea id="hiddenArea" style="display: none" name="terms"><?php echo $auction->terms; ?></textarea>
                                    </div>

                                </div>

                                <div class="row row g-3" style="margin-top: 1rem;">


                                    <div class="col-md-12">
                                        <label for="inputName5" class="form-label">Auction bid buttons list</label>
                                        <input type="hidden" name="bid_buttons_json" id="bid-buttons-json"
                                            value="<?php echo e($auction->bidding_buttons); ?>" />
                                        <table id="bid-buttons" class="table table-bordered">
                                            <tr>
                                                <th>
                                                    Button Value
                                                </th>
                                                <th>
                                                    Starts at
                                                </th>
                                                <th>
                                                    Hides at
                                                </th>
                                                <th></th>

                                            </tr>
                                            <tbody id="bids-buttons-tbody">

                                            </tbody>
                                            <tr>
                                                <td colspan=100% style="text-align: center;background-color: #f0fff8">
                                                    <label style="color: green">Add new</label>


                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <input class="form-control" type="number" placeholder="Value"
                                                        id="input-bid-button-value" />
                                                </th>
                                                <th>
                                                    <input class="form-control" type="number" placeholder="Start"
                                                        id="input-bid-button-start" />
                                                </th>
                                                <th>
                                                    <input class="form-control" type="number" placeholder="End"
                                                        id="input-bid-button-end" />
                                                </th>
                                                <th><button type="button" class="btn btn-warning "
                                                        onclick="addBidButton()">Save</button></th>

                                            </tr>
                                        </table>

                                    </div>



                                </div>




                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </form><!-- End Horizontal Form -->
                </div>
            </div><!-- End Extra Large Modal-->

        <?php endif; ?>
    </main><!-- End #main -->

    <?php echo $__env->make('dashboard.shared.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('dashboard.shared.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- SortableJS CDN -->
    <script src="https://raw.githack.com/SortableJS/Sortable/master/Sortable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function renderBiddingButtons() {
            let str = $('#bid-buttons-json').val();
            let json = JSON.parse(str);
            let res = "";
            for (var i = 0; i < json.length; i++) {
                res += "<tr><td>" + json[i].value + "</td><td>" + json[i].show_after + "</td><td>" + json[i].hide_after + "</td><td>\n\
    <button class=\"btn btn-danger btn-sm btn-remove-bid-button\" data-value='" + json[i].value +
                    "'>Remove</button></td></tr>";
            }

            $('#bids-buttons-tbody').html(res);

            $('.btn-remove-bid-button').click(function() {

                let val = $(this).attr('data-value');

                let str = $('#bid-buttons-json').val();
                let json = JSON.parse(str);
                let new_json = [];
                let res = "";
                for (var i = 0; i < json.length; i++) {
                    if (json[i].value != val) {
                        new_json.push(json[i]);
                    }
                }
                $('#bid-buttons-json').val(JSON.stringify(new_json));
                renderBiddingButtons();

            });

        }


        function addBidButton() {
            let str = $('#bid-buttons-json').val();
            let json = JSON.parse(str);
            json.push({
                value: parseFloat($('#input-bid-button-value').val()),
                show_after: parseFloat($('#input-bid-button-start').val()),
                hide_after: parseFloat($('#input-bid-button-end').val())
            });
            $('#bid-buttons-json').val(JSON.stringify(json));
            renderBiddingButtons();
            $('#input-bid-button-value').val("")
            $('#input-bid-button-start').val("")
            $('#input-bid-button-end').val("")
        }


        <?php if($currentUser->can('edit-lots')): ?> <?php if($cnt > 0): ?>   
       <?php if($live == 0): ?>

let  sortable = new Sortable(sortablelist, {
   animation: 150,
   ghostClass: 'sortable-ghost' ,
 

   
 });
 
 
 function updateLotsOrder(c){
     c.disabled=true;
   let arr = sortable.toArray();
   
   $.ajax({
    url: server + '/operations/lots/reorder',
    dataType: 'json',
    type: 'post',
    contentType: 'application/json',
    data: JSON.stringify( { "orderArray": arr, "auction-id": <?= $auction->id ?> } ),
    processData: false,
    success: function( data, textStatus, jQxhr ){
        console.log(data);
        location.reload();
    },
    error: function( jqXhr, textStatus, errorThrown ){
              console.log(errorThrown);
    }
});
   
   
   
   
 }
  function reCalculateTimes(c){
     c.disabled=true;
   
   
   $.ajax({
    url: server + '/operations/lots/update-times',
    dataType: 'json',
    type: 'post',
    contentType: 'application/json',
    data: JSON.stringify( { "auctionId": <?= $auction->id ?> } ),
    processData: false,
    success: function( data, textStatus, jQxhr ){
        location.reload();
    },
    error: function( jqXhr, textStatus, errorThrown ){
              console.log(errorThrown);
    }
});
   
   
   
   
 } <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>

        function getTargetType() {
            console.log("queriying.." + document.getElementById('will_sell_horse').checked)
            return (document.getElementById('will_sell_horse').checked ? 'horse' : 'breeding');
        }

        $('#select-horses-multi').select2({
            dropdownParent: $('#ExtralargeModal'),
            theme: 'bootstrap-5',
            ajax: {
                url: function() {
                    return server + '/operations/ajax/horses?auction=' + <?= $auction->id ?> + '&target=' +
                        getTargetType();
                },
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });


        function checkHorseGender(c) {

            if ($("#select-horses-multi option:selected").text().endsWith("mare)")) {
                console.log("Mare")
                $('#horse-preg-container').show('swing');
            } else {
                console.log("Stallion")
                $('#horse-preg-container').hide('swing');
            }
        }
    </script>
    <?php if($currentUser->can('edit-auctions')): ?>
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            var quill = new Quill('#editor', {
                theme: 'snow'
            });

            function populateQuiil() {

                $("#hiddenArea").val($("#editor").find('.ql-editor').html());
                return true;
            }

            renderBiddingButtons();

            <?php endif; ?>
        </script>
</body>

</html>
<?php /**PATH C:\wamp64\www\test\resources\views/dashboard/pages/auctions-single.blade.php ENDPATH**/ ?>