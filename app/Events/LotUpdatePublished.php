<?php

namespace App\Events;
  
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Helpers\TimeUtils;

  
class LotUpdatePublished implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;
  
   private $auction_id;
   
    
  
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($auction_id) {
        $this->auction_id = $auction_id;
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
        return 'LotUpdatePublished';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
        $data = \App\Models\LotUpdate::where('lot_id' , $this->auction_id)->orderBy('id' ,'desc')->take(50)->get();
        foreach ($data as $d){
            $d->time_formatted = TimeUtils::humanTiming($d->created_at)." ago.";
        }
        return ["payload" => $data];
    }
}