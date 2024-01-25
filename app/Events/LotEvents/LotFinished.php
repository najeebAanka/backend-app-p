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

  
class LotFinished implements ShouldBroadcastNow
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
        return 'LotFinished';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
      $b = AuctionHorseReg::find($this->auction_id);
      event(new \App\Events\AuctionLotsUpdated($b->auction_id ,$this->currency));    
        
        return [];
    }
}