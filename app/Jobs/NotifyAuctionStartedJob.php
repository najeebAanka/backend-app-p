<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class NotifyAuctionStartedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public $auction_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($auction_id)
    {
        $this->auction_id = $auction_id;
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
        $au = \App\Models\Auction::find($this->auction_id);
      if(!Carbon::parse( $au->start_date)->isPast()){   
     
     User::sendNotificationToAll("general", "-1",  $au->name." has started ! check it now in the app !", $this->auction_id);
  
      }
    }
}
