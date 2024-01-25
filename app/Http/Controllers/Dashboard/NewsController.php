<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\ImageUtils;
class NewsController extends Controller
{
    //
    
        public function createArticle(Request $request) {

        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'contents' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }

        $blog = new \App\Models\Blog();
        $blog->title = $request->title;
        $blog->details = $request->contents;
        $blog->created_by = Auth::id();

        

        if ($request->hasFile('poster')){
            $path =  ImageUtils::saveImage($request, "poster", "blogs-media", 800, 800);
            if($path)
              $blog->thumb_url = $path;
            else{
             return back()->with('error', " 's poster can not be saved");
            }
        }
        $blog->save();
            return back()->with('message', $blog->title . " is added succesfully.");
        


    }
        public function editArticle(Request $request) {

        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'title' => 'required',
                    'contents' => 'required',
        ]);

        if ($validator->fails()) {

            return back()->with('error', $this->failedValidation($validator));
        }

        $blog = \App\Models\Blog::find($request->id);
        $blog->title = $request->title;
        $blog->details = $request->contents;
  

        

        if ($request->hasFile('poster')){
            $path =  ImageUtils::saveImage($request, "poster", "blogs-media", 800, 800);
            if($path)
              $blog->thumb_url = $path;
            else{
             return back()->with('error', " 's poster can not be saved");
            }
        }
        $blog->save();
            return back()->with('message', $blog->title . " is updated succesfully.");
        


    }
    
}
