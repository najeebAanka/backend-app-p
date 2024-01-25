<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horse;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\TimeUtils;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{

    public function getAuctions(Request $request)
    {

        $data = Auction::select([
                'id', 'name', 'start_date', 'end_date',
                'auction_poster', 'accepts_silent_lots', 'accepts_offline_lots'
            ]);

        $data = $data->where('status', 1);

        if ($request->has('year')) {
            $data = $data->whereYear('start_date', '=', $request->year);
        }
        if ($request->has('month')) {
            $data = $data->whereMonth('start_date', '=', $request->month);
        }

        $data = $data->orderBy('start_date', 'desc')->paginate(15);

        $user_id = $request->has('user_id') ? $request->user_id : -1;
        foreach ($data->getCollection() as $item) {

            $item->in_favourite = \App\Models\Favourite::where('user_id', $user_id)->where('target_type', 1)
                ->where('target_id', $item->id)->count() > 0;

            $item->auction_poster = $item->buildPoster();
            $item->count_horses = AuctionHorseReg::where('auction_id', $item->id)->count();
            $item->start_time_formatted = Carbon::parse($item->start_date)->format('Y/m/d h:i a');

            $item->status = "unknown";
            $item->time_remaining = 0;
            $item->type = "online";
            $count_live = AuctionHorseReg::where('auction_id', $item->id)->where('status_string', 'started')->count();
            $count_waiting = AuctionHorseReg::where('auction_id', $item->id)->where('status_string', 'created')->count();
            if ($count_live > 0) {
                $item->status = "live";
            } else {
                if ($count_waiting > 0) {
                    $item->status = "upcoming";
                } else {
                    $item->status = "completed";
                }
            }






            if ($item->end_date != "") {

                $item->end_time_formatted = Carbon::parse($item->end_date)->format('Y/m/d h:i a');
            } else {
                $item->type = "mixed";
            }
        }

        return $this->formResponse("Auctions retrieved", $data, 200);
    }

    public function getMyBids(Request $request)
    {

        $user_id = Auth::id();
        $data = \App\Models\Bid::where('user_id', $user_id)->select(['lot_id', DB::Raw("max(curr_amount) as amount")])
            ->groupBy('lot_id')->orderBy('lot_id', 'desc')->paginate(30);

        $list = [];

        foreach ($data->getcollection() as $item) {
            $lot = AuctionHorseReg::find($item->lot_id);
            $item->lot_no = "Lot #" . ($lot->order_sn + 1);
            $item->time_passed_formated = TimeUtils::humanTiming($item->created_at);
            $item->auction_name = Auction::find($lot->auction_id)->name;
            $item->horse_name = Horse::find($lot->horse_id)->name_en;
            $item->lot_status = $lot->status_string;
            $item->bidding_status = "Lost";
            if ($item->lot_status == 'started' || $item->lot_status == 'stopped') {
                $mx = \App\Models\Bid::where('lot_id', $lot->id)->where('status', 1)->max('curr_amount');

                if ($mx == $item->amount)
                    $item->bidding_status = "Winning";
                else
                    $item->bidding_status = "Losing";
            }
            if ($item->lot_status == 'sold') {
                if ($lot->winner_id == $user_id)
                    $item->bidding_status = "Won";
                else
                    $item->bidding_status = "Lost";
            }

            $item->amount = number_format($item->amount, 0) . " " . Auction::find($lot->auction_id)->currency;
        }

        return $this->formResponse("My Bids lots details retrieved", $data, 200);
    }

    function buildHorseObject($horse_id)
    {

        $horse = Horse::find($horse_id);
        $horse->name = $horse->name_en;

        $sire = $horse->getSire();
        $dam = $horse->getDam();


        $horse->dam = $dam ? $dam->name_en :  "Unknown";
        $horse->sire = $sire ? $sire->name_en :  "Unknown";


        $sire_of_sire_name = $sire ? $sire->getSire() : null;
        $sire_of_dam_name = $dam ? $dam->getDam() : null;

        $dam_of_sire_name = $sire ? $sire->getDam() : null;
        $dam_of_dam_name = $dam ? $dam->getDam() : null;



        $pedigree = '{
    "edges": [
        {
            "to": 2,
            "from": 1
        },
        {
            "to": 3,
            "from": 1
        },
        {
            "to": 4,
            "from": 2
        },
        {
            "to": 5,
            "from": 2
        },
        {
            "to": 6,
            "from": 3
        },
        {
            "to": 7,
            "from": 3
        }
    ],
    "nodes": [
        {
            "id": 1,
            "label": "' . $horse->name . '"
        },
        {
            "id": 2,
            "label": "' . $horse->sire . '"
        },
        {
            "id": 3,
            "label": "' . $horse->dam . '"
        },
        {
            "id": 4,
            "label": "' . ($sire_of_sire_name ? $sire_of_sire_name->name_en : "Unknown") . '"
        },
        {
            "id": 5,
            "label": "' . ($dam_of_sire_name ? $dam_of_sire_name->name_en : "Unknown") . '"
        },
        {
            "id": 6,
            "label": "' . ($sire_of_dam_name ? $sire_of_dam_name->name_en : "Unknown") . '"
        },
        {
            "id": 7,
            "label": "' . ($dam_of_dam_name ? $dam_of_dam_name->name_en : "Unknown") . '"
        }
    ]
}';



        $horse->family_tree =  $pedigree;






        $horse->performance_tree = \App\Models\PerformanceTree::where('horse_id', $horse_id)->get();
        $horse->dob_formatted = Carbon::parse($horse->dob)->format('Y/m/d');


        $horse->veterinary = $horse->veterinary != "" ?  url('storage/' . $horse->veterinary) : "";




        $gallery = [];
        foreach (\App\Models\HorseMultimedia::where('horse_id', $horse_id)->get() as $m)
            $gallery[] = url('storage/horses-gallery/' . $m->media_link);
        $horse->gallery = $gallery;
        return $horse;
    }

    public function getHorseDetailsById(Request $request, $id)
    {
        $horse = $this->buildHorseObject($id);

        return $this->formResponse("Horse details retrieved", $horse, 200);
    }

    public function getAuctionByIdInternal(Request $request, $id)
    {
        $data = Auction::find($id);

        // name , gender , age  , status  
        if ($data) {
            $data->notification_bar = $data->buildNotificationBarObject();
            $data->auction_poster = $data->buildPoster();

            $data->start_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $data->start_date)->format('Y/m/d H:i');
            $data->lots = AuctionHorseReg::join('horses', 'horses.id', 'auction_horse_regs.horse_id')
                ->select(['auction_horse_regs.*', 'horses.name_en', 'horses.name_ar', 'horses.gender'])->where('auction_id', $data->id);

            if ($request->has('horse_name')) {
                $data->lots = $data->lots->where(function ($q) use ($request) {
                    $q->where('name_en', 'like', '%' . $request->horse_name . '%')
                        ->orWhere('name_ar', 'like', '%' . $request->horse_name . '%');
                });
            }
            if ($request->has('min_age')) {
                $data->lots = $data->lots->whereRaw("TIMESTAMPDIFF(YEAR, horses.dob, CURDATE()) >= " . $request->min_age);
            }

            if ($request->has('max_age')) {
                $data->lots = $data->lots->whereRaw("TIMESTAMPDIFF(YEAR, horses.dob, CURDATE()) <= " . $request->max_age);
            }

            if ($request->has('gender')) {
                if ($request->gender == 'stallion' || $request->gender == 'mare') {
                    $data->lots = $data->lots->where('horses.gender', $request->gender);
                }
                if ($request->gender == 'embryo') {
                    $data->lots = $data->lots->where('auction_horse_regs.target_type', 'breeding-right')
                        ->where('horses.gender', 'mare');
                }
                if ($request->gender == 'breeding') {
                    $data->lots = $data->lots->where('auction_horse_regs.target_type', 'breeding-right')
                        ->where('horses.gender', 'stallion');
                }
            }


            if ($request->has('status')) {
                $data->lots = $data->lots->where('status_string', $request->status);
            }

            $data->lots = $data->lots
                ->orderBy('order_sn')->get();

            $data->count_horses = count($data->lots);
            $user_id = $request->has('user_id') ? $request->user_id : -1;

            $data->in_favourite = $user_id != -1 ? \App\Models\Favourite::where('user_id', $user_id)->where('target_type', 1)
                ->where('target_id', $data->id)->count() > 0 : false;

            $data->total_duration = (int) ((count($data->lots) * $data->lot_duration) / 60) . " hours";






            $currency = $this->getAuctionCurrency($data->id);
            $count_live = 0;
            $count_finished = 0;
            $count_upcoming = 0;
            foreach ($data->lots as $item) {

                if ($item->status_string == 'created') $count_upcoming++;
                else  if ($item->status_string == 'started' || $item->status_string == 'stopped') $count_live++;
                else $count_finished++;

                $item->auction_name = $data->name;
                $item->in_favourite = \App\Models\Favourite::where('user_id', $user_id)->where('target_type', 2)
                    ->where('target_id', $item->id)->count() > 0;
                $item->lot_number = "#" . ($item->order_sn + 1);
                $item->lot_poster =  $data->auction_poster;
                $item->horse = $this->buildHorseObject($item->horse_id);

                $item->lot_poster =  count($item->horse->gallery) > 0  ? $item->horse->gallery[0] :  $data->auction_poster;
                $item->horse->name =   $item->name_en = $item->horse->name_en = ($item->target_type == 'breeding-right' ? ($item->horse->gender == 'mare' ? "Embryo of : " : "Breeding right from : ") : "")
                    . $item->horse->name_en;

                $item->type = $item->target_type == 'horse' ? "Horse purchase" : "Breeding rights";
                $item->status = "completed";
                $item->time_remaining = 0;
                $item->status_extra_info = $item->status_string;
                $item->server_time = Carbon::now()->format('Y/m/d h:i a');
                if ($item->lot_type == 'online') {
                    $item->lot_duration_seconds = $item->lot_duration * 60;
                    $item->start_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)->format('Y/m/d h:i a');
                    if ($item->status_string == 'started') {
                        $item->status = "live";
                        $item->status_extra_info = "";
                        $item->time_remaining = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->diffInSeconds(now());
                    }

                    if ($item->status_string == 'created') {
                        $item->status = "upcoming";
                        $item->time_remaining = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)
                            ->diffInSeconds(now());
                        $item->status_extra_info = "";
                    }

                    if ($item->status_string == 'sold') {
                        $item->status = "completed";
                        $item->time_remaining = 0;

                        $winner = \App\Models\User::find($item->winner_id);
                        $item->status_extra_info = "Sold for " . $item->current_bid .
                            " " . $currency . " for " . ($winner ? $winner->name : "Unknown");
                    }

                    $item->end_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->format('Y/m/d h:i a');
                } else {

                    if ($item->lot_type == 'silent') {
                        $item->start_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->format('Y/m/d h:i a');
                        if ($item->status_string == 'started') {
                            $item->status = 'live';
                            $item->status_extra_info = "Lot is active now";
                        } else if ($item->status_string == 'created') {
                            $item->status = 'upcoming';
                        } else {
                            $item->status = 'completed';
                        }
                        $item->is_current = false;
                    } else {
                        $item->is_current = $data->current_offline_auction == $item->id;
                        if ($item->is_current) {
                            $item->status = 'live';
                            $item->status_extra_info = "Lot is active now";
                        } else {
                            if ($item->status_string == 'created') {
                                $item->status = 'upcoming';
                                $item->status_extra_info = "It will start soon";
                            } else if ($item->status_string == 'sold') {
                                $item->status = 'completed';
                                $winner = \App\Models\User::find($item->winner_id);
                                $item->status_extra_info = "Sold for " . $item->current_bid .
                                    " " . $currency . " for " . ($winner ? $winner->name : "Unknown");
                            } else if ($item->status_string == 'unsold-no-bids') {
                                $item->status = 'completed';
                                $item->status_extra_info = "No one visited this lot";
                            }
                        }
                    }
                }
                $item->count_bids = \App\Models\Bid::where('lot_id', $item->id)->count();
                $max = \App\Models\Bid::where('lot_id', $item->id)->where('status', 1)->max('curr_amount');
                $item->top_bid = $max ? $max : 0;
                $item->top_bid_formatted = $item->top_bid . " " . $currency;
            }



            $data->status = "completed";
            if ($data->accepts_silent_lots == -1 && $data->accepts_offline_lots == -1) {
                $data->type = "online";
            } else {
                $data->type = "mixed";
            }
            if ($count_finished > 0)       $data->status = "completed";
            if ($count_upcoming > 0)       $data->status = "upcoming";
            if ($count_live > 0)       $data->status = "live";

            $data->currency_exchange_rate = 1.0;
            $data->stream_url = $data->stream_url == null || $data->stream_url == "" ? null : $data->stream_url;

            if ($data->currency == 'AED') $data->currency_exchange_rate = 1.0;
            if ($data->currency == 'USD' || $data->currency == '$') $data->currency_exchange_rate = 3.65;
            if ($data->currency == 'EUR') $data->currency_exchange_rate = 3.97;

            return $data;
        }
        return null;
    }

    public function getAuctionById(Request $request, $id)
    {


        return $this->formResponse("Auction details retrieved", $this->getAuctionByIdInternal($request, $id), 200);
    }

    public function getLotById(Request $request, $id)
    {

        return $this->formResponse("Auction details retrieved", $this->getLotByIdInternal($request, $id), 200);
    }

    public function getLotByIdInternal(Request $request, $id)
    {

        $item = AuctionHorseReg::find($id);
        if ($item) {
            $user_id = $request->has('user_id') ? $request->user_id : -1;

            $item->in_favourite = \App\Models\Favourite::where('user_id', $user_id)->where('target_type', 2)
                ->where('target_id', $item->id)->count() > 0;

            $item->lot_number = "#" . ($item->order_sn + 1);

            $data = Auction::find($item->auction_id);

            $data->auction_poster = $data->buildPoster();
            $data->notification_bar = $data->buildNotificationBarObject();
            $currency = $this->getAuctionCurrency($data->id);
            $item->auction_name = $data->name;
            $item->terms = $data->terms;


            $item->currency = $currency;
            $item->lot_poster = $data->auction_poster;
            $item->notification_bar = $data->notification_bar;
            $item->stream_url = $data->stream_url == null || $data->stream_url == "" ? null : $data->stream_url;
            $item->horse = $this->buildHorseObject($item->horse_id);

            $item->horse->name  =   $item->name_en = $item->horse->name_en = ($item->target_type == 'breeding-right' ?
                ($item->horse->gender == 'mare' ? "Embryo of : " : "Breeding right from : ") : "")
                . $item->horse->name_en;

            $item->type = $item->target_type == 1 ? "Horse purchase" : "Breeding rights";
            $item->status = "completed";
            $item->time_remaining = 0;
            $item->status_extra_info = "";

            if ($item->lot_type == 'online') {
                $item->lot_duration_seconds = $item->lot_duration * 60;

                $item->start_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_start_date)->format('Y/m/d h:i a');

                $item->status_extra_info = "No updates.";
                $item->server_time = Carbon::now()->format('Y/m/d h:i a');

                if ($item->status_string == 'started') {
                    $item->status = "live";
                    $item->status_extra_info = "";
                    $item->time_remaining = Carbon::parse($item->lot_end_date)->diffInSeconds(now());
                }

                if ($item->status_string == 'created') {
                    $item->status = "upcoming";
                    $item->time_remaining = now()->diffInSeconds(Carbon::parse($item->lot_start_date));
                    $item->status_extra_info = "";
                }

                if ($item->status_string == 'sold') {
                    $item->status = "completed";
                    $item->time_remaining = 0;

                    $winner = \App\Models\User::find($item->winner_id);
                    $item->status_extra_info = "Sold for " . $item->current_bid .
                        " " . $currency . " for " . ($winner ? $winner->name : "Unknown");
                }

                $item->end_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->lot_end_date)->format('Y/m/d h:i a');
            } else {
                if ($item->lot_type == 'silent') {
                    $item->start_time_formatted = Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->format('Y/m/d h:i a');

                    if ($item->status_string == 'started') {
                        $item->status = 'live';
                        $item->status_extra_info = "Lot is active now";
                    } else  if ($item->status_string == 'created') {
                        $item->status = 'upcoming';
                    } else {
                        $item->status = 'completed';
                    }


                    $item->is_current = false;
                } else {

                    $item->is_current = $data->current_offline_auction == $item->id;

                    if ($item->status_string == 'started') {
                        $item->status = 'live';
                        $item->status_extra_info = "Lot is active now";
                    } else if ($item->status_string == 'created') {
                        $item->status = 'upcoming';
                    } else {
                        $item->status = 'completed';
                    }
                }
            }
            $bids = \App\Models\Bid::where('lot_id', $item->id)->orderBy('id', 'desc')->get();

            $updates = \App\Models\LotUpdate::where('lot_id', $item->id)->orderBy('id', 'desc')->take(20)->get();
            foreach ($updates as $d) {
                $d->time_formatted = TimeUtils::humanTiming($d->created_at) . " ago.";
            }
            $item->updates = $updates;

            $item->count_bids = count($bids);
            $max = \App\Models\Bid::where('lot_id', $item->id)
                ->where('status', 1)->max('curr_amount');
            $item->top_bid = $max ? $max : 0;
            $item->top_bid_formatted = $item->top_bid . " " . $currency;
            $list = [];
            foreach ($bids as $b) {
                $list[] = $b->buildObject();
            }
            $item->bids_list = $list;
            $item->buttons = $data->bidding_buttons;

            if ($item->status == "live" && $user_id != -1) {
                $name = \App\Models\User::find($user_id)->name;
                $item->RegisterUpdate($name . " joined lot #" . $item->order_sn, "normal");
            }
            //    $item->visits = $item->visits+1;
            //  $item->save();
            return $item;
        }
        return null;
    }

    public function submitBid(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'lot_id' => 'required',
            'inc_amount' => 'required',
            'target_amount' => 'required',
        ]);

        if ($validator->fails()) {

            return $this->formResponse($this->failedValidation($validator), null, 200);
        }
        $user = Auth::user();
        if ($user->is_blocked == 1) {
            return $this->formResponse("You have been blocked !", null, 400);
        }

        if ($user->is_phone_verified == -1) {
            return $this->formResponse("Your phone number is not verified , please verify it and try again later ..", null, 402);
        }
        $lot = AuctionHorseReg::find($request->lot_id);
        if ($lot->lot_type == 'online' && Carbon::createFromFormat('Y-m-d H:i:s', $lot->lot_end_date)->isPast()) {
            return $this->formResponse("This auction is over ! ", null, 400);
        }

        if ($lot->status_string != 'started') {
            return $this->formResponse("The bidding on this auction is not available", null, 400);
        }

        $auction = Auction::find($lot->auction_id);
        if ($auction->required_deposit > 0) {
            if ($user->wallet_amount < $auction->required_deposit) {
                return $this->formResponse("You don't have enough credit in your wallet to access this lot , minimum deposit required is " . $auction->required_deposit . " "
                    . $auction->currency, null, 402);
            }
        }


        $max_bid = \App\Models\Bid::where('lot_id', $request->lot_id)
            ->where('status', 1)
            ->orderBy('curr_amount', 'desc')->first();

        if ($max_bid) {


            $lot_max = $max_bid->curr_amount;

            if ($lot->can_outbid == '-1' && $max_bid->user_id == $user->id) {

                return $this->formResponse("You  can not outbid yourself !", null, 400);
            }




            if ($request->target_amount <= $lot_max) {

                return $this->formResponse("Your bid is less than or equal the highest price", null, 400);
            }


            if ($request->target_amount <= $lot_max) {

                return $this->formResponse("Your bid is less than or equal the highest price", null, 400);
            }
            if ($request->target_amount != $lot_max + $request->inc_amount) {

                return $this->formResponse("Auction highest price has been changed to " . ($lot_max) . "!", null, 400);
            }
        }


        $currency = $this->getAuctionCurrency($lot->auction_id);

        $b = new \App\Models\Bid();
        $b->user_id = Auth::user()->id;
        $b->ms_timestamp = floor(microtime(true) * 1000);
        $b->inc_amount = $request->inc_amount;
        $b->curr_amount = \App\Models\Bid::where('lot_id', $request->lot_id)->where('status', 1)->max('curr_amount') + $request->inc_amount;
        $b->lot_id = $request->lot_id;
        $b->auction_id = $lot->auction_id;
        $b->save();
        event(new \App\Events\BidPlaced($b->lot_id, $currency));
        $lot->RegisterUpdate($user->name . " has placed " . $b->curr_amount . " " . $currency, "normal");
        return $this->formResponse("Bid placed succesfully", null, 200);
    }

    private function getAuctionCurrency($auction_id)
    {
        return Auction::find($auction_id)->currency;
    }

    public function getActiveLot(Request $request, Auction $auction)
    {
        $lot = AuctionHorseReg::select(['id', 'order_sn', 'lot_type', 'status_string', 'lot_end_date', 'horse_id', 'target_type', 'is_pregnant', 'pregnant_from', 'pregnant_due_date'])->with('horse')->where('auction_id', $auction->id)->where('status_string', 'started')->first();
        if(!$lot){
            return $this->formResponse("No active lot in this auction", null, 203);
        }
        $max = \App\Models\Bid::where('lot_id', $lot->id)->where('status', 1)->max('curr_amount');

        return $this->formResponse("Active lot details fetched successfully", [
            'lot' => $lot,
            'sire' => $lot->horse->getSire()->name_en,
            'dam' => $lot->horse->getDam()->name_en,
            'max_bid' => ($max ? $max  : 0) . " " . $auction->currency,
        ], 200);
    }
}
