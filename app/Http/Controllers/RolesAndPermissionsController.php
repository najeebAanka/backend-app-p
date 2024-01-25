<?php
namespace  App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesAndPermissionsController extends  Controller
{
    public function linkPermissionToRole(Request $request)
    {
       
          $validator = Validator::make($request->all(), [
                    'role_id' => 'required',
                    'perm_id' => 'required',
                    'status' => 'required|in:1,0',
              
        ]);

        if ($validator->fails()) {

            return $this->formResponse($this->failedValidation($validator), null, 200);
        }
        
        
       if($request->status == '1'){
           
       DB::insert('insert into role_has_permissions (role_id, permission_id) values (?, ?)', [$request->role_id ,$request->perm_id]);    
           
       }else{
           
         DB::delete('delete from role_has_permissions where role_id=? and permission_id=? ' ,[$request->role_id ,$request->perm_id]);  
           
       }
       app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        
       return $this->formResponse("Saved", null, 200);    
        
        
    }

 
}