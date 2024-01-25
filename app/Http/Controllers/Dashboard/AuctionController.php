<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horse;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\ImageUtils;
use App\Jobs\NotifyAuctionStartedJob;
use App\Models\Bid;
use App\Jobs\UpdateLotStatusJob;
use App\Models\User;
use App\Http\Controllers\JobHandlerController;
use Barryvdh\DomPDF\Facade\Pdf;

class AuctionController extends Controller
{
    public function generateLog(Request $request, $id)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AuctionExcelReport($id),
            Auction::find($id)->name . "-log-" . date('d-m-Y') . '.xlsx'
        );
    }

    public function addHallBidder(Request $request)
    {
        $h = new \App\Models\HallBidder();
        $h->auction_id = $request->auction_id;
        $h->name = $request->name;
        $h->email = $request->email;
        $h->phone = $request->phone;
        $h->country = $request->country;
        $h->paid_deposit = $request->deposit;
        $h->bidding_number = $request->number;
        $h->save();
        return back()->with('message', "Hall bidder is added to this auction");
    }


    public function generateCatalogPdf(Request $request, $id)
    {
        $controller = new \App\Http\Controllers\Api\AuctionController();
        $data = $controller->getAuctionByIdInternal($request, $id);
        $pdf = Pdf::loadView('pdf.auction-catalog', ["data" => $data]);
        return $pdf->download($data->name . '-catalog.pdf');
    }


    public function finishLot(Request $request)
    {
        $lot = AuctionHorseReg::find($request->lot_id);
        $lot->finishLot($request->status);
        $auction =  Auction::find($lot->auction_id);
        $auction->current_offline_auction = -1;
        $auction->save();
        event(new \App\Events\AuctionLotsUpdated($lot->auction_id, $auction->currency));
        return back()->with('message', "Lot finished  , you can start a new lot now !");
    }

    public function startSilentLot(Request $request)
    {
        $lot = AuctionHorseReg::find($request->lot_id);
        $lot->status_string = 'started';
        $lot->save();
        event(new \App\Events\LotEvents\LotStarted($lot->id, Auction::find($lot->auction_id)->currency));
        return back()->with('message', "Lot started and opened for slient bidding");
    }

    public function changeOutbidStatus(Request $request)
    {
        $lot = AuctionHorseReg::find($request->lot_id);
        $lot->can_outbid = $request->can_outbid;
        $lot->save();
        return $this->formResponse("Lot now " . ($lot->can_outbid == '-1' ? "Rejects" : "Accepts") . " outbidding.", null, 200);
    }

    public function editLot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $this->failedValidation($validator));
        }

        $lot = AuctionHorseReg::find($request->id);
        if ($request->has('min_reservation')) {
            $lot->min_reservation = $request->min_reservation;
        }
        if ($request->has('lot_type')) {
            if ($lot->lot_type != $request->lot_type) {
                if ($lot->lot_type != 'online' && $request->lot_type == 'online') {
                    return back()->with('error', "You cant change silent / offline lot to online lot !");
                }

                $lot->lot_type = $request->lot_type;
                $type = $request->lot_type;
                $auction = Auction::find($lot->auction_id);

                if ($type == 'offline') {
                    $auction->accepts_offline_lots = 1;
                    $auction->save();
                }
                if ($type == 'silent') {
                    $auction->accepts_offline_lots = 1;
                    $auction->save();
                }
            }
        }
        if ($request->has('target_type')) {
            $lot->target_type = $request->target_type;
        }
        $lot->save();

        return back()->with('message', "Lot is updated");
    }

    public function pauseSilentLot(Request $request)
    {
        $lot = AuctionHorseReg::find($request->lot_id);
        $lot->status_string = 'stopped';
        $lot->save();
        event(new \App\Events\LotEvents\LotFinished($lot->id, Auction::find($lot->auction_id)->currency));
        return back()->with('message', "Lot bidding paused");
    }

    public function publish(Request $request)
    {
        $auction = Auction::find($request->auctionId);
        $auction->status = 1;

        $duration = $auction->lot_duration * 60;
        $interval = $auction->auction_interval * 60;
        $stepper = 0;
        $interval_steps = 0;

        foreach (AuctionHorseReg::where('auction_id', $auction->id)->orderBy('order_sn')->get() as $c) {
            if ($c->lot_type == 'online') {
                if ($c->lot_start_date == "" || $c->lot_end_date == "") {
                    return back()->with('error', "Lot " . ($c->order_sn + 1) . " start and end time are not set ,Please calculate times before publishing");
                }
                $c->updateLotTimesAndCrons();
                $auction->end_date = $c->lot_end_date;
                if (Carbon::parse($c->lot_start_date)->isPast()) {
                    $c->status_string = 'started';
                }
                if (Carbon::parse($c->lot_end_date)->isPast()) {
                    $c->status_string = 'unsold-no-bids';
                }
                $c->save();
            } else if ($c->lot_type == 'silent') {
                if ($c->lot_start_date == "") {
                    return back()->with('error', "Lot " . ($c->order_sn + 1) . " start time is not set ! can not publish this auction !");
                }
            }
        }
        $auction->save();
        if (!config('app.debug')) {
            \App\Models\User::sendNotificationToAll("aucton-added", $auction->id, $auction->name . " is now available on test !", $auction->id);
        }
        return back()->with('message', "Published successfully");
    }

    public function cancelBid(Request $request)
    {
        $bid = \App\Models\Bid::find($request->id);
        $lot =     AuctionHorseReg::find($bid->lot_id);
        if ($lot->status_string  == "started") {
            $lot->RegisterUpdate("Bid of amount " . $bid->inc_amount .
                " from user " . \App\Models\User::find($bid->user_id)->name . " "
                . "is cancelled by admin !", "admin");
            $lot_id = $bid->lot_id;
            $bid->status = -1;
            $bid->save();

            event(new \App\Events\BidPlaced($lot->id, Auction::find($lot->auction_id)->currency));

            return $this->formResponse("Cancelled", null, 200);
        } else {
            return $this->formResponse("Lot is no longer live !", null, 200);
        }

        return $this->formResponse("Cancelled", null, 200);
    }

    public function extendLot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return  $this->formResponse($this->failedValidation($validator), null, 400);
        }

        $lot_id = $request->id;
        $lot =  AuctionHorseReg::find($lot_id);
        $f = $lot->lot_end_date;
        $lot->extend($request->amount);
        $auction = Auction::find($lot->auction_id);

        event(new \App\Events\AuctionLotsUpdated($lot->auction_id, Auction::find($lot->auction_id)->currency));

        Horse::find($lot->horse_id)->addToTimelineStory("Lot " . ($lot->order_sn + 1) . " time extended", "Time was extended by "
            . $request->amount . " mins in auction (" . $auction->name . ")");
        return $this->formResponse("Time extended", $lot, 200);
    }

    public function startOnlineLotManually(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'online_lot_id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $this->failedValidation($validator));
        }

        $lot =  AuctionHorseReg::find($request->online_lot_id);
        $lot->startLot();
        $auction = Auction::find($lot->auction_id);
        event(new \App\Events\AuctionLotsUpdated($auction->id, $auction->currency));
        return back()->with('message', "Lot started !");
    }


    public function edit(Request $request)
    {
        $auction = Auction::find($request->auctionId);

        if ($request->has('offline_lot_id')) {
            if ($auction->status == '1') {
                $clive =   AuctionHorseReg::where('auction_id', $request->auctionId)->where('lot_type', 'offline')->where('status_string', 'started')->get();
                foreach ($clive as $c) {
                    $c->status_string = 'stopped';
                    $c->save();
                    $c->RegisterUpdate("Auction lot is paused temporary , It can start later", "admin");
                    event(new \App\Events\LotEvents\LotFinished($c->id, $auction->currency));
                }

                $auction->current_offline_auction = $request->offline_lot_id;
                $auction->save();
                $lot =  AuctionHorseReg::find($request->offline_lot_id);
                $lot->startLot();
                event(new \App\Events\AuctionLotsUpdated($lot->auction_id, $auction->currency));
                if ($request->has('redrirect_self') && $request->redrirect_self == 'true') {
                    return \Illuminate\Support\Facades\Redirect::to('lot/' . $request->offline_lot_id)->with('message', "Lot " . ($lot->order_sn + 1) . " is started");
                } else{
                    return back()->with('message', "Lot " . ($lot->order_sn + 1) . " is started");
                }
            } else {
                return back()->with('error', "Can not start lot , auction is not published yet !");
            }
        }

        $should_republish_times = false;

        if ($request->has('bid_buttons_json')) {
            $auction->bidding_buttons = $request->bid_buttons_json;
        }
        if ($request->has('auction_interval')) {
            $auction->auction_interval = $request->auction_interval;
        }
        if ($request->has('lot_duration')) {
            $auction->lot_duration = $request->lot_duration;
        }

        if ($request->has('terms')) {
            $auction->terms = $request->terms;
        }

        if ($request->has('stream_url')) {
            $auction->stream_url = $request->stream_url;
        }

        if ($request->has('remind_start_before')) {
            $auction->remind_start_before = $request->remind_start_before;
        }

        if ($request->has('remind_end_before')) {
            $auction->remind_end_before = $request->remind_end_before;
        }

        if ($request->has('start_date')) {
            if ($auction->start_date != $request->start_date . " " . $request->start_time . ":00") {
                $auction->start_date = $request->start_date . " " . $request->start_time . ":00";
                $should_republish_times = true;
            }
        }

        if ($request->has('currency')) {
            $auction->currency = $request->currency;
        }

        if ($request->has('vat')) {
            $auction->vat = $request->vat;
        }

        if ($request->has('entry_fee')) {
            $auction->entry_fee = $request->entry_fee;
        }
        if ($request->has('deposit')) {
            $auction->required_deposit = $request->deposit;
        }

        if ($request->has('description')) {
            $auction->description = $request->description;
        }

        if ($request->has('title')) {
            $auction->name = $request->title;
        }

        if ($request->has('entry_start_date')) {
            $auction->entry_start_datetime = $request->entry_start_date . " " . $request->entry_start_time . ":00";
        }

        if ($request->has('entry_end_date')) {
            $auction->entry_end_datetime = $request->entry_end_date . " " . $request->entry_end_time . ":00";
        }

        if ($request->has('live_bids_main_bg')) {
            $auction->live_bids_main_bg = $request->live_bids_main_bg;
        }

        if ($request->has('live_bids_bg')) {
            $auction->live_bids_bg = $request->live_bids_bg;
        }

        if ($request->hasFile('poster')) {
            $path = ImageUtils::saveImage($request, "poster", "auction-posters", 1200, 1200);
            if ($path)
                $auction->auction_poster = $path;
            else {
                return back()->with('error', $auction->name . " 's poster can not be saved");
            }
        }

        if ($request->hasFile('tv_banner_bg')) {
            $path = ImageUtils::saveImage($request, "tv_banner_bg", "auction-posters", 1200, 1200);
            if ($path)
                $auction->tv_banner_bg = $path;
            else {
                return back()->with('error', $auction->name . " 's Tv banner background can not be saved");
            }
        }
        $auction->save();

        return back()->with('message', "Saved successfully. ");
    }

    public function createAuction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'currency' => 'required',
            'vat' => 'required',
            'terms' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $this->failedValidation($validator));
        }

        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        if (in_array($request->accepts_online, $acceptable)) {
            $validator = Validator::make($request->all(), [
                'interval' => 'required|numeric',
                'lot_duration' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->with('error', $this->failedValidation($validator));
            }
        }

        $auction = new Auction();
        $auction->name = $request->title;
        $auction->bidding_buttons = '[{"value" : 100 ,"show_after" : 0 ,"hide_after" : 500}'
            . ' ,{"value" : 200 ,"show_after" : 0 ,"hide_after" : 500}'
            . ' ,{"value" : 300 ,"show_after" : 0 ,"hide_after" : 500}'
            . ' ,{"value" : 400 ,"show_after" : 0 ,"hide_after" : 500}'
            . ' ,{"value" : 500 ,"show_after" : 0 ,"hide_after" : 500}'
            . ',{"value" : 1000 ,"show_after" : 500 ,"hide_after" : 900000}'
            . ',{"value" : 2000 ,"show_after" : 500 ,"hide_after" : 900000}'
            . ',{"value" : 3000 ,"show_after" : 500 ,"hide_after" : 900000}'
            . ',{"value" : 4000 ,"show_after" : 500 ,"hide_after" : 900000}'
            . ',{"value" : 5000 ,"show_after" : 500 ,"hide_after" : 900000}'
            . ']';
        $auction->description = $request->description;
        $auction->currency = $request->currency;
        $auction->vat = $request->vat;

        if (in_array($request->accepts_online, $acceptable)) {
            $auction->auction_interval = $request->interval;
            $auction->lot_duration = $request->lot_duration;
        } else {
            $auction->auction_interval = 0;
            $auction->lot_duration = 0;
        }

        $auction->entry_fee = $request->entry_fee;
        $auction->required_deposit = $request->deposit;
        $auction->start_date = $request->start_date . " " . $request->start_time . ":00";

        if ($request->has('entry_start_date')) {
            $auction->entry_start_datetime = $request->entry_start_date . " " . $request->entry_start_time . ":00";
        }

        if ($request->has('entry_end_date')) {
            $auction->entry_end_datetime = $request->entry_end_date . " " . $request->entry_end_time . ":00";
        }

        $auction->terms = $request->terms;
        $auction->live_bids_main_bg = $request->live_bids_main_bg;
        $auction->live_bids_bg = $request->live_bids_bg;
        $auction->status = -1;

        if ($request->hasFile('poster')) {
            $path = ImageUtils::saveImage($request, "poster", "auction-posters", 1200, 1200);
            if ($path)
                $auction->auction_poster = $path;
            else {
                return back()->with('error', $auction->name . " 's poster can not be saved");
            }
        }

        if ($request->hasFile('tv_banner_bg')) {
            $path = ImageUtils::saveImage($request, "tv_banner_bg", "auction-posters", 1200, 1200);
            if ($path)
                $auction->tv_banner_bg = $path;
            else {
                return back()->with('error', $auction->name . " 's Tv banner background can not be saved");
            }
        }

        if (in_array($request->accepts_online, $acceptable)) {
            $auction->accepts_online_lots = 1;
        } else {
            $auction->accepts_online_lots = -1;
        }
        if (in_array($request->accepts_offline, $acceptable)) {
            $auction->accepts_offline_lots = 1;
        } else {
            $auction->accepts_offline_lots = -1;
        }
        if (in_array($request->accepts_slient, $acceptable)) {
            $auction->accepts_silent_lots = 1;
        } else {
            $auction->accepts_silent_lots = -1;
        }
        if ($auction->accepts_online_lots == -1 && $auction->accepts_offline_lots == -1 && $auction->accepts_silent_lots == -1) {
            return back()->with('error', $auction->name . " Should accept at least one of ( silent ,offline ,or online) !");
        }
        $auction->save();

        if ($request->has('redirect_flag')) {
            return \Illuminate\Support\Facades\Redirect::to("auctions/" . $auction->id);
        } else {
            return back()->with('message', $auction->name . " is added succesfully. ");
        }
    }

    public function updateLotTimes(Request $r)
    {
        $auction_id = $r->auctionId;
        $auction = Auction::find($auction_id);
        $interval_steps = 0;
        $duration = $auction->lot_duration * 60;
        $interval = $auction->auction_interval * 60;
        $stepper = 0;

        foreach (AuctionHorseReg::where('auction_id', $auction_id)->where('lot_type', 'online')->orderBy('order_sn')->get() as $c) {
            $c->lot_start_date = Carbon::createFromFormat('Y-m-d H:i:s', $auction->start_date)
                ->addSeconds(($stepper++) * $interval)
                ->format('Y-m-d H:i:s');
            $lot_end = Carbon::createFromFormat('Y-m-d H:i:s', $c->lot_start_date)->addSeconds($duration);
            $c->lot_end_date = $lot_end->format('Y-m-d H:i:s');
            $c->lot_duration = $duration;
            $c->remind_start_before = $auction->remind_start_before;
            $c->remind_end_before = $auction->remind_end_before;
            $c->save();
        }

        foreach (AuctionHorseReg::where('auction_id', $auction_id)
            ->where('lot_type', 'silent')
            ->orderBy('order_sn')->get() as $c) {
            $c->lot_start_date = $auction->start_date;
            $c->save();
        }

        if ($stepper > 0)
            $auction->save();
        return $this->formResponse("Times calculated for " . $stepper . " horses", null, 200);
    }

    public function sendUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lot_id' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }
        AuctionHorseReg::find($request->lot_id)->RegisterUpdate($request->text, "admin");

        return $this->formResponse("Broadcasted", null, 200);
    }

    public function addLotsBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lots_type' => 'required',
            'target_type' => 'required',
            'auctionId' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with(["error" => "Select a lot type first"]);
        }

        if ($request->has('horses') && count($request->horses) > 0) {
            $items = $request->horses;
            $adder = Auth::id();
            foreach ($items as $i) {
                $reg = new AuctionHorseReg();
                $reg->horse_id = $i;
                $reg->auction_id = $request->auctionId;
                $reg->lot_type = $request->lots_type;
                $reg->target_type = $request->target_type;
                $reg->added_by = $adder;
                $order = -1;

                if (AuctionHorseReg::where('auction_id', $reg->auction_id)->count() > 0) {
                    $order = AuctionHorseReg::where('auction_id', $reg->auction_id)->max('order_sn');
                }
                $reg->order_sn = $order + 1;
                $reg->lot_duration = 1440;
                $reg->save();
            }

            return back()->with(["message" => "Added " . count($items) . " horses."]);
        } else {
            return back()->with(["message" => "No horses selected"]);
        }
    }
    public function acceptRegRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with(["error" => "Select a request first"]);
        }

        $i = \App\Models\HorseRegRequest::find($request->id);
        if (AuctionHorseReg::where('auction_id', $i->auction_id)->where('horse_id', $i->horse_id)->count()  == 0) {
            $reg = new AuctionHorseReg();
            $reg->horse_id = $i->horse_id;
            $reg->auction_id = $i->auction_id;
            $reg->lot_type = $i->lot_type;
            $reg->target_type = $i->selling_type;
            $reg->min_reservation = $i->min_reservation;
            $reg->is_pregnant = $i->is_pregnant;
            $reg->pregnant_from = $i->pregnant_from;
            $reg->pregnant_due_date = $i->pregnant_due_date;
            $reg->added_by = Auth::id();
            $order = -1;

            if (AuctionHorseReg::where('auction_id', $reg->auction_id)->count() > 0) {
                $order = AuctionHorseReg::where('auction_id', $reg->auction_id)->max('order_sn');
            }
            $reg->order_sn = $order + 1;
            $reg->lot_duration = 1440;
            $reg->save();

            $i->status = 'accepted';
            $i->save();

            return back()->with(["message" => "Added 1 horses."]);
        } else {
            return back()->with(["message" => "Already in this auction"]);
        }
    }

    public function sendRegRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lot_type' => 'required',
            'target_type' => 'required',
            'auctionId' => 'required',
            'horse_id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with(["error" => $this->failedValidation($validator)]);
        }

        if (\App\Models\HorseRegRequest::where('horse_id', $request->horse_id)->where('auction_id', $request->auctionId)->count() == 0) {
            $req = new \App\Models\HorseRegRequest();
            $req->horse_id = $request->horse_id;
            $req->lot_type = $request->lot_type;
            $req->selling_type = $request->target_type;
            $req->min_reservation = $request->min_reservation;
            $req->notes = $request->notes;
            $req->sent_by = Auth::id();
            $req->status = "pending";
            $req->auction_id = $request->auctionId;
            $acceptable = ['yes', 'on', '1', 1, true, 'true'];
            if (in_array($request->is_pregnant, $acceptable)) {
                $req->is_pregnant = 1;
                $req->pregnant_from = $request->pregnant_from;
                $req->pregnant_due_date = $request->pregnant_due_date;
            }

            $req->save();
        } else {
            return back()->with(["error" => "Request already sent !"]);
        }
        return back()->with(["message" => "Request is sent , pending adminstration approval"]);
    }

    public function submitBid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lot_id' => 'required',
            'inc_amount' => 'required',
            'bid_source' => 'required',
            'target_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 200);
        }

        $user = Auth::user();
        $lot = AuctionHorseReg::find($request->lot_id);
        if ($lot->lot_type == 'online' && Carbon::createFromFormat('Y-m-d H:i:s', $lot->lot_end_date)->isPast()) {
            return $this->formResponse("This auction is over ! ", null, 400);
        }

        if ($lot->status_string != 'started') {
            return $this->formResponse("The bidding on this auction is not available", null, 400);
        }

        $max_bid = \App\Models\Bid::where('lot_id', $request->lot_id)
            ->where('status', 1)
            ->orderBy('curr_amount', 'desc')
        ->first();

        if ($max_bid) {
            $lot_max = $max_bid->curr_amount;

            if ($max_bid->user_id == $user->id && $max_bid->bid_source == $request->bid_source) {
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

        $currency = Auction::find($lot->auction_id)->currency;

        $b = new \App\Models\Bid();
        $b->user_id = Auth::user()->id;
        $b->inc_amount = $request->inc_amount;
        $b->bid_source = $request->bid_source;
        $b->curr_amount = \App\Models\Bid::where('lot_id', $request->lot_id)->where('status', 1)->max('curr_amount') + $request->inc_amount;
        $b->lot_id = $request->lot_id;
        $b->auction_id = $lot->auction_id;
        $b->save();

        event(new \App\Events\BidPlaced($b->lot_id, $currency));

        $lot->RegisterUpdate("E-auctioneer throws bid for (" . $request->bid_source . ") for amount " . $b->curr_amount . " " . $currency, "normal");
        return $this->formResponse("Bid placed succesfully", null, 200);
    }

    public function reorderLots(Request $request)
    {
        $order = $request->orderArray;

        for ($i = 0; $i < count($order); $i++) {
            $s = AuctionHorseReg::find($order[$i]);
            $s->order_sn = $i;
            $s->save();
        }

        return $this->formResponse("Order Updated", $order, 200);
    }

    public function getTvBanner(Auction $auction)
    {
        return view('web.tv-banner', [
            'auction' => $auction
        ]);
    }

    public function getTvBannerAndResults(Auction $auction)
    {
        $active_lot = AuctionHorseReg::where('auction_id' ,$auction->id)->where('status_string' ,'started')->first();

        if(!$active_lot){
            return abort(404);
        } 

        return view('web.tv-banner-and-results', [
            'auction' => $auction,
            'lot' => $active_lot,
            'horse' => Horse::find($active_lot->horse_id)
        ]);
    }
}
