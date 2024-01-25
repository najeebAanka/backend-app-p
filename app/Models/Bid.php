<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Bid
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $lot_id
 * @property float|null $inc_amount
 * @property float|null $curr_amount
 * @property string|null $thisid_source
 * @property int|null $registered_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Bid extends Model
{
	use SoftDeletes;
	protected $table = 'bids';

	protected $casts = [
		'user_id' => 'int',
		'lot_id' => 'int',
		'inc_amount' => 'float',
		'curr_amount' => 'float',
		'registered_by' => 'int'
	];

	protected $fillable = [
		'user_id',
		'lot_id',
		'inc_amount',
		'curr_amount',
		'bid_source',
		'registered_by'
	];
        
        
        public function buildObject(){
            
            $s = new \stdClass();
            $user = User::find($this->user_id);
            $name = "Deleted account";
            if($this->bid_source ){
                $bidder = HallBidder::where('auction_id' ,$this->auction_id)
                        ->where('bidding_number' ,$this->bid_source)->first();
                
               $name =  $bidder ? $bidder->name :  "Hall Bidder (".$this->bid_source.")" ;
            }else{
              if($user){
                  $name=$user->name;
              }  
            }
            
            
            $s->name =   strlen($name) > 18 ? substr($name,0,18)."..." : $name ;
           $s->id = $this->id;
           $s->status = $this->status;
            $s->user_id = $this->user_id;
            $s->source = $this->bid_source  ? "E-Auctioneer" : "Online";
            $s->country_code = $user ?  $user->country_code : "AE";
            $s->country_flag = url('dist/assets/img/flags/'.strtolower($s->country_code).'.png');
            $s->amount = $this->curr_amount." ".$this->currency;
            $s->time = Carbon::createFromFormat('Y-m-d H:i:s' ,$this->created_at)->format('H:i:s');
            $s->date = Carbon::createFromFormat('Y-m-d H:i:s' ,$this->created_at)->format('d/m/Y');
            return $s;
            
        }
}
