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
class Horse extends Model
{
	protected $table = 'horses';

        
        public function addToTimelineStory($title  ,$description){
          
            $r = new HorseTimelineRecord();
            $r->horse_id = $this->id;
            $r->title = $title;
            $r->description = $description;
            $r->flag = 0;
            $r->save();
            
        }
        
        
        public function getSire(){
            $link=HorseToHorseRelation::where('horse_2_id' ,$this->id)->where('horse_relation' ,'sire')->first();
            return $link ? Horse::find($link->horse_1_id) : null;
             
        }
        public function getDam(){
          $link=HorseToHorseRelation::where('horse_2_id' ,$this->id)->where('horse_relation' ,'dam')->first();
            return $link ? Horse::find($link->horse_1_id) : null;
             
        }
        
        
          private     $array;    
          private function populatePedigreeArray($node){
          $this->array[]=$node;
          if(isset($node->dam)){
          $this-> populatePedigreeArray($node->dam);
          }
             if(isset($node->sire)){
          $this-> populatePedigreeArray($node->sire);
             }
            
        } 
         public function generatePedegreeNodes(){
            
             $pedigree = self::getPedigree($this);
            $this->array = [];
           $this->populatePedigreeArray( $pedigree);
           return $this->array;
        } 
        
        public function generatePedegreeHTML(){
            
             $pedigree = self::getPedigree($this);
             return $this->renderPedegree($pedigree);
        }
        
        private function renderPedegree($plan){
            $str = "<table class='pkj'>";
            $str.= "<tr>";
            
            if((isset($plan->sire) && isset($plan->dam))){
                
                 $str.=  '<td><td colspan="2"><p class="table-node node-'.$plan->gender.'" style="width : 100%;">'.$plan->name.'</p></td><td></td></tr>';    
            }else{
                 $str.=  '<td colspan="4"><p class="table-node node-'.$plan->gender.'">'.$plan->name.'</p></td></tr>';    
            }
            
          
             $str.='<tr>';
               
                 if(isset($plan->sire)){
                     $str.=  "<td style='width : 50%' ></td><td style='width : 50%' class='diag-left'></td>";
            }
              if(isset($plan->dam)){
                   $str.=  "<td  style='width : 50%' class='diag-right'></td><td style='width : 50%' ></td>";
            }
               
               $str.= "</tr><tr>";  
               
            if(isset($plan->sire)){
                   $str.=  "<td  style='width : 50%' colspan=\"2\">";
                $str.=   $this->renderPedegree($plan->sire) ;
                  $str.=  "</td>";
            }
              if(isset($plan->dam)){
                   $str.=  "<td  style='width : 50%' colspan=\"2\">";
                $str.=   $this->renderPedegree($plan->dam) ;
                  $str.=  "</td>";
            }
           
               $str.=  "</tr>";
               $str.=  "</table>";
               return $str;
        }
        
    
        
        
        
        public static function getPedigree($horse){
           $plan = new \stdClass();
           $plan->id = $horse->id;
           $plan->name = $horse->name_en;
           $plan->gender = $horse->gender;
          if(HorseToHorseRelation::where('horse_2_id' ,$horse->id)->where('horse_relation' ,'sire')->count()>0){
          $plan->sire = self::getPedigree(Horse::find(HorseToHorseRelation::where('horse_2_id' ,$horse->id)->where('horse_relation' ,'sire')->first()
                  ->horse_1_id));   
         }
           if(HorseToHorseRelation::where('horse_2_id' ,$horse->id)->where('horse_relation' ,'dam')->count()>0){
         $plan->dam = self::getPedigree(Horse::find(HorseToHorseRelation::where('horse_2_id' ,$horse->id)->where('horse_relation' ,'dam')->first()
                  ->horse_1_id));   
         }
         return $plan;
        }
        
        
        
        public function getImage(){
             $m = HorseMultimedia::where('horse_id' ,$this->id)->first();
            if($m) return url('storage/horses-gallery/'.$m->media_link);
             return url('dist/assets/img/empty-auction.jpg'); 
            
        }
        
        
 
}
