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
 * @property string|null $bid_source
 * @property int|null $registered_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Notification extends Model
{

	protected $table = 'notifications';


             public function translate($lang = "en"){
           
       if($this->not_type == 'aucton-added') return "A new auction (".Auction::find($this->not_id)->name.") was addedd.";  
       if($this->not_type == 'auction-finished') return $this->gen_text;  
       if($this->not_type == 'lot-finished') return $this->gen_text;  
       if($this->not_type == 'lot-started') return $this->gen_text;  
       if($this->not_type == 'lot-is-about-to-start') return $this->gen_text;  
       if($this->not_type == 'lot-is-about-to-finish') return $this->gen_text;  
     
       if($this->not_type == 'general') return $this->gen_text;  
       
         return "Unknown notification type ! (".($this->not_type).")";  
           
       } 
        
}
