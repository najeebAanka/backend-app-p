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
use App\Models\AuctionHorseReg;
use App\Models\Horse;
use App\Models\User;
use Carbon\Carbon;


class AuctionLotsUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;


    private $auction_id;
    private $currency;



    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($auction_id, $currency)
    {
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
        return new Channel('parent-auction-' . $this->auction_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'AuctionLotsUpdated';
    }
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastWith()
    {
        $data = new \stdClass();
        $lots = AuctionHorseReg::select(['id', 'order_sn', 'lot_type', 'status_string', 'lot_end_date', 'horse_id', 'target_type', 'is_pregnant', 'pregnant_from', 'pregnant_due_date'])->where('auction_id', $this->auction_id)->orderBy('order_sn')->get();
        foreach ($lots as $b) {
            $b->count_bids = \App\Models\Bid::where('lot_id', $b->id)->count();
            $max = \App\Models\Bid::where('lot_id', $b->id)->where('status', 1)->max('curr_amount');
            $b->max_bid = ($max ? $max  : 0) . " " . $this->currency;
            $b->time_remaining = 0;
            if ($b->status_string == 'started')
                $b->time_remaining = Carbon::parse($b->lot_end_date)->diffInSeconds(Carbon::now());
            if ($b->status_string == 'created')
                $b->time_remaining = Carbon::now()->diffInSeconds(Carbon::parse($b->lot_started_date));


            $b->extra_info = "Status : " . $b->status_string;
            $horse = Horse::find($b->horse_id);
            $b->horse = $horse;
            $b->sire = $horse->getSire()->name_en;
            $b->dam = $horse->getDam()->name_en;
        }
        $wrapper = new \stdClass();
        $wrapper->notification_bar = Auction::find($this->auction_id)->buildNotificationBarObject();
        $wrapper->lots = $lots;
        return ["payload" => $wrapper];
    }
}
