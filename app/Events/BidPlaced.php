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

  
class BidPlaced implements ShouldBroadcastNow
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
        return 'NewBidPlaced';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
        $data = new \stdClass();
        $bids = Bid::where('lot_id' ,$this->auction_id)->orderBy('id' ,'desc')->get();
        $data->count_bids = count($bids);
        $max = Bid::where('lot_id' ,$this->auction_id) ->where('status', 1)->max('curr_amount');
        $data->top_bid = $max ? $max : 0;
        $data->top_bid_formatted = $data->top_bid." ".$this->currency;
        $data->bids_list = [];
        foreach ($bids as $b){
           
            $data->bids_list[]=$b->buildObject(); 
            
        }
           event(new \App\Events\AuctionLotsUpdated($b->auction_id ,$this->currency));  
        return ["payload" => $data];
    }
}