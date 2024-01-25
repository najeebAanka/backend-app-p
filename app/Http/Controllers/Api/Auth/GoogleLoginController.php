<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;



class GoogleLoginController extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
        
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function callback()
    {
        try {
      
            $user = Socialite::driver('google')->user();
       
            $finduser = User::where('google_id', $user->id)->first();
       
            if ( $finduser ) {
       
                Auth::login($finduser);
      
             
                    $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $user->token = $accessToken;
            return $this->formResponse("Login successful.", $user, 200);
                
                
       
            } else {
                
//                $newUser = User::create([
//                    'name' => $user->name,
//                    'email' => $user->email,
//                    'google_id'=> $user->id,
//                    'password' => 'dummypass'// you can change auto generate password here and send it via email but you need to add checking that the user need to change the password for security reasons
//                ]);
       $newUser = new User();
        $newUser->name = $user->name;
        $newUser->google_id = $user->id;
        $newUser->email =$user->email;
        $newUser->password = "temp";
        $newUser->user_type = 1;
        $newUser->is_phone_verified  = -1;

   

              $newUser->save();
                Auth::login($newUser);
      
                 $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $newUser->token = $accessToken;
            return $this->formResponse("Login successful.", $newUser, 200);
                
            }
      
        } catch (Exception $e) {
      
            return $this->formResponse("Login failed.", $e->getMessage(), 400);
        }
    }
}
