<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horse;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\HorseToHorseRelation;



class HorsesController extends Controller {
 
    
    public function deleteImges(Request $request){
      if($request->has('ids')){
          foreach ($request->ids as $id){
              \App\Models\HorseMultimedia::find($id)->delete();
          }
            return back()->with('message', "Gallery updated");
      }else{
           return back()->with('error', "No images selected");
      }
        
    }
    
    public function deletePerformance(Request $request){
      if($request->has('id')){
       
          \App\Models\PerformanceTree::find($request->id)->delete();
         
            return back()->with('message', "Performance Records updated");
      }
      return back()->with('message', "Item not found !");  
    }
    
    public function addPerformance(Request $request){
        
        
          $validator = Validator::make($request->all(), [
                    'horse_id' => 'required',
                    'relation' => 'required',
                    'rank' => 'required',
                    'comp' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        
        
   $p = new \App\Models\PerformanceTree();
   $name = $request->relation;
   if($request->relation == 'Self'){
    $name  = Horse::find($request->horse_id) ->name_en;  
   }
    if($request->relation == 'Dam'){
    $name  = Horse::find($request->horse_id)->getDam() ->name_en;  
   }
    if($request->relation == 'Sire'){
    $name  = Horse::find($request->horse_id)->getSire() ->name_en;  
   }
     if($request->relation == 'Dam of dam'){
    $name  = Horse::find($request->horse_id)->getDam()->getDam() ->name_en;  
   }
      if($request->relation == 'Dam of sire'){
    $name  = Horse::find($request->horse_id)->getSire()->getDam() ->name_en;  
   }
   
     if($request->relation == 'Sire of dam'){
    $name  = Horse::find($request->horse_id)->getDam()->getSire() ->name_en;  
   }
     if($request->relation == 'Sire of sire'){
    $name  = Horse::find($request->horse_id)->getSire()->getSire() ->name_en;  
   }
   $p->horse_name =  $name;
   $p->relation_name = $request->relation;
   $p->rank_name = $request->rank;
   $p->comp_name = $request->comp;
    $p->horse_id =    $request->horse_id; 
    $p->save();  
    return back()->with('message', "Record is added.");
        
    }
    
    public function updateHorsePedigree(Request $request) {
          $validator = Validator::make($request->all(), [
                    'horse_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }

 $horse = Horse::find($request->horse_id);
        $pedigree = '{
    "edges": [
        {
            "to": 2,
            "from": 1
        },
        {
            "to": 3,
            "from": 1
        },
        {
            "to": 4,
            "from": 2
        },
        {
            "to": 5,
            "from": 2
        },
        {
            "to": 6,
            "from": 3
        },
        {
            "to": 7,
            "from": 3
        }
    ],
    "nodes": [
        {
            "id": 1,
            "label": "'.$request->Ped_root.'"
        },
        {
            "id": 2,
            "label": "'.$request->Ped_sire.'"
        },
        {
            "id": 3,
            "label": "'.$request->Ped_dam.'"
        },
        {
            "id": 4,
            "label": "'.$request->Ped_sire_of_sire.'"
        },
        {
            "id": 5,
            "label": "'.$request->Ped_dam_of_sire.'"
        },
        {
            "id": 6,
            "label": "'.$request->Ped_sire_of_dam.'"
        },
        {
            "id": 7,
            "label": "'.$request->Ped_dam_of_dam.'"
        }
    ]
}';
         


      $horse->family_tree =  $pedigree;
        
        
        
        
        
        $horse->save();
        return back()->with('message', $horse->name_en . "'s Pedigree is updated succesfully.");
    }
    public function updateHorse(Request $request) {

        $validator = Validator::make($request->all(), [
                    'horse_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }




        $horse = Horse::find($request->horse_id);
        if ($request->has('name'))
            $horse->name_en = $request->name;
        if ($request->has('dob'))
            $horse->dob = $request->dob;
        if ($request->has('reg_no'))
            $horse->reg_no = $request->reg_no;
        if ($request->has('color'))
            $horse->color = $request->color;
       
        if ($request->has('breeder'))
            $horse->breeder_name = $request->breeder;
        if ($request->has('owner'))
            $horse->owner_name = $request->owner;
        if ($request->has('country'))
            $horse->origin = $request->country;
   
         if ($request->has('about'))
            $horse->about_horse = $request->about;
        
        if(Auth::user()->isAdmin()){
             if ($request->has('status'))
            $horse->status = $request->status; 
             if($request->status == 'shipped'){
             $horse->  addToTimelineStory("Horse is shipped" ,$request->notes) ; 
                 
             }
        }
        
     




        if ($request->hasfile('passport_doc')) {
            $counter = 0;
            $file = $request->file('passport_doc');

            $fileArray = array('passport_doc' => $file);
            $rules = array(
                'passport_doc' => 'mimes:jpg,png,jpeg,gif,svg,wav,aac,mp3,m4a,pdf,doc,docx,txt|required|max:500000' // max 10000kb
            );
            $validator = Validator::make($fileArray, $rules);
            if ($validator->fails()) {
                return response()->json("passport_doc File format is not supported", 400);
            } else {





                if (in_array($file->getClientOriginalExtension(), ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {


                    $name = \App\Http\Controllers\Helpers\ImageUtils::saveImage($request, "passport_doc", "passports", 1200, 1200);

                    $horse->passport_doc = "passports/" . $name;
                } else {


                    $fileName = md5(time()) . '.' . $file->getClientOriginalExtension();
                    $path = "passports/" . date('Y') . "/" . date('m') . "/" . date('d') . "";
                    $filePath = $file->storeAs($path, $fileName, 'public');
                    $horse->passport_doc = $filePath;
                }
            }
        }



        if ($request->hasfile('vet_doc')) {
            $counter = 0;
            $file = $request->file('vet_doc');

            $fileArray = array('vet_doc' => $file);
            $rules = array(
                'vet_doc' => 'mimes:jpg,png,jpeg,gif,svg,wav,aac,mp3,m4a,pdf,doc,docx,txt|required|max:500000' // max 10000kb
            );
            $validator = Validator::make($fileArray, $rules);
            if ($validator->fails()) {
                return response()->json("vet_doc File format is not supported", 400);
            } else {





                if (in_array($file->getClientOriginalExtension(), ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {


                    $name = \App\Http\Controllers\Helpers\ImageUtils::saveImage($request, "vet_doc", "veterinary", 1200, 1200);

                    $horse->veterinary = "passports/" . $name;
                } else {


                    $fileName = md5(time()) . '.' . $file->getClientOriginalExtension();
                    $path = "veterinary/" . date('Y') . "/" . date('m') . "/" . date('d') . "";
                    $filePath = $file->storeAs($path, $fileName, 'public');
                    $horse->veterinary = $filePath;
                }
            }
        }
        
        
        
        
                if ($request->hasFile('bulk-images')) {
                    $location = "horses-gallery";
             
            foreach ($request->file('bulk-images') as $file) {

                $uniqueFileName = uniqid()
                        . '.' . $file->getClientOriginalExtension();
                $name_lg = date('Y') . "/" . date("m") . "/" . date("d") . "/lg_" . $uniqueFileName;
                $name_sm = date('Y') . "/" . date("m") . "/" . date("d") . "/sm_" . $uniqueFileName;
                try {
                 
                    
                      $uniqueFileName = uniqid()
                        . '.' . $file->getClientOriginalExtension();
                $name = date('Y') . "/" . date("m") . "/" . date("d") . "/" . $uniqueFileName;
              
                    if (!Storage::disk('public')->has($location.'/' . date('Y') . "/" . date("m") . "/" . date("d") . "/")) {
                        Storage::disk('public')->makeDirectory($location.'/' . date('Y') . "/" . date("m") . "/" . date("d") . "/");
                    }

                    Image::make($file)->resize(1200, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                         $constraint->upSize();
                    })->save(storage_path('app/public/'.$location.'/' . $name));
                      
                    
                    $media = new \App\Models\HorseMultimedia();
                    $media->horse_id = $horse->id;
                    $media->media_link = $name;
                    $media->media_type=1;
                    $media->save();
                          
                          
                          
               
                  
                    
                    
                } catch (Exception $r) {
                    return back()->with('error', "Failed to upload image " . $r);
                }
            }
           }
        
        

        $horse->save();
     //   $horse->addToTimelineStory("Horse info is modified" ,"Horse internal information was modified by ".Auth::user()->name);
        return back()->with('message', $horse->name_en . " is updated succesfully.");
    }

    public function createHorse(Request $request) {

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'dob' => 'required',
                    'reg_no' => 'required',
                    'color' => 'required',
                    'gender' => 'required',
                    'breeder' => 'required',
                    'owner' => 'required',
                    'country' => 'required',
                 
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }




        $horse = new Horse();
        $horse->name_en = $request->name;
        
        


        
        
        $horse->dob = $request->dob;
        $horse->reg_no = $request->reg_no;
        $horse->color = $request->color;
        $horse->breeder_name = $request->breeder;
        $horse->owner_name = $request->owner;
        $horse->origin = $request->country;
        $horse->status = 'accepted';
        $horse->seller_id = Auth::id();

        if ($request->hasfile('passport_doc')) {
            $counter = 0;
            $file = $request->file('passport_doc');

            $fileArray = array('passport_doc' => $file);
            $rules = array(
                'passport_doc' => 'mimes:jpg,png,jpeg,gif,svg,wav,aac,mp3,m4a,pdf,doc,docx,txt|required|max:500000' // max 10000kb
            );
            $validator = Validator::make($fileArray, $rules);
            if ($validator->fails()) {
                return response()->json("passport_doc File format is not supported", 400);
            } else {





                if (in_array($file->getClientOriginalExtension(), ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {


                    $name = \App\Http\Controllers\Helpers\ImageUtils::saveImage($request, "passport_doc", "passports", 1200, 1200);

                    $horse->passport_doc = "passports/" . $name;
                } else {


                    $fileName = md5(time()) . '.' . $file->getClientOriginalExtension();
                    $path = "passports/" . date('Y') . "/" . date('m') . "/" . date('d') . "";
                    $filePath = $file->storeAs($path, $fileName, 'public');
                    $horse->passport_doc = $filePath;
                }
            }
        }



        if ($request->hasfile('vet_doc')) {
            $counter = 0;
            $file = $request->file('vet_doc');

            $fileArray = array('vet_doc' => $file);
            $rules = array(
                'vet_doc' => 'mimes:jpg,png,jpeg,gif,svg,wav,aac,mp3,m4a,pdf,doc,docx,txt|required|max:500000' // max 10000kb
            );
            $validator = Validator::make($fileArray, $rules);
            if ($validator->fails()) {
                return response()->json("vet_doc File format is not supported", 400);
            } else {





                if (in_array($file->getClientOriginalExtension(), ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {


                    $name = \App\Http\Controllers\Helpers\ImageUtils::saveImage($request, "vet_doc", "veterinary", 1200, 1200);

                    $horse->veterinary = "passports/" . $name;
                } else {


                    $fileName = md5(time()) . '.' . $file->getClientOriginalExtension();
                    $path = "veterinary/" . date('Y') . "/" . date('m') . "/" . date('d') . "";
                    $filePath = $file->storeAs($path, $fileName, 'public');
                    $horse->veterinary = $filePath;
                }
            }
        }







        $horse->save();
      
       
        
        
        if($request->has('sire') && $request->sire != ""){
            $this->createRelation("sire", $request->sire, $horse->id);
        }
           if($request->has('dam') && $request->dam != ""){
            $this->createRelation("dam", $request->dam, $horse->id);
        }
       
        
        
          if ($request->has('redirect_flag')) {
            return \Illuminate\Support\Facades\Redirect::to("horse-details/" . $horse->id);
        } else {
         return back()->with('message', $horse->name_en . " is added succesfully.");
        }
        
    }
function createRelation($rel ,$horse1,$horse2){
    
        $r = new HorseToHorseRelation();
             $r->horse_1_id = $horse1;
             $r->horse_2_id = $horse2;
             $r->horse_relation = $rel;
             if(HorseToHorseRelation::where('horse_1_id' ,  $r->horse_1_id)
                     ->where('horse_2_id' ,  $r->horse_2_id) ->where('horse_relation' ,  $r->horse_relation)->count()==0){
             $r->save();
             return true;
                     }
                     return false;
    
    
}



public function getHorseHistory(Request $request ,$id){

$response = [];
$horse  = Horse::find($id);
$data = AuctionHorseReg::where('horse_id' ,$id)->orderBy('id' ,'desc')->get();
foreach ($data as $d){
$obj = new \stdClass();
$obj->auction = Auction::find($d->auction_id)->name." (".$d->lot_type.")";
$obj->lot = $d->order_sn+1;
$obj->started = $d->lot_start_date!="" ? Carbon::parse( $d->lot_start_date)->format('d/m/y h:i') : "Not set";
$obj->finished = $d->lot_end_date!="" ? Carbon::parse( $d->lot_end_date)->format('d/m/y h:i') : "Not set";
$obj->status = $d->status_string;
$obj->selling = $d->target_type == 'horse' ? "Actaul horse" : (   $horse->gender != 'mare' ?  "Breeding right" : "Embryo" );
$obj->num_bids = \App\Models\Bid::where('lot_id' ,$d->id)->count();
$obj->max_bid = $obj->num_bids> 0 ? \App\Models\Bid::where('lot_id' ,$d->id)->where('status' ,1)->max('curr_amount') : "No bids";
$response[]=$obj;
}
 return $this->formResponse("Retrieved", $response, 200);   
    
}


    public function getHorsesAjax(Request $request) {


        $horses = Horse::where('name_en', 'like', $request->q . '%') ->
                where('status', 'accepted');
             
               if($request->has('target') && $request->target == 'horse'){
                 $horses=    $horses   ->whereNotIn('id',
                          AuctionHorseReg::where('status_string', 'sold')
                        ->orWhere('status_string', 'created')
                        ->orWhere('status_string', 'started')
                          ->orWhere('status_string', 'stopped')
                        ->pluck('horse_id')->toArray()
                ) ;
               }
               if($request->has('gender')){
               $horses=    $horses   ->where('gender' ,$request->gender);    
               }
               if(Auth::user()->sellsHorses()){
               $horses=    $horses   ->where('seller_id' ,Auth::id());    
               }
               
        
            $horses=    $horses->take(25)->orderBy('name_en')->get();

        $rend = new \stdClass();
        $list = [];
        foreach ($horses as $h) {
            $s = new \stdClass();
            $s->id = $h->id;
            $s->text = $h->name_en. " (".$h->gender.")";
            $list[] = $s;
        }
        $rend->results = $list;
        $pagination = new \stdClass();
        $pagination->more = false;
        $rend->pagination = $pagination;

        return response()->json($rend);
    }

}
