<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Bid;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use App\Models\User;
use App\Models\Horse;

class LotAboutToFinishtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public $lot_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lot_id)
    {
        $this->lot_id = $lot_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    
    public function getJobId(){
            return $this->job->getJobId();
    }
    
    public function handle()
    {
           $lot = AuctionHorseReg::find($this->lot_id);
              $lot->status_string = "about-to-start";
            $lot->save();
         $currency = Auction::find($lot->auction_id)->currency;
      
                // auction lot finished    
            $min_reservation = $lot->min_reservation;
            $max_bidder = Bid::where('lot_id', $lot->id)->orderBy('curr_amount', 'DESC')->first();
             
                  
                    $lot->RegisterUpdate("Auction lot is about to finish ! hurry up", "admin");

                    foreach (User::whereIn('id',
                            Bid::where('lot_id', $lot->id)->select(['user_id'])
                                    ->groupBy(['user_id'])->pluck('user_id')->toArray()
                    )->get() as $u) {
                        $u->sendNotification("lot-is-about-to-finish", $lot->id, "Auction on lot Number " . $lot->id . " is about to finish , hurry up !", null);
                    }
       
    }
}
