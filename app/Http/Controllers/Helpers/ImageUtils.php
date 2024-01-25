<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Http\Controllers\Helpers;
/**
 * Description of Utils
 *
 * @author braainclick
 */
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImageUtils {
    //put your code here
    public static function saveImage($request , $key ,$location ,$mxw=1200 ,$mxh=1200){
        
        if ($request->hasFile($key)) {
            $file = $request->only($key)[$key];
            $fileArray = array('image' => $file);
            $rules = array(
                'image' => 'mimes:jpg,png,jpeg|required|max:500000' // max 10000kb
            );
            $validator = Validator::make($fileArray, $rules);
            if ($validator->fails()) {
                return false;
                ;
            } else {
                $uniqueFileName = uniqid()
                        . '.' . $file->getClientOriginalExtension();
                $name = date('Y') . "/" . date("m") . "/" . date("d") . "/" . $uniqueFileName;
                try {
                    if (!Storage::disk('public')->has($location.'/' . date('Y') . "/" . date("m") . "/" . date("d") . "/")) {
                        Storage::disk('public')->makeDirectory($location.'/' . date('Y') . "/" . date("m") . "/" . date("d") . "/");
                    }

                    Image::make($file)->resize($mxw, $mxh, function ($constraint) {
                        $constraint->aspectRatio();
                         $constraint->upSize();
                    })->save(storage_path('app/public/'.$location.'/' . $name));
                          return $name;
                } catch (Exception $r) {
                     return false;
                }
            }
        }  
            return false;
        
    }
}
