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

class LotFinishedJob implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $lot_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lot_id) {
        $this->lot_id = $lot_id;
    }

    
    public function getJobId() {
        return $this->job->getJobId();
    }

    public function handle() {
        AuctionHorseReg::find($this->lot_id)->finishLot();
    }

}
