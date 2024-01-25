<?php

namespace App\Events\LotEvents;
  
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Horse;

  
class LotTimeExtended implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;
  
   
   private $auction_id;
   private $currency;
   
    
  
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($auction_id ,$currency) {
        $this->auction_id = $auction_id;
        $this->currency = $currency;
       
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
      return new Channel('auction-rooms-'.$this->auction_id);
    }
  
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'LotTimeExtended';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
      $lot = AuctionHorseReg::find($this->auction_id);
      
      $payload = new \stdClass();
      $payload->new_finish_time = $lot->lot_end_date;
      $seconds_to_end = Carbon::parse($lot->lot_end_date)->diffInSeconds(Carbon::now());
      $payload->time_remaining = $seconds_to_end;
      
       $lot->RegisterUpdate("Auction lot ".($lot->order_sn+1)." time is extended", "admin");
            $text = "Auction lot #" . $lot->order_sn . " (" . Horse::find($lot->horse_id)->name_en . ") time is extended !";
            $users = \App\Models\User::whereIn( 'id' ,
                    $lot->getInteresetdUsers()
                    )
                    ->get();
            foreach ($users as $u)
           $u->sendNotification("lot-time-extended",  $lot->id, $text, null);
    
        return  ["payload" => $payload];
    }
}