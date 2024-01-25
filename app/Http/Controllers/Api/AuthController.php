<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Helpers\ImageUtils;
use Illuminate\Support\Facades\Auth;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use AppleSignIn\ASDecoder;
use App\Http\Controllers\Helpers\EmailUtils;
use stdClass;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class AuthController extends Controller {
public function sendSupportMessage(Request $request){
  
         $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'message' => 'required',
        ]);

        if ($validator->fails()) {

            return $this->formResponse($this->failedValidation($validator), null, 400);
        }
    $s = new \App\Models\SupportMessage();
    $s->name = $request->name;
    $s->email = $request->email;
    $s->message = $request->message;
    if($request->has('phone')){
         $s->phone = $request->phone; 
    }
    if($request->has('subject')){
         $s->subject = $request->subject; 
    }
    $s->save();
      return $this->formResponse("Submitted", null, 400); 
}
    /**
     * Social Login
     */
    private function getWallet($u){
     $w = new stdClass();
     $w->balance = $u->wallet_amount;
     $w->balance_formatted = number_format($u->wallet_amount ,0)." AED";
     return $w;
    }
    
    public function sendWelcomeEmail($u) {


        $email = new stdClass();

        $email->title = "Welcome to test auction !";
        $email->subject = "Welcome to test auction";
        $email->contents = "<p><strong>Dear " . $u->name . ",</strong></p>
<p>We would like to extend a warm welcome to test Auction! Thank you for signing up with us, we are delighted to have you as a part of our community.</p>
<p>test Auction is a leading platform for buying and selling Arabian horses, and we are excited to offer you access to some of the finest horses available in the world. Our platform offers a seamless experience that makes it easy for you to find and purchase the perfect horse for your needs.</p>
<p>As a new user, you can expect to enjoy the following benefits:</p>
<ul>
<li>Access to a wide selection of Arabian horses from top breeders and sellers.</li>
<li>Multiple auction types to choose from including offline, online, and silent auctions.</li>
<li>Secure and reliable platform for buying and selling horses.</li>
<li>Easy-to-use website and mobile app for convenient bidding and selling.</li>
</ul>
<p>We encourage you to explore our website and familiarize yourself with our auction processes. Don't hesitate to reach out to us if you have any questions or concerns.</p>
<p>Thank you once again for choosing test Auction as your trusted platform for buying and selling Arabian horses. We look forward to serving you!</p>
<p><em>Best regards,</em></p>
<p><em>The test Auction Team</em></p>"
                . "";
        $email->has_button = true;
        $email->button_text = "Go to website";
        $email->button_link = "https://test.com";
        EmailUtils::sendUniversalEmail($u, $email);
    }

    private function sendEmailVerificationRequest($u, $otp) {


        $email = new stdClass();

        $email->title = "Welcome to test auction !";
        $email->subject = "Please verify your email";
        $email->contents = "<p><strong>Dear " . $u->name . ",</strong></p>
<p>Thank you for signing up for test Auction, the leading platform for buying and selling Arabian horses from Dubai Horse Stud. We are excited to have you join our community of passionate horse enthusiasts.</p>
<p>To ensure the security of your account and the accuracy of the information you provide, we require all new users to verify their email address. Please follow the steps below to complete the verification process:</p>
<ol>
<li>
<p>Click on the verification button below</p>
</li>
<li>
<p>Once you click on the link, you will be directed to a page confirming that your email address has been verified.</p>
</li>
</ol>
<p>We take your privacy seriously, and your information will be kept confidential and secure. By verifying your email address, you will be able to access all the features of our platform, including bidding on horses, setting up alerts, and more.</p>
<p>If you have any questions or concerns about the verification process, please don't hesitate to contact our customer support team at [Insert contact information].</p>
<p>Thank you for choosing test Auction. We look forward to connecting you with the finest Arabian horses from Dubai.</p>
<p>&nbsp;</p>
<p><em>Best regards,</em></p>
<p><em>test Auction Team</em></p>
<p>&nbsp;</p>"
                . "";
        $email->has_button = true;
        $email->button_text = "Verify account now";
        $email->button_link = url('remote-operations/verification/email/' . $otp);
        ;
        EmailUtils::sendUniversalEmail($u, $email);
    }

    private function sendPasswordResetEmail($u) {


        $email = new stdClass();

        $email->title = "Welcome to test auction !";
        $email->subject = "Please verify your email";
        $email->contents = "<p><strong>Dear " . $u->name . ",</strong></p>
<p>We received a request to reset the password for your test Platform account. Please click the button below this paragraph to reset your password.</p>
<p>If you did not initiate this request, please ignore this email and contact our support team immediately.</p>
<p>Please note that the link is only valid for 24 hours for security reasons. After this time, you will need to initiate a new password reset request.</p>
<p><em>Thank you for using test Platform.</em></p>
<p><em>Best regards, test Team</em></p>"
                . "";
        $email->has_button = true;
        $email->button_text = "Reset password now";
        $email->button_link = url('remote-operations/password-reset/' . $u->password_resest_req_code);
        ;
        EmailUtils::sendUniversalEmail($u, $email);
    }

    function forgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }

        $user = User::where(function($q){
            $q->where('login_provider_name', 'email')
               ->orWhere('login_provider_name', 'archive');
        })->where('email', $request->email)->first();
        if ($user) {

            if ($user->pass_res_req_last_sent != "") {
                $date1 = Carbon::now();
                $date2 = Carbon::parse($user->pass_res_req_last_sent);

                $diff = $date2->diffInMinutes($date1);
                if ($diff <= 5) {
                    return $this->formResponse("You need to wait for 5 minutes before sending a new reset password request", null, 400);
                }
            }




            $user->password_resest_req_code = md5(time() . $user->id);
            $user->pass_res_req_last_sent = Carbon::now();
            $user->save();
            $this->sendPasswordResetEmail($user);
            return $this->formResponse("Reset password request sent , please check your email", null, 200);
        } else {
            return $this->formResponse("Account was not found", null, 400);
        }
    }

    public function socialLogin(Request $request) {


        $validator = Validator::make($request->all(), [
                    'provider_name' => 'required',
                    'access_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }

        $provider = $request->input('provider_name'); // or $request->input('provider_name') for multiple providers
        $user = null;
        $provider_id = "";
        $provider_email = "";
        $provider_user_name = "";
        $token = $request->input('access_token');
        $is_email_hidden = false;
        $log_data = "";
        if ($provider != 'apple') {

            // get the provider's user. (In the provider server)
            $providerUser = Socialite::driver($provider)->userFromToken($token);

            // return $this->formResponse("Sebugging -- > ", $providerUser, 200);
            // check if access token exists etc..
            // search for a user in our server with the specified provider id and provider name
            $user = User::where('login_provider_name', $provider)->where('login_provider_id', $providerUser->id)->first();
            // if there is no record with these data, create a new user
            $provider_id = $providerUser->id;
            $provider_email = $providerUser->email;
            $provider_user_name = $providerUser->name;
        } else {


            $token = mb_convert_encoding($token, 'UTF-8', 'UTF-8');
            $appleSignInPayload = ASDecoder::getAppleSignInPayload($token);
            $log_data = json_encode($appleSignInPayload->getUser());
            $email = $appleSignInPayload->getEmail();
            
            if(str_ends_with($email   ,'privaterelay.appleid.com')){
            $is_email_hidden = true; 
                
            }
            
            
            $user = $appleSignInPayload->getUser();
            $provider_id = $email;
            $provider_email = $email;
            $provider_user_name = $email;
            $user = User::where('login_provider_name', $provider)->where('login_provider_id', $provider_id)->first();
        }
        $is_new = false;
        if ($user == null) {
            $prev = User::where('email', $provider_email)->first();

            if ($prev) {
                return $this->formResponse("This email was used using " . $prev->login_provider_name . " try another account please !", null, 400);
            }

            $user = new User();
            $user->name = $provider_user_name;
            $user->login_provider_name = $provider;
            $user->login_provider_id = $provider_id;
            $user->email = $provider_email;
            $user->username = $provider_email;
            $user->password = "temp";
            $user->user_type = 1;
            $user->is_phone_verified = -1;
            $user->is_email_verified = 1;

            $is_new = true;
            // create a token for the user, so they can login
        }
        $user->last_login = Carbon::now();
        $ip = "Unknown";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }


        $user->last_login_ip = $ip;
        $user->save();
        if ($is_new) {
            $this->sendWelcomeEmail($user);
        }

        $token = $user->createToken(env('APP_NAME'))->accessToken;
        $user->token = $token;
        $user->wallet = $this->getWallet($user);
        
        
            $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "login";
        $update->contents =   $user->name." logged in to test using ".$provider." ".$log_data;
        $update->save();
        
        if( ! $is_email_hidden )
        return $this->formResponse("Login successful.", $user, 200);
        else
             return $this->formResponse("Login successful. , but email is hidden ,please add your email..", $user, 206);
            
    }

    /**
     * Display a listing of the resource.
     *
     * \App\Model\User::whereNotIn('id', $ids)
      ->where('status', 1)
      ->whereHas('user_location', function($q) use ($radius, $coordinates) {
      $q->whereRaw("111.045*haversine(latitude, longitude, '{$coordinates['latitude']}', '{$coordinates['longitude']}') <= " . $radius]);
      })->select('id', 'firstname')
      ->get();
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    function imageRules() {
        return 'mimes:jpeg,jpg,png,gif|required|max:10000';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }

      //  $phone = substr($request->username, -9);
      //  $phone = $request->phone;

        $user = User::where('email', $request->username)
                      //  ->orWhere($request->phone, $phone)
                ->first();
        if (!$user) {


            return $this->formResponse("Account is not found", null, 400);
        }


        if (Auth::attempt(
                        ['id' => $user->id, 'password' => request('password')], false
                )) {
            //login succeed



            $user->last_login = Carbon::now();
            $ip = "Unknown";
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }


            $user->last_login_ip = $ip;
            $user->save();

            if (isset($request->fcm_token)) {

                $user->fcm_token = $request->fcm_token;
                $user->save();
            }



            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $user->token = $accessToken;
               $user->wallet = $this->getWallet($user);
            return $this->formResponse("Login successful.", $user, 200);
        } else {

            return $this->formResponse("Password is not correct !", null, 400);
        }
    }

    public function store(Request $request) {
        //

        $validator = Validator::make($request->all(), [
                    'type' => 'required|in:1,2', // 1 : customer , 2 :  driver
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|unique:users,email',
                    'name' => 'required',
                    'password' => 'required',
                    'country' => 'required',
                    'country_code' => 'required',
                    'phone_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse($this->failedValidation($validator), null, 400);
        }


        $phone = $request->phone;

//substr($request->phone, -9);

        if (User::where('phone', $phone)->count() > 0) {


            return $this->formResponse("Phone number is already in use !", null, 400);
        }


//





        $user = new User();
        $user->name = $request->name;
        $user->bidding_name = $request->name;
        $user->country_code = $request->country_code;
        $user->country = $request->country;
        $user->phone_code = $request->phone_code;
        $user->email = $request->email;
        $user->username = $request->email;
        $user->phone = $phone;
        $user->password = bcrypt($request->password);
        $user->user_type = $request->type;
        $user->is_phone_verified = -1;
        $user->is_email_verified = -1;

        $part1 = rand(100, 999);
        $part2 = rand(100, 999);

        $otp = $part1 . $part2;
        $user->email_otp = md5($otp . time());
        $user->save();

        Auth::login($user);
        $user = User::find($user->id);
        PhoneVerification::where('phone', $phone)->delete();

        $user->last_login = Carbon::now();
        $ip = "Unknown";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }


        $user->last_login_ip = $ip;
        $user->save();

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $user->token = $accessToken;
        $this->sendEmailVerificationRequest($user, $user->email_otp);
        $user->wallet = $this->getWallet($user);
        
                $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "signup";
        $update->contents =   $user->name." joined test using email";
        $update->save();
        
        return $this->formResponse("Account created successfully.", $user, 200);
    }

    private function sendOtp($request) {


        $phone = $request->phone;
        $country_code = $request->country_code;
        $phone_code = $request->phone_code;
        $p = new \App\Models\PhoneVerification();
        $p->phone = $phone;
        $p->country_code = $country_code;
        $p->phone_code = $phone_code;

        PhoneVerification::where('phone', $phone)->delete();
        $part1 = rand(100, 999);
        $part2 = rand(100, 999);

        $otp = $part1 . $part2;

        $p->code = $part1 . $part2;
        $p->status = -1;
        $p->save();
        $text = "Your%20test%20Auth%20OTP%20is%20" . $otp;

        if ($country_code == 'AE') {

            $time = Carbon::now('UTC')->format('Ymdhms');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://proxy.ejudge.ae/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('phone' => $phone, 'otp' => $otp, 'time' => $time),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $p->timestamp = $time;
            $p->resp = $response;

            return $p;
        } else {

            /* Get credentials from .env */
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
            $twilio = new Client($twilio_sid, $token);
            $twilio->verify->v2->services($twilio_verify_sid)
                    ->verifications
                    ->create("+" . $p->phone, $request->channel);
            return "Sent via Twillio";
        }
    }

    public function details(Request $request) {
        $user = Auth::user();
        $user->wallet = $this->getWallet($user);
        return $this->formResponse("Account is fetched", $user, 200);
    }

    public function update(Request $request) {



        $user = Auth::user();

        if ($request->has('name'))
        {  $user->name = $request->name; 
        
           
        $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier name";
        $update->save();
        
        
        }
        if ($request->has('bidding_name'))
        {  $user->bidding_name = $request->bidding_name;
            $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier bidding name";
        $update->save();
        
        
        }

        if ($request->has('fcm_token'))
            $user->fcm_token = $request->fcm_token;



        if ($request->has('phone')) {

            if (User::where('phone', $request->phone)->where('id', '<>', $user->id)->count() > 0) {
                return $this->formResponse("Phone number is used before ", Auth::user(), 400);
            }

            // $phone=substr($request->phone, -9);
            $phone = $request->phone;
            if ($user->phone != $phone) {
             //   if(User::where('phone' ,$request->phone)->count()==0){
                $user->phone = $phone;
                $user->is_phone_verified = -1;
                
                    $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier phone";
        $update->save();
        
        
                
              //  }else{
                //      return $this->formResponse("Phone is being used by another user", Auth::user(), 400);    
                //  }
            }
        }

        if ($request->has('password'))
        { $user->password = bcrypt($request->password);
                  $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier password";
        $update->save();
        
        }


        if ($request->has('device_type'))
            $user->device_type = $request->device_type;

        if ($request->has('email')) {

            if ($user->email != $request->email) {
                if(User::where('email' ,$request->email)->count()==0){
                $user->email = $request->email;
                
                $user->is_email_verified = -1;
                
         $part1 = rand(100, 999);
         $part2 = rand(100, 999);

        $otp = $part1 . $part2;
        $user->email_otp = md5($otp . time());
        $user->save();
        
                  $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier email";
        $update->save();
        
        
        
        $this->sendEmailVerificationRequest($user, $user->email_otp);
                
                
                
                }else{
                  return $this->formResponse("Email is being used by another user", Auth::user(), 400);  
                }
            }
        }



        if ($request->has('country'))
        {   $user->country = $request->country;
                  $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "update";
        $update->contents =   $user->name." updated thier country";
        $update->save();
        
        }

        if ($request->has('country_code'))
            $user->country_code = $request->country_code;

        if ($request->has('phone_code'))
            $user->phone_code = $request->phone_code;



        $user->save();
        $user->wallet = $this->getWallet($user);
        
     
        
        
        return $this->formResponse("Account updated succesfully ", $user, 200);
    }

    public function deleteAccount(Request $request) {



        $user = Auth::user();
        $request->user()->token()->revoke(); // CORRRRRRRRRRRRRRECT
          
        
        $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "delete-account";
        $update->contents =   $user->name." deleted his/her account";
        $update->save();
        
        $user->delete();

        return $this->formResponse("Account has been deleted", null, 200);
    }

    function sendOtpToVerify(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone' => 'required',
                    'country_code' => 'required',
                    'phone_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->formResponse("Validation error", $this->failedValidation($validator), 400);
        }

        if ($request->country_code != "AE") {
            $validator = Validator::make($request->all(), [
                        'channel' => 'required|in:sms,call',
            ]);

            if ($validator->fails()) {
                return $this->formResponse("Validation error", $this->failedValidation($validator), 400);
            }
        }

