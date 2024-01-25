<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserBank
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $acc_name
 * @property string|null $acc_no
 * @property string|null $bank_name
 * @property string|null $iban
 * @property string|null $fav_branch
 * @property string|null $swiftcode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Favourite extends Model
{
		protected $table = 'favourites';
}
