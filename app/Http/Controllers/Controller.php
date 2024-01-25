<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    
   protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        // Get all the errors thrown
        $errors = collect($validator->errors());
        // Manipulate however you want. I'm just getting the first one here,
        // but you can use whatever logic fits your needs.
        $error = $errors->unique()->first();
        // Either throw the exception, or return it any other way.
        return $error[0];
    }

    public function formResponse($message ,$data ,$status){
           return response()->json(['message'=>$message , 'data' => $data ], $status
                , ['Content-Type' => 'application/json;charset=UTF-8','Charset' => 'utf-8'],
                        JSON_UNESCAPED_UNICODE);
 
}




    
    
    
}
