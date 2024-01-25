<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\JobHandlerController;
use App\Models\Horse;
use App\Models\User;
use App\Http\Controllers\Helpers\EmailUtils;
use stdClass;

/**
 * Class AuctionHorseReg
 *
 * @property int $id
 * @property int|null $auction_id
 * @property int|null $horse_id
 * @property int|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $added_by
 * @property float|null $min_reservation
 * @property string|null $notes
 * @property float|null $current_bid
 * @property int|null $winner_id
 * @property int|null $target_type
 *
 * @property Auction|null $auction
 *
 * @package App\Models
 */
class AuctionHorseReg extends Model {

    use SoftDeletes;

    protected $table = 'auction_horse_regs';
    protected $casts = [
        'auction_id' => 'int',
        'horse_id' => 'int',
        'added_by' => 'int',
        'min_reservation' => 'float',
        'current_bid' => 'float',
        'winner_id' => 'int',
    ];
    protected $fillable = [
        'auction_id',
        'horse_id',
        'status',
        'added_by',
        'min_reservation',
        'notes',
        'current_bid',
        'winner_id',
        'target_type'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function horse() {
        return $this->belongsTo(Horse::class);
    }

    public function getInteresetdUsers() {
        $arr1 = Favourite::where('target_type', 2)
                        ->where('target_id', $this->id)
                        ->select(['user_id'])->groupBy(['user_id'])->pluck('user_id')->toArray();

        $arr2 = Favourite::where('target_type', 1)
                        ->where('target_id', $this->auction_id)
                        ->select(['user_id'])->groupBy(['user_id'])->pluck('user_id')->toArray();

        $list = [];
        foreach ($arr1 as $a)
            $list[] = $a;
        foreach ($arr2 as $a)
            $list[] = $a;
        return $list;
    }

    public function startLot() {
        $this->status_string = "started";
        $this->lot_start_date = Carbon::now();
        $this->save();
        $horse = Horse::find($this->horse_id);
        $auction = Auction::find($this->auction_id);
        $this->RegisterUpdate("Auction lot started", "admin");
        $text = "Auction lot #" . ( $this->order_sn + 1 ) . " in auction (" . $auction->name . ") " . " (" . $horse->name_en . ") has just started , bid now in the app !";
        $users = User::whereIn(
                        'id',
                        $this->getInteresetdUsers())
                ->get();
        foreach ($users as $u)
            $u->sendNotification("lot-started", $this->id, $text, $this);



        event(new \App\Events\LotEvents\LotStarted($this->id, Auction::find($this->auction_id)->currency));
        $horse->addToTimelineStory("Lot " . ( $this->order_sn + 1 ) . " Started", "Lot started   in auction (" . $auction->name . " by timer");
    }

    public function RegisterUpdate($text, $type) {
        $p = new LotUpdate();
        $p->lot_id = $this->id;
        $p->contents = $text;
        $p->text_color = $type;
        $p->save();
        event(new \App\Events\LotUpdatePublished($this->id));
    }

    public function extend($mins) {

        $this->lot_end_date = Carbon::parse($this->lot_end_date)->addSeconds($mins * 60)->format('Y-m-d H:i:s');
        $this->save();

        $seconds_to_end = Carbon::parse($this->lot_end_date)->diffInSeconds(Carbon::now());
        if ($seconds_to_end > 0) {


            if ($this->finished_job_id != -1) {
                DB::table('jobs')->where('id', $this->finished_job_id)->delete();
            }

            $job = new \App\Jobs\LotFinishedJob($this->id);
            $job->delay(now()->addSeconds($seconds_to_end));
            $this->finished_job_id = JobHandlerController::dispatchJob($job);
            $this->save();
        }
        event(new \App\Events\LotEvents\LotTimeExtended($this->id, Auction::find($this->auction_id)->currency));
    }

    public function updateLotTimesAndCrons() {
        $seconds_to_start = Carbon::parse($this->lot_start_date)->diffInSeconds(Carbon::now());
        if ($seconds_to_start > 0) {

            if ($this->started_job_id != -1) {
                DB::table('jobs')->where('id', $this->started_job_id)->delete();
            }
            $job = new \App\Jobs\LotStartedJob($this->id);
            $job = $job->delay(now()->addSeconds($seconds_to_start));
            $this->started_job_id = JobHandlerController::dispatchJob($job);
        }
        $seconds_to_end = Carbon::parse($this->lot_end_date)->diffInSeconds(Carbon::now());
        if ($seconds_to_end > 0) {


            if ($this->finished_job_id != -1) {
                DB::table('jobs')->where('id', $this->finished_job_id)->delete();
            }

            $job = new \App\Jobs\LotFinishedJob($this->id);
            $job->delay(now()->addSeconds($seconds_to_end));
            $this->finished_job_id = JobHandlerController::dispatchJob($job);
        }


        // reminders

        if ($this->remind_start_before > 0)
            if ($seconds_to_start > 0) {


                if ($this->about_to_start_job_id != -1) {
                    DB::table('jobs')->where('id', $this->about_to_start_job_id)->delete();
                }

                $job = new \App\Jobs\LotAboutStartJob($this->id);
                $job->delay(now()->addSeconds($seconds_to_start - ($this->remind_start_before * 60)));
                $this->about_to_start_job_id = JobHandlerController::dispatchJob($job);
            }
        if ($this->remind_end_before > 0)
            if ($seconds_to_end > 0) {

                if ($this->about_to_finish_job_id != -1) {
                    DB::table('jobs')->where('id', $this->about_to_finish_job_id)->delete();
                }

                $job = new \App\Jobs\LotAboutToFinishtJob($this->id);
                $job->delay(now()->addSeconds($seconds_to_end - ($this->remind_end_before * 60)));
                $this->about_to_finish_job_id = JobHandlerController::dispatchJob($job);
            }



        $this->save();
    }

    private function sendEmailToWinner($u) {
        $email = new stdClass();

        $email->title = "Welcome to test auction !";
        $email->subject = "Congratulations on Winning the Auction Lot";
        $email->contents = "<p><strong>Dear " . $u->name . ",</strong></p>
        <p>I am delighted to inform you that you have won the auction lot <strong>Lot : " . ($this->order_sn + 1) . " (" .
                        Auction::find($this->auction_id)->name . ")</strong> on test.
                            Congratulations on your successful bid and thank you for choosing our platform for your auction needs.</p>
        <p>As the winner of the auction lot, you will soon receive an email
        from our team regarding the payment method and the process of delivering the item to you. We will ensure that the payment and delivery process is smooth and hassle-free for you.</p>
        <p>We understand that you may have questions regarding the payment and delivery
        process. Therefore, our team will provide you with a detailed explanation of everything you need to know to ensure that you receive your item as soon as possible.</p>
        <p>We appreciate your participation in the auction and hope that you had a
        positive experience. At test, we strive to offer our customers the best
        auction experience possible, and we are thrilled that you were able to find what you were looking for on our platform.</p>
        <p>If you have any questions or concerns, please do not hesitate to contact
        us. We are always here to help.</p>
        <p>Once again, congratulations on your successful bid, and we look forward to working with you soon.</p>
        <p>&nbsp;</p>
        <p><em>Best regards,</em></p>
        <p><em>test Website Team</em></p>";
        $email->has_button = false;
        if ($u->is_email_verified == '1') {
            EmailUtils::sendUniversalEmail($u, $email);
        }
    }

    public function finishLot($status = "") {
        $auction = Auction::find($this->auction_id);
        $currency = $auction->currency;

        if ($status != "") {
            $this->status_string = $status;
            $this->lot_end_date = Carbon::now();
            $this->save();
            $this->RegisterUpdate("Auction lot is closed by admin with status (" . $status . ")", "admin");
        } else {
            $min_reservation = $this->min_reservation;
            $max_bidder = Bid::where('lot_id', $this->id)->where('status', 1)->orderBy('curr_amount', 'DESC')->first();
            if ($max_bidder) {
                if ($max_bidder->curr_amount > $min_reservation) {
                    $this->winner_id = $max_bidder->user_id;
                    $this->status_string = "sold";
                    $this->current_bid = $max_bidder->curr_amount;

                    $this->lot_end_date = Carbon::now();
                    $this->save();
                    // notify people that this horse is sold for someone

                    $record = new LotWinningRecord();
                    $record->auction_id = $this->auction_id;
                    $record->lot_id = $this->id;

                    $record->winner_type = $max_bidder->bid_source == "" ? "user" : "hall-bidder";
                    $record->winner_id = $record->winner_type == "user" ? $this->winner_id : $max_bidder->bid_source;
                    $record->amount = $this->current_bid;
                    $record->bid_id = $max_bidder->id;
                    $record->horse_id = $this->horse_id;
                    $record->selling_type = $this->target_type;
                    $record->currency = $currency;
                    $record->save();

                    if ($max_bidder->bid_source == "") {
                        $winner = User::find($this->winner_id);
                        $this->RegisterUpdate("Auction lot is closed , sold for Mr. " . $winner->name . " for amount :  "
                                . $max_bidder->curr_amount . " " . $currency, "admin");

                        $message = "Auction lot " . ($this->order_sn + 1) . " has finished , horse was sold for Mr. " .
                                $winner->name . " for amount ("
                                . $max_bidder->curr_amount . " " . $currency . ".";
                    } else {

                        $this->RegisterUpdate("Auction lot is closed , sold for Hall bidder number. (" . $max_bidder->bid_source . ") for amount :  "
                                . $max_bidder->curr_amount . " " . $currency, "admin");

                        $message = "Auction lot " . ($this->order_sn + 1) . " has finished , horse was sold for Hall bidder number. (" . $max_bidder->bid_source . ") for amount ("
                                . $max_bidder->curr_amount . " " . $currency . ".";
                    }


                    Horse::find($this->horse_id)->addToTimelineStory("Lot " . ( $this->order_sn + 1 ) . " Finished", $message . " in auction (" . $auction->name . ")");

                    $people = User::whereIn('id',
                                    Bid::where('lot_id', $this->id)->select(['user_id'])
                                            ->groupBy(['user_id'])->pluck('user_id')->toArray()
                            )->get();
                    foreach ($people as $u) {
                        if ($u->id != $max_bidder->user_id) {


                            $u->sendNotification("lot-finished", $this->id, $message, $this);
                        } else {
                            if ($max_bidder->bid_source == "") {
                                $this->sendEmailToWinner($u);
                                $u->sendNotification("lot-finished", $this->id, "Congratulations ! you won the lot across (" . count($people) . ")  !", $this);
                            }
                        }
                    }
                } else {
                    //notify people that this horse was not sold due to low bids
                    $this->status_string = "unsold-low-max-bid";
                    $this->current_bid = $max_bidder->curr_amount;
                    $this->lot_end_date = Carbon::now();
                    $this->save();
                    $this->RegisterUpdate("Auction lot is closed , Not sold due to low maximum bid", "admin");
                    Horse::find($this->horse_id)->addToTimelineStory("Lot " . ( $this->order_sn + 1 ) . " Finished", "Lot was closed due to max bid less than minimum reservation"
                            . " in auction (" . $auction->name . ")");

                    foreach (User::whereIn('id',
                            Bid::where('lot_id', $this->id)->select(['user_id'])
                                    ->groupBy(['user_id'])->pluck('user_id')->toArray()
                    )->get() as $u) {
                        $u->sendNotification("lot-finished", $this->id, "Auction on lot Number " . $this->id . " has finished , horse was not sold due to low bids lower than minimum reservation amount !", $this);
                    }
                }
            } else {
                //no one tried to bid
                $this->status_string = "unsold-no-bids";
                $this->lot_end_date = Carbon::now();
                $this->save();
                $this->RegisterUpdate("Auction lot is closed , No bids placed for this lot !", "admin");
                Horse::find($this->horse_id)->addToTimelineStory("Lot " . ( $this->order_sn + 1 ) . " Finished", "Lot was closed  ,without any bids in auction (" . $auction->name . ")");
            }
        }
        $this->lot_end_date = Carbon::now();

        event(new \App\Events\LotEvents\LotFinished($this->id, $currency));
    }

}
