<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - Auctions</title>
    <?php $currentUser = Auth::user(); ?>
    @include('dashboard.shared.css')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        span.sm-text {
            font-size: 11px;
        }

        .hovy:hover {
            background: linear-gradient(0deg, transparent 0%, #fff0eb 98%);
        }

        .nb-news {
            position: absolute;
            /* top: 0rem; */
            /* margin-right: 1rem; */
            text-align: center;
            font-size: 11px;
            /* bottom: 15rem; */
            bottom: 0;
            right: 0;
            left: 0;

        }



        .blk-bg {
            animation: blinkingBackground 2s infinite;
        }

        @keyframes blinkingBackground {
            0% {
                background-color: #fff;
            }

            50% {
                background-color: #c5ffc7;
            }

            100% {
                background-color: #fff;
            }
        }
    </style>
</head>

<body>
    @include('dashboard.shared.nav-top')

    @include('dashboard.shared.side-nav')
    <main id="main" class="main">
        <!-- Extra Large Modal -->
        <div class="bg-trans p-2">

            @if ($currentUser->can('create-auctions'))
                <button type="button" class="btn btn-primary" style="float: right" data-bs-toggle="modal"
                    data-bs-target="#ExtralargeModal">
                    <i class="fa fa-plus"></i> Create a new auction
                </button>
            @endif


            <div class="pagetitle">


                <h1>Auctions</h1>
                <nav>





                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Auctions</li>
                    </ol>

                </nav>



            </div><!-- End Page Title -->
        </div>

        <section class="section dashboard">

            @include('dashboard.shared.messages')


            <div class="row">
                <!--          <table class="table table-bordered bg-white">
              <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                    <th>Total lots</th>
                    <th>Live Lots</th>
                    <th>Finished</th>
                    <th>Total bidders</th>
                     <th>Published</th>
                  <th></th>
        
                  
              </tr>    -->
                <?php
         $data = \App\Models\Auction::where('status' ,'>' ,-5)->orderBy('id' ,'desc');
       
         
          $data=   $data  ->paginate(20);
          if(count($data) == 0){
              ?>

                <div class="text-center p-5">

                    <h3>No auctions published so far !</h3>
                </div>

                <?php
              
          }
         foreach ( $data  as $u){
             $live = \App\Models\AuctionHorseReg::where('auction_id' ,$u->id)
                        ->where('status_string' ,'started')
                        ->count();
             ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <a href="{{ url('auctions/' . $u->id) }}" style="color: #000">
                        <div class="card p-3 hovy">

                            @if ($live > 0)
                                <span class="blk-bg"
                                    style=" 
                          position: absolute;
    background-color: #ffffff;
    color: #08730c;
    font-weight: bold;
    z-index: 100;
    top: -6.9px;
    left: -6px;
    border-radius: 1rem;
    padding: 0.7rem;
  ">{{ $live }}
                                    Live now</span>
                            @endif


                            @if ($u->status == -1)
                                <span
                                    style=" 
                          position: absolute;
background-color: #F44336;
    color: #ffffff;
    font-weight: bold;
    z-index: 100;
    top: -6.9px;
    left: -6px;
    border-radius: 1rem;
    padding: 0.7rem;
  ">
                                    @if ($currentUser->isAdmin()) Not published
                                        <i> (<?= App\Models\HorseRegRequest::where('status', 'pending')
                                            ->where('auction_id', $u->id)
                                            ->count() ?> requests)</i>
                                    @else
                                        @if (\Carbon\Carbon::now()->between($u->entry_start_datetime, $u->entry_end_datetime))
                                            Open for registration
                                        @else
                                            Registration is not available
                                        @endif


                                    @endif
                                </span>






                            @endif

                            <div>
                                <div
                                    style="    width: 100%;
  
    height: 12rem;
    position: relative;
       background: url('<?= $u->buildPoster() ?>');
    background-size: 100% auto;
    background-repeat: no-repeat;
    background-position: center;
 ">



                                    <div class="nb-news">
                                        <?= $u->buildNotificationBarObject()->title ?></div>


                                </div>
                            </div>
                            <div style="text-align: center;padding: 1rem;font-weight: bold;min-height: 5rem">
                                <?= $u->name ?></div>
                            <div style="text-align: center;">Accepts :
                                <?= $u->accepts_online_lots == 1 ? 'Online,' : '' ?>
                                <?= $u->accepts_offline_lots == 1 ? 'Offline,' : '' ?>
                                <?= $u->accepts_silent_lots == 1 ? 'Silent,' : '' ?></div>
                            <div class="text-center" style="color: #666666;font-size: 12px">
                                <i class="fa fa-clock"></i> Starting date :
                                <?= $u->start_date ?>
                            </div>

                            <div style="   
    padding: 0.5rem;
    margin-top: 1rem;">
                                <div class="row">

                                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                        <i class="fa fa-horse" style="color: #ea5151"></i> <br /> <span
                                            class="sm-text">Lots </span> <br />
                                        <p style="    font-size: 1.5rem;">
                                            <?= \App\Models\AuctionHorseReg::where('auction_id', $u->id)->count() ?>
                                        </p>
                                    </div>



                                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                        <i class="fa fa-clock" style="color: #ea9751"></i><br /> <span class="sm-text">
                                            Live </span> <br />
                                        <p style="    font-size: 1.5rem;"> <?= $live ?>
                                        </p>
                                    </div>


                                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                        <i class="fa fa-check" style="color: #3db3c3"></i> <br /> <span
                                            class="sm-text">Sold </span><br />
                                        <p style="    font-size: 1.5rem;"> <?= \App\Models\AuctionHorseReg::where('auction_id', $u->id)
                                            ->where('status_string', 'sold')
                                            ->count() ?>
                                        </p>
                                    </div>

                                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12 text-center">
                                        <i class="fa fa-user" style="color: #3dc342"></i><br /> <span class="sm-text">
                                            Bdrs </span><br />
                                        <p style="    font-size: 1.5rem;"> <?= \App\Models\Bid::where('auction_id', $u->id)
                                            ->groupBy('user_id')
                                            ->count() ?>
                                        </p>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </a>
                </div>

                <?php
         }
         
         ?>

                <!--          </table>-->
                <div class="d-flex">
                    {!! $data->links() !!}
                </div>

                @if ($currentUser->can('create-auctions'))
                    <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <form method="post" id="identifier" action="{{ url('operations/auctions/create') }}"
                                enctype="multipart/form-data" onsubmit="populateQuiil()">
                                {{ csrf_field() }}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Create a new auction</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Horizontal Form -->
                                        <div class="row">
                                            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12">
                                                <div class="row row g-3">


                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                        <label for="inputName5" class="form-label">Auction title</label>
                                                        <input type="text" class="form-control" name="title">
                                                    </div>
                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                        <label for="inputAddress5" class="form-label">Short
                                                            description</label>
                                                        <input type="text" class="form-control" name="description"
                                                            placeholder="Auction description">
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputEmail5" class="form-label">Auction Start
                                                            date</label>
                                                        <input type="date" class="form-control" name="start_date">
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputEmail5" class="form-label">Auction Start
                                                            time</label>
                                                        <input type="time" class="form-control" name="start_time">
                                                    </div>





                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputEmail5" class="form-label">Entry Start
                                                            date</label>
                                                        <input type="date" class="form-control"
                                                            name="entry_start_date">
                                                    </div>


                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputEmail5" class="form-label">Entry Start
                                                            time</label>
                                                        <input type="time" class="form-control"
                                                            name="entry_start_time">
                                                    </div>


                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputPassword5" class="form-label">Entry End
                                                            date</label>
                                                        <input type="date" class="form-control"
                                                            name="entry_end_date">
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputEmail5" class="form-label">Entry End
                                                            time</label>
                                                        <input type="time" class="form-control"
                                                            name="entry_end_time">
                                                    </div>




                                                    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputCity" class="form-label">Accepts online
                                                            lots</label>
                                                        <br /> <input type="checkbox"
                                                            onchange="if(this.checked){$('.online-div').show()}else{$('.online-div').hide()}"
                                                            class="form-checkbox" checked name="accepts_online">
                                                    </div>



                                                    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputCity" class="form-label">Accepts offline
                                                            lots</label>
                                                        <br /> <input type="checkbox" class="form-checkbox"
                                                            name="accepts_offline">
                                                    </div>



                                                    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputCity" class="form-label">Accepts silent
                                                            lots</label>
                                                        <br /> <input type="checkbox" class="form-checkbox"
                                                            name="accepts_slient">
                                                    </div>



                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 online-div">
                                                        <label for="inputState" class="form-label">Interval
                                                            (Minutes)</label>
                                                        <!--                  <select id="inputState" class="form-select">
                    <option selected>Choose...</option>
                    <option>...</option>
                  </select>-->
                                                        <input required value="0" type="number"
                                                            class="form-control" name="interval">
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 online-div">
                                                        <label for="inputZip" class="form-label">Lot duration
                                                            (Minutes)</label>
                                                        <input required value="1" type="number"
                                                            class="form-control" name="lot_duration">
                                                    </div>

                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputCity" class="form-label">Entry fee
                                                            (Sellers)</label>
                                                        <input type="number" class="form-control" value="0"
                                                            name="entry_fee">
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputCity" class="form-label">Deposit
                                                            (Bidders)</label>
                                                        <input type="number" class="form-control" value="0"
                                                            name="deposit">
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputZip" class="form-label">Vat (%)</label>
                                                        <input type="number" class="form-control" value="0"
                                                            name="vat">
                                                    </div>


                                                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputZip" class="form-label">Currency</label>
                                                        <select class="form-control" name="currency">

                                                            <option value="AED">AED</option>
                                                            <option value="$">USD</option>
                                                            <option value="EUR">EURO</option>

                                                        </select>
                                                    </div>




                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                        <label for="inputZip" class="form-label">Auction
                                                            poster</label>
                                                        <input type="file" class="form-control" name="poster">
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                        <label class="form-label">Tv Banner Background</label>
                                                        <input type="file" class="form-control"
                                                            name="tv_banner_bg">
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                      <label class="form-label">Live Bids Page Background</label>
                                                      <input type="color" class="form-control"
                                                          name="live_bids_main_bg">
                                                    </div>
                                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                                                      <label class="form-label">Live Bids Modal Background</label>
                                                      <input type="color" class="form-control"
                                                          name="live_bids_bg">
                                                    </div>
                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" checked
                                                                name="redirect_flag">
                                                            <label class="form-check-label" for="gridCheck">
                                                                Take me to auction details page after creation
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                                                <label for="inputZip" class="form-label">Terms and conditions</label>


                                                <div id="editor" style="       max-height: 29rem;">


                                                    <ol>
                                                        <li>
                                                            <p><strong>Bidding</strong>: All bidders must be registered
                                                                before the auction starts. Bidding will start at a set
                                                                minimum price and increase in set increments until the
                                                                highest bidder wins the auction.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Payment</strong>: The successful bidder must pay
                                                                the full amount due within a specified time frame,
                                                                typically 24-48 hours after the auction ends. Payment
                                                                can be made by cash, check, or bank transfer.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Ownership</strong>: The horse will become the
                                                                property of the successful bidder upon receipt of full
                                                                payment.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Health</strong>: The horses are sold "as is" with
                                                                no guarantee of health or fitness for any particular
                                                                purpose. It is the buyer's responsibility to arrange for
                                                                a veterinarian examination and to be satisfied with the
                                                                horse's condition before making a bid.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Withdrawal</strong>: The auctioneer reserves the
                                                                right to withdraw any horse from the sale at any time
                                                                for any reason.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Reserves</strong>: The auctioneer may set a
                                                                reserve price on any horse. If the reserve price is not
                                                                met, the horse will not be sold.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Commission</strong>: The auctioneer may charge a
                                                                commission on the sale of the horse, which will be
                                                                deducted from the final sale price.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Dispute resolution</strong>: Any disputes arising
                                                                from the auction will be resolved through arbitration in
                                                                accordance with the laws of the state where the auction
                                                                took place.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Liability</strong>: The auctioneer and the seller
                                                                are not responsible for any injury or damage to any
                                                                person or property during the auction.</p>
                                                        </li>
                                                        <li>
                                                            <p><strong>Finality</strong>: All sales are final and there
                                                                are no returns or exchanges.</p>
                                                        </li>
                                                    </ol>


                                                </div>


                                                <textarea id="hiddenArea" style="display: none" name="terms">
                           
                           </textarea>



                                            </div>

                                        </div>



                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Create auction</button>
                                    </div>
                                </div>
                            </form><!-- End Horizontal Form -->
                        </div>
                    </div><!-- End Extra Large Modal-->
                @endif
            </div>
        </section>

    </main><!-- End #main -->

    @include('dashboard.shared.footer')
    @include('dashboard.shared.js')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow'
        });

        function populateQuiil() {

            $("#hiddenArea").val($("#editor").find('.ql-editor').html());
            return true;
        }
    </script>
</body>

</html>
