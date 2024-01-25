<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Horse
 * 
 * @property int $id
 * @property string|null $reg_no_en
 * @property string|null $reg_no_ar
 * @property string|null $name_en
 * @property string|null $name_ar
 * @property string|null $gender
 * @property string|null $color_en
 * @property string|null $color_ar
 * @property Carbon|null $dob
 * @property string|null $sire_name_en
 * @property string|null $sire_name_ar
 * @property string|null $dam_en
 * @property string|null $dam_ar
 * @property string|null $stud_name_en
 * @property string|null $stud_name_ar
 * @property string|null $owner_name_en
 * @property string|null $owner_name_ar
 * @property string|null $breeder_name_en
 * @property string|null $breeder_name_ar
 * @property string|null $owner_country_name_en
 * @property string|null $owner_country_name_ar
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $color
 * @property string|null $passport_doc
 * @property string|null $veterinary
 * @property string|null $bloodline
 * @property int|null $seller_id
 * @property int|null $order_sn
 * @property int|null $status
 *
 * @package App\Models
 */
class HorseToHorseRelation extends Model
{
	protected $table = 'horse_pedigree';

   
        
 
}
