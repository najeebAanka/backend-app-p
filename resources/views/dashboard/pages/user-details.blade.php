
<!DOCTYPE html>
<html lang="en">
<?php $u = \App\Models\User::find(Route::input('id')); 

if($u){

?>
       <?php $currentUser = Auth::user(); ?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Users ( Bidders ) </title>
 
@include('dashboard.shared.css')
<style>


    img.ico-sm {
    width: 75px;
}
    
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
  <div class="bg-trans p-2">
    <div class="pagetitle">
      <h1>Bidders</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{url('users')}}">Users</a></li>
          <li class="breadcrumb-item active"><?=$u->name?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
         @include('dashboard.shared.messages')
      <div class="row">
          <div class="col-md-6">   
              
                    <div class="card p-2 table-container"> 
              
              <table class="table table-bordered bg-white">
              
  
              
              
              
            <tr>
                
                
            
                   <th>Name</th>    <td ><a style="font-weight: bold" href="{{url('user-details/'.$u->id)}}"><?=$u->name?></a></td>
            </tr><tr>   <th>Phone</th>  <td><?=$u->phone?> <?=$u->is_phone_verified ==1  ? '<i style="color: green;font-size: 11px" class="fa fa-check"></i>' : 
                     '<i style="color: red;font-size: 11px" class="fa fa-multiply"></i>'
               ?> | <a href="https://wa.me/{{$u->phone}}">Open in WhatsApp  </a></td>
              </tr><tr>   <th>Email</th>      <td><?=$u->email?> <?=$u->is_email_verified  ==1 ? '<i style="color: green;font-size: 11px" class="fa fa-check"></i>' : 
                     '<i style="color: red;font-size: 11px" class="fa fa-multiply"></i>'
                     ?></td>
               
                 </tr><tr>   <th>Country</th>   <td><?=$u->country_code?> <img src='<?= url('dist/assets/img/flags/' . strtolower($u->country_code) . '.png')?>' style="width: 50px;" /></td>
                </tr><tr>   <th>Join date</th>    <td><?=$u->created_at?></td>
               
              </tr><tr>   <th>Account source</th>      <td><?= $u->login_provider_name?></td>
             </tr><tr>   <th>Last login</th>       <td><?= \App\Http\Controllers\Helpers\TimeUtils::humanTiming($u->last_login)?> ago</td>
                </tr><tr>   <th>Last login IP</th>    <td><?=$u->last_login_ip?></td>
              </tr><tr>   <th>Total bids amount</th>       <td><?= App\Models\Bid::where('user_id' ,$u->id)->sum('inc_amount')?></td>
                 </tr><tr>   <th>Total Lots joined</th>    <td><?= App\Models\Bid::where('user_id' ,$u->id)->groupBy('lot_id')->count()?></td>
                </tr><tr>   <th>Published horses</th>      <td><?= App\Models\Horse::where('seller_id' ,$u->id)->count()?></td>
              </tr>     
              
              <tr>   <th>Current wallet Amount</th>      <td><?= $u->wallet_amount?></td>
              </tr>     
              
              <tr>     <td colspan="2">
                  
                         @if($currentUser->can('delete-users'))       
            <div class="card p-2">     
                <p style="color: #1d8b12;font-size: 1.4rem">Wallet options</p>
                  
                    <form method="post" action="{{url('operations/users/wallet-recharge-by-admin')}}">
                     {{csrf_field()}}
                     <input
                         type='hidden' name="user_id" value="{{$u->id}}" />
                     <input type="number" name="amount" required style="background-color: #f8f8f8"  class="form-control mt-2 mb-2" placeholder="Amount to recharge .." />
                     <button class="btn btn-success">Save new amount</button>
                  </form> 
               </div> 
               @endif  
               
               
              
               
                  
                  </td>
              </tr>     
              
           
              
          </table>
                    
                    
                    </div>
                     @if($currentUser->can('delete-users'))       
            <div class="card p-2">     
                <p style="color: #1d8b12;font-size: 1.4rem">Modification by admin</p>
                  
                    <form method="post" action="{{url('operations/users/edit-user-admin')}}">
                     {{csrf_field()}}
                     <input
                     type='hidden' name="user_id" value="{{$u->id}}" />
                     
                        <p class="mt-2 mb-2">Phone</p> 
                     <input type="text" name="phone" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Phone" value="{{$u->phone}}" />
                        <p class="mt-2 mb-2">Country</p> 
                         <input type="text" name="country" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Country" value="{{$u->country}}" />
                            <p class="mt-2 mb-2">Country code</p> 
                                <input type="text" name="country_code" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Country code" value="{{$u->country_code}}" />
                                
                                   <p class="mt-2 mb-2">Phone code</p> 
                                    <input type="text" name="phone_code" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Phone code" value="{{$u->phone_code}}" />
                                    
                                    
                                   
                                       <p class="mt-2 mb-2">Phone verification status</p> 
                                       <select     class="form-control mt-2 mb-2" name="is_phone_verified">
                                           <option value="-1" {{$u->is_phone_verified == -1 ? "selected" : ""}}>No</option>   
                                           <option value="1" {{$u->is_phone_verified ==  1 ? "selected" : ""}}>Yes</option>   
                                           
                                       </select>              
                                   <p class="mt-2 mb-2">Email verification status</p> 
                                       <select     class="form-control mt-2 mb-2" name="is_email_verified">
                                           <option value="-1" {{$u->is_email_verified == -1 ? "selected" : ""}}>No</option>   
                                           <option value="1" {{$u->is_email_verified ==  1 ? "selected" : ""}}>Yes</option>   
                                           
                                       </select>            
                     
                     <button class="btn btn-success">Modify account</button>
                  </form> 
                
                <hr />
                
                
                <p style="color: #1d8b12;font-size: 1.4rem">Password</p>
                
                   <form method="post" action="{{url('operations/users/edit-user-admin')}}">
                     {{csrf_field()}}
                     <input
                     type='hidden' name="user_id" value="{{$u->id}}" />
                     
                        <p class="mt-2 mb-2">Password</p> 
                        <input type="password" name="password" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Password"  />
                             
                     
                     <button class="btn btn-success">Change password</button>
                  </form> 
                
                <hr />
                     <p style="color: #1d8b12;font-size: 1.4rem">FCM notification</p>
                @if($u->fcm_token != "")
                    <form method="post" action="{{url('operations/users/edit-user-admin')}}">
                     {{csrf_field()}}
                     <input
                     type='hidden' name="user_id" value="{{$u->id}}" />
                     
                        <p class="mt-2 mb-2">Message</p> 
                        <input type="text" name="fcm" required style="background-color: #f8f8f8" 
                            class="form-control mt-2 mb-2" placeholder="Write your message here..."  />
                             
                     
                     <button class="btn btn-info">Send message</button>
                  </form> 
                @else
                  <p style="color: #1d8b12;font-size: 1.2rem">User device fcm token is not set</p>
                @endif
                
                
               </div> 
               @endif  
               
          
          </div>
          <div class="col-md-6 ">
              
             
                  @if($currentUser->can('modify-roles-permissions'))   
              <div class="card p-2">
            
              
                        <p>User roles and permissions</p>
                  <hr />
                  
                 <form method="post" action="{{url('operations/users/change-role')}}">
                     {{csrf_field()}}
                     <input
                         type='hidden' name="user_id" value="{{$u->id}}" />
                 
                     
                     <div class="">
                    <div class="form-check">
                      <input class="form-check-input"  <?=$u->hasRole('Admin') ? "checked" : ""?>  name="role_id" value="Admin" type="radio">
                      <label class="form-check-label" for="gridRadios1">
                       Admin
                      </label>
                      <br /><small style="color: #a0a0a0">Full access to everything</small>
                    </div>
                    <div class="form-check">
                      <input  class="form-check-input"  <?=$u->hasRole('Editor') ? "checked" : ""?> type="radio" name="role_id" value="Editor">
                      <label class="form-check-label" for="gridRadios2">
                       Editor
                      </label>
                          <br /><small style="color: #a0a0a0">Accessing blogs , news , static content</small>
                    </div>
                    <div class="form-check">
                      <input  class="form-check-input"  <?=$u->hasRole('Auctioneer') ? "checked" : ""?> type="radio" name="role_id" value="Auctioneer">
                      <label class="form-check-label" for="gridRadios2">
                       E-Auctioneer
                      </label>
                          <br /><small style="color: #a0a0a0">Able to bid in offline auctions</small>
                    </div>
                    <div class="form-check disabled">
                      <input class="form-check-input" <?=!($u->hasRole('Admin') || $u->hasRole('Editor') || $u->hasRole('Auctioneer')) ? "checked" : ""?>
                             type="radio" name="role_id" value="Normal" >
                      <label class="form-check-label" for="gridRadios3">
                     Normal Seller/Bidder
                      </label>
                    </div>
                  </div>
                     
                   
                  
                     <button class="btn btn-warning mt-5">Set user role</button>
                  </form>  
               
                  
              </div>
                @endif
                  
                  
                  
                       @if($currentUser->can('block-unblock-users'))   
                  @if($u->is_blocked == -1)
                          <div class="card p-2">  
                                   <p>This user is active</p>
                  <form method="post" action="{{url('operations/users/block')}}">
                     {{csrf_field()}}
                     <input
                         type='hidden' name="user_id" value="{{$u->id}}" />
                     <button class="btn btn-warning">Block</button>
                  </form>
                          </div>
                  @else
                          <div class="card p-2">   
                              <p>This user is blocked by adminstration</p>
                    <form method="post" action="{{url('operations/users/unblock')}}">
                     {{csrf_field()}}
                     <input
                         type='hidden' name="user_id" value="{{$u->id}}" />
                     <button class="btn btn-success">Unblock</button>
                  </form> 
                   </div>
                  @endif
                 @endif   
                  
               @if($currentUser->can('delete-users'))       
            <div class="card p-2">     
                  <p>Advanced options</p>
                  
                    <form method="post" action="{{url('operations/users/delete')}}">
                     {{csrf_field()}}
                     <input
                         type='hidden'name="user_id" value="{{$u->id}}" />
                     <button class="btn btn-danger">Delete account</button>
                  </form> 
            </div> 
               @endif
               
           
              
          </div>
  

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>
<?php }else echo 'This account has been deleted';  ?>
</html>