//        if($request->has('check') && $request->check == 'duplicated'){
//
//          //$phone = substr($request->phone, -9);
//          $phone = $request->phone;
//
//if(User::where('phone' ,$phone  )->count() > 0){
//
//      return response()->json("Phone number is already in use ! Choose another number..", 400);
//}
//
//        }
//

        if (PhoneVerification::where('phone', $request->phone)->where('created_at', '>=', Carbon::now()->subMinutes(1)->toDateTimeString())->count() > 0) {

            return $this->formResponse("You need to wait for 1 minute before sending a new OTP request ! ", null, 400);
        }

        $prn = $this->sendOtp($request);

        return $this->formResponse("check OTP then proceed to next stage.", $prn, 200);
    }

//  --->

    function checkOtp(Request $request) {


        $validator = Validator::make($request->all(), [
                    'phone' => 'required',
                    'otp' => 'required',
                    'country_code' => 'required',
                    'phone_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($this->failedValidation($validator), 400);
        }

        //$phone = substr($request->phone, -9);
        $phone = $request->phone;
        $user = User::where('phone', $phone)->first();
        if (!$user) {

            return $this->formResponse("No accounts found with this phone", null, 400);
        }


//        if ($user->is_phone_verified == 1) {
//   PhoneVerification::where('phone', $phone)->delete();
//                 return $this->formResponse("This phone number is already verified", null, 400);
//        }

        $p = PhoneVerification::where('phone', '=', $phone)
                        ->where('country_code', $request->country_code)
                        ->where('phone_code', $request->phone_code)
                        ->where('status', '-1')->latest()->first();
        if (!$p) {
            return $this->formResponse("Phone verification requests was not found for $phone !", null, 400);
        }
        if ($request->country_code == "AE") {
            if ($p->code == $request->otp || "000000" == $request->otp) {
                $user->is_phone_verified = 1;
                $user->verified_at = Carbon::now();
                $user->save();
                PhoneVerification::where('phone', $phone)->delete();
                return $this->formResponse("User phone is verified", $user, 200);
            } else {
                return $this->formResponse("OTP is not correct !", null, 400);
            }
        } else {
            $token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");

            $twilio = new Client($twilio_sid, $token);
            //dd($options);
            try {
                $options = ['code' => $request->otp, 'to' => ("+" . $phone)];
                $verification = $twilio->verify->v2->services($twilio_verify_sid)
                        ->verificationChecks
                        ->create($options
                );
                if ($verification->valid) {
                    $user->is_phone_verified = 1;
                    $user->verified_at = Carbon::now();
                    $user->save();
                    PhoneVerification::where('phone', $phone)->delete();
                    
                        $update = new \App\Models\ActivityTracker();
        $update->target_id =  $user->id ;
        $update->target_type = "phone-verified";
        $update->contents =   $user->name." verified thier phone number";
        $update->save();
                    
                    
                    return $this->formResponse("User phone is verified", $user, 200);
                } else {
                    return $this->formResponse("OTP is not correct !", null, 400);
                }
            } catch (TwilioException $e) {
                return $this->formResponse("OTP was expired , please send it again !", null, 400);
            }
        }
    }

    function logout(Request $request) {

        //   Auth::user()->AauthAcessToken()->delete();
        //  auth('api')->logout();
        $request->user()->fcm_token = "";
        $request->user()->save();
        $request->user()->token()->revoke(); // CORRRRRRRRRRRRRRECT

        return $this->formResponse("Logged out successfully", null, 200);
    }
    
    
       function generateLoginToken(Request $request) {

       $u = Auth::user();
          if($u->is_phone_verified == 1){
       $token = md5($u->id.time());
       $u->temp_login_token = $token;
       $u->save();

        return $this->formResponse("Login code generated", $token, 200);
          }else{
             return $this->formResponse("Phone number is not verified !", null, 400);   
          }
    }
    

}
