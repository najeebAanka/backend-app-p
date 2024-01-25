<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HorseMultimedia
 * 
 * @property int $id
 * @property int|null $horse_id
 * @property string|null $media_thumb
 * @property string|null $media_link
 * @property int|null $media_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $order_sn
 *
 * @package App\Models
 */
class HorseMultimedia extends Model
{
	use SoftDeletes;
	protected $table = 'horse_multimedia';

	protected $casts = [
		'horse_id' => 'int',
		'media_type' => 'int',
		'order_sn' => 'int'
	];

	protected $fillable = [
		'horse_id',
		'media_thumb',
		'media_link',
		'media_type',
		'order_sn'
	];
}
