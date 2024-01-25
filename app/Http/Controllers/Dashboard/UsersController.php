<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\ImageUtils;
use App\Models\User;
use DB;
class UsersController extends Controller
{
    //
    
    
        
        function editProfile(Request $request)
    {
       
          
            $u = Auth::user();
            if($request->has('name')){
                $u->name = $request->name;
            }
            
            
             if($request->has('email')){
                 
        if($u->email != $request->email)   {      
          $this->validate($request, [
            'email' => 'required|unique:users,email',
        ]);
 
                 
                $u->email = $request->email;
                $u->is_email_verified = 0;
        }
            }
            
              if($request->has('phone')){
                  
      if($u->phone != $request->phone)   { 
                                       $this->validate($request, [
            'phone' => 'required|unique:users,phone',
                     ]); 
                $u->phone = $request->phone;
                $u->is_phone_verified = 0;
      }
            }
            
              if($request->has('country')){
                $u->country = $request->country;
            }
            
              if($request->has('about')){
                $u->about = $request->about;
            }
            
               if($request->has('password')){
                $u->password = bcrypt($request->password);
            }
             
            
            $u->save();
            
            return back()->with('message', 'Saved succesfully');
        
    }
    
    
    
      public function deleteUser(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        $u = User::find($request->user_id);
        $u->delete();
          return \Illuminate\Support\Facades\Redirect::to('users')
                  ->with('message', "User is deleted succesfully");
            
        }
    
        public function changeRole(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'role_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        
           DB::delete("delete from model_has_roles  where  model_id=? " ,[$request->user_id ]);  
           if($request->role_id != "Normal"){
           $user = User::find($request->user_id);
           $user->assignRole($request->role_id );
           
               return back()->with('message', "User role has been changed");
           }else{
               
           }
          return back()->with('message', "Model is now a normal user");
            
        }
        public function editWalletAmount(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'amount' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        $u = User::find($request->user_id);
        $u->wallet_amount = $request->amount;
        $u->save();
        
             $update = new \App\Models\ActivityTracker();
        $update->target_id =  $u->id ;
        $update->target_type = "wallet-recharge";
        $update->contents =   $u->name." wallet is charged by admin with amount :  ".$request->amount;
        $update->save();
        
        
          return back()->with('message', "User wallet amount changed");
            
        }
        public function editUserByAdmin(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                   
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        $u = User::find($request->user_id);
        if($request->has('phone'))
        $u->phone = $request->phone;
        
          if($request->has('country'))
        $u->country = $request->country;
          
          
            if($request->has('country_code'))
        $u->country_code = $request->country_code;
            
            
              if($request->has('phone_code'))
        $u->phone_code = $request->phone_code;
        
        
              if($request->has('is_phone_verified'))
        $u->is_phone_verified = $request->is_phone_verified;
        
              if($request->has('is_email_verified'))
        $u->is_email_verified = $request->is_email_verified;
        
            if($request->has('password'))
        $u->password = bcrypt($request->password);
        
                if($request->has('fcm') && $request->fcm!=""){
                     $u->save();
                $u->sendNotification("general", "", $request->fcm, null);
                 return back()->with('message', "Message sent");
                }
       
            
            
            
        $u->save();
          return back()->with('message', "Modified");
            
        }
        public function blockUser(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        $u = User::find($request->user_id);
        $u->is_blocked = 1;
        $u->save();
          return back()->with('message', "User is now blocked , he cant bid anymore");
            
        }
          public function unblockUser(Request $request) {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }
        $u = User::find($request->user_id);
        $u->is_blocked = -1;
        $u->save();
          return back()->with('message', "User is now ublocked , he can bid again");
            
        }
    

        public function sendFCM(Request $request) {

        $validator = Validator::make($request->all(), [
                    'message' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }

           $m =  User::sendNotificationToAll("general", -1, $request->message, null);
            return back()->with('message', "Message sent succesfully with code : ".  $m );



    }

}
