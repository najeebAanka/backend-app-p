
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



                    <nav>





                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{url('auctions/'.$auction->id)}}"><?= $auction->name ?></a></li>
                            <li class="breadcrumb-item active"><?= $horse->name_en ?></li>
                        </ol>
                    </nav>
                </div><!-- End Page Title -->
                
                
                      @include('dashboard.shared.messages')
                
                <h2 style="color: #fff;margin-bottom: 2rem">Edit lot basic info</h2>
            <form method="post" action="{{url('operations/lot/edit-lot')}}"  >
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="id" value="{{$lot->id}}" />
                                                                    
                <div class="row">
                        
                              
                                                                    <div class="col-xl-3 col-lg-6 col-12">
                                                                             <p> Minimum reservation : </p>
                                                                    <input required class="form-control" type="number" name="min_reservation" value="{{$lot->min_reservation}}" />    
                                
                                                                        
                                                                    </div>   
                                                                    
                                                                    
                                                                       <div class="col-xl-3 col-lg-6 col-12">
                                                                             <p> Selling</p>
                                                                             
                                                                             <select class="form-control" name="target_type">
                                                                                 <option value="horse" {{$lot->target_type == 'horse' ? 'selected' : ''}}>Actual Horse</option>   
                                                                                 <option value="breeding-right" {{$lot->target_type == 'breeding-right' ? 'selected' : ''}}>Breeding right</option>   
                                                                                 
                                                                             </select>
                                                                             
                                
                                                                        
                                                                    </div>   
                                                                    
                       <div class="col-xl-3 col-lg-6 col-12">
                                                                             <p> Lot type</p>
                                                                             
                                                                             <select class="form-control" name="lot_type">
                                                                                 <option value="online" {{$lot->lot_type == 'online' ? 'selected' : ''}}>Online</option>   
                                                                                 <option value="offline" {{$lot->lot_type == 'offline' ? 'selected' : ''}}>Offline</option>   
                                                                                 <option value="silent" {{$lot->lot_type == 'silent' ? 'selected' : ''}}>Silent</option>   
                                                                                 
                                                                             </select>
                                                                             
                                
                                                                        
                                                                    </div>   
                                                                    
                                                                    
                                                                    <div class="col-12">
                                                                        <hr />
                                                                            <button type="submit" class="btn btn-sm btn-success m-1">Update Lot</button>
                                                                    </div>
                                  
                                                                
                          
                             
                    
                </div>             
                                
                        </form>               


            </div>
       




        </main><!-- End #main -->

        @include('dashboard.shared.footer')
        @include('dashboard.shared.js')
 

    </body>

</html>