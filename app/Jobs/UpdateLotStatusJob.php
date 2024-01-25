<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class UpdateLotStatusJob implements ShouldQueue
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
        \App\Http\Controllers\Dashboard\AuctionController::checkLotStatus($this->lot_id);
   
    }
}
