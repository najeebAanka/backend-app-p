<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Auction
 * 
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $name
 * @property int|null $created_by
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property int|null $auction_interval
 * @property string|null $stream_url
 * @property float|null $entry_fee
 * @property string|null $description
 * @property string|null $currency
 * @property string|null $bidding_buttons
 * @property Carbon|null $entry_start_datetime
 * @property Carbon|null $entry_end_datetime
 * @property float|null $required_deposit
 * @property int $active_lot
 * 
 * @property Collection|AuctionHorseReg[] $auction_horse_regs
 *
 * @package App\Models
 */
class Auction extends Model
{
	use SoftDeletes;
	protected $table = 'auctions';

	protected $casts = [
		'created_by' => 'int',
		'auction_interval' => 'int',
		'entry_fee' => 'float',
		'required_deposit' => 'float',
		'active_lot' => 'int'
	];


	public function auction_horse_regs()
	{
		return $this->hasMany(AuctionHorseReg::class);
	}

	public function buildPoster()
	{
		return $this->auction_poster != "" ? asset('storage/auction-posters/' . $this->auction_poster) : url('dist/assets/img/empty-auction.jpg');
	}

	public function gettvBannerBgAttribute()
	{
		return $this->attributes['tv_banner_bg'] != "" ? asset('storage/auction-posters/' . $this->attributes['tv_banner_bg']) : 'https://st.depositphotos.com/2079965/4337/i/450/depositphotos_43371959-stock-photo-running-black-horse-warmblooded-at.jpg';
	}


	public function buildNotificationBarObject()
	{
		$bar = new \stdClass();
		$bg = 'background-color: #795548;';


		$bar->title = "<div style='background-color: #607d8b;color: #fff;text-align: center;padding: 6px;'>Auction is idle , " . AuctionHorseReg::where('auction_id', $this->id)->count() . " lots are there.</div>";
		$bar->target_type = "default";
		$bar->target_id = -1;
		$bar->bg_color = "#ffffff";

		if ($this->accepts_offline_lots ==  1) {
			if ($this->current_offline_auction != -1) {
				$lot = AuctionHorseReg::find($this->current_offline_auction);
				$horse = Horse::find($lot->horse_id);
				$bar->title = "<div style='background-color : #4CAF50;color: #fff;
    text-align: center;

    padding: 6px;
    '>Current active lot <i>" . $horse->name_en . "</i> (Lot #" . ($lot->order_sn + 1) . "  ) , click to visit </div>";
				$bar->target_type = "lot";
				$bar->target_id = $this->current_offline_auction;
				$bar->bg_color = "#2596be";
			} else {
				$count_live = AuctionHorseReg::where('auction_id', $this->id)->where('status_string', 'started')
					->where('lot_type', 'silent')->count();
				if ($count_live == 0) {
					$bar->title = "<div style='" . $bg . ";color: #fff;
    text-align: center;

    padding: 6px;
    '>Welcome to " . $this->name . " , No auction lots are live now , stay tuned  , you will be informed once a lot got started !</div>";
					$bar->target_type = "lot";
					$bar->target_id = $this->current_offline_auction;
				} else {
					$bar->title = "<div style='background-color: orange;;color: #fff;
    text-align: center;

    padding: 6px;
    '>Welcome to " . $this->name . " ,We have <b>" . $count_live . "</b> live lots , check them now !</div>";
				}
			}
		}
		return $bar;
	}
}
