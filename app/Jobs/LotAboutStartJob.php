<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Horse;


class LotAboutStartJob implements ShouldQueue
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
    public function handle()
    {
           $lot = \App\Models\AuctionHorseReg::find($this->lot_id);
        
        
            $lot->status_string = "about-to-start"; // action
            $lot->save();
            $lot->RegisterUpdate("Auction lot is about to start , be ready !", "admin");
            $text = "Auction lot #" . $lot->order_sn . " (" . Horse::find($lot->horse_id)->name_en . ") is about to start  , be ready to bid !";
            $users = \App\Models\User::whereIn( 'id' ,
                    $lot->getInteresetdUsers()
                    )
                    ->get();
            foreach ($users as $u)
           $u->sendNotification("lot-is-about-to-start",  $lot->id, $text, null);

        
    }
       public function getJobId(){
            return $this->job->getJobId();
    }
}
