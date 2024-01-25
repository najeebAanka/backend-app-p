<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AuctionRegisteration
 * 
 * @property int $id
 * @property int|null $auction_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class AuctionRegisteration extends Model
{
	use SoftDeletes;
	protected $table = 'auction_registerations';

	protected $casts = [
		'auction_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'auction_id',
		'user_id'
	];
}
