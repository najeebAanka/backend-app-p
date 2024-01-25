<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\ImageUtils;
use App\Models\StaticContent;
class StaticContentController extends Controller
{
    //
    
    
    public function fetch($id){
      return $this->formResponse("Fetched",   StaticContent::where('static_key' ,$id)->first(), 200);   
    }
        public function editContent(Request $request) {

        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'key' => 'required',
                    'contents' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }

       StaticContent::where('static_key' ,$request->key)->update(["title"=>$request->title ,"static_content"=>$request->contents]);
      
     
        return back()->with('message', "Saved succesfully.");
        


    }
      
    
}
