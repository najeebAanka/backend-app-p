<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PhoneVerification
 * 
 * @property int $id
 * @property string $phone
 * @property string $code
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property int $status
 *
 * @package App\Models
 */
class PhoneVerification extends Model
{
	protected $table = 'phone_verifications';

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'phone',
		'code',
		'status'
	];
}
