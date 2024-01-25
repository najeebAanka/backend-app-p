<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Helpers;
use stdClass;
/**
 * Description of EmailUtils
 *
 * @author braainclick
 */
class EmailUtils {
    //put your code here
    
    public static function sendUniversalEmail($user ,$email){
  if(!config('app.debug')){
          \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\UniversalEmailTemplate($email));
  }     
     
//$email = new stdClass();
//
//$email->title = "Example email";
//$email->contents = "Hello from test auction !";
//$email->has_button = true;
//$email->button_text = "Verify email";
//$email->button_link = url('');   
//        
        
        
    }
    
    
}
