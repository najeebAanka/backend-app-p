<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Payment
 * 
 * @property int $id
 * @property string|null $user_id
 * @property string|null $source
 * @property float|null $subtotal
 * @property float|null $vat
 * @property string|null $grandtotal
 * @property string|null $order_id
 * @property string|null $capture_id
 * @property int|null $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Payment extends Model
{
	use SoftDeletes;
	protected $table = 'payments';

	protected $casts = [
		'subtotal' => 'float',
		'vat' => 'float',
		'is_paid' => 'int'
	];

	protected $fillable = [
		'user_id',
		'source',
		'subtotal',
		'vat',
		'grandtotal',
		'order_id',
		'capture_id',
		'is_paid'
	];
}
