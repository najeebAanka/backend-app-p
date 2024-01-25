
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Users  </title>
 
@include('dashboard.shared.css')
<?php    $request = request(); ?> 
<style>
    img.ico-sm {
    width: 75px;
}
   
tr.seen--1 {
    background-color: #d5ffd5;
}
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
     <div class="bg-trans p-2">
    <div class="pagetitle">
      <h1>Users</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
     </div>
    <section class="section dashboard">
        
           @include('dashboard.shared.messages')
        
      <div class="row">
            <div class="col-md-4">      <div class="card p-2">
              <p class="p-1 font-bold "><b>Search filter </b></p>  
              <form>
              <div class="row">
                  <div class="col-md-12 p-2" ><label class="p-1"><i class="fa-solid fa-user"></i> User info</label><input 
                          
                          value="{{$request->has('name') && $request->name!="" ? $request->name : ""}}"
                          name="name" class="form-control" type="text" placeholder="Name / Phone / Email" /></div>
               
                  <div class="col-md-12 p-2"><label class="p-1"><i class="fa-solid fa-globe"></i> Type</label>
                      
                      
                      <select class="form-control"  name="status" >
                       <option value="all">All</option>
                          <option value="selling" {{ $request->has('status') && $request->status=="selling" ? "selected" : "" }} >Selling only</option>
                          <option value="bidding" {{ $request->has('status') && $request->status=="bidding" ? "selected" : "" }} >Bidding only</option>
                          <option value="blocked" {{ $request->has('status') && $request->status=="blocked" ? "selected" : "" }} >Blocked</option>
                          
                          
                      </select>
                  
                  </div>
                
                  
              </div> 
              <hr />
              <div class="text-right">
                  <button class="btn btn-success"><i class="fa fa-search"></i> Search </button>  
              </div>
              </form>
          </div></div>
          <div class="col-md-8">  
                  <div class="card p-2 table-container">   
                      <div class="table-responsive">
              <table class="table table-bordered bg-white">
              
              <tr style="background-color: #ffe3cb">
                  <th colspan="4">Basic information</th>  
                  <th colspan="2">Bidder activities</th>  
                  <th >Seller activities</th>  
                  
                  
              </tr>
              <tr style="background-color: #fff5ee">
                 
                  <th>Name</th>
                   <th>Phone</th>
                    <th>Email</th>
                    <th>Source</th> 
        
                  
          <th>Bids amount</th>
          <th>Lots joined</th>
            <th>Horses count</th>      
              </tr>    
         <?php
         
         
         $data = \App\Models\User::where('user_type' ,'<>' ,0)->orderBy('id' ,'desc');
              $name_pref = "";
      
        
        
         if($request->has('name') && $request->name!=""){
           $data= $data ->whereIn('id'  , \App\Models\User::where('name' ,'like' ,'%'.$request->name.'%')
                   ->orWhere('email' ,'like' ,'%'.$request->name.'%')->orWhere('phone' ,'like' ,'%'.$request->name.'%')
              ->pluck('id')->toArray()) ; 
           
        }
        
        if($request->has('status') && $request->status=="selling"){
           $data= $data ->whereIn('id' , \App\Models\Horse::select(['seller_id'])->groupBy('seller_id')->pluck('seller_id')->toArray()) ; 
        }
            if($request->has('status') && $request->status=="blocked"){
           $data= $data ->where('is_blocked' , 1) ; 
        }
          if($request->has('status') && $request->status=="bidding"){
           $data= $data ->whereIn('id' , \App\Models\Bid::select(['user_id'])->groupBy('user_id')->pluck('user_id')->toArray()) ; 
        }
         $data = $data->paginate(20);
         foreach ( $data as $u){
             ?>
              
              
              
              <tr class="seen-{{$u->seen}}">
                
                
            
                <td ><a style="font-weight: bold" href="{{url('users/'.$u->id)}}"><?=$u->name?></a></td>
                <td><?=$u->phone?> <?=$u->is_phone_verified == 1 ? '<i style="color: green;font-size: 11px" class="fa fa-check"></i>' : 
                     '<i style="color: red;font-size: 11px" class="fa fa-multiply"></i>'
                     ?></td>
                <td><?=$u->email?> <?=$u->is_email_verified == 1 ? '<i style="color: green;font-size: 11px" class="fa fa-check"></i>' : 
                     '<i style="color: red;font-size: 11px" class="fa fa-multiply"></i>'
                     ?></td>
                <td>
                    <?php if($u->login_provider_name == 'google') 
                        
                    {
                        echo '<img class="ico-sm" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/2560px-Google_2015_logo.svg.png" />';
                    }else if($u->login_provider_name == 'facebook'){
                       echo '<img class="ico-sm" src="https://logos-world.net/wp-content/uploads/2020/04/Facebook-Logo.png" />';
    
                    }else if($u->login_provider_name == 'apple'){
                       echo '<img class="ico-sm" src="https://1000logos.net/wp-content/uploads/2016/10/Apple-Logo.png" />';
    
                    }else{ ?>
                    <span style="color: #795548;
    font-weight: bold;
    text-transform: capitalize;
    font-size: 1.3rem;">
                    <?php
                       echo $u->login_provider_name; ?>
                    </span>
                    <?php
                    }
                     ?>
                </td>
               
           
                 <td><?= App\Models\Bid::where('user_id' ,$u->id)->sum('inc_amount')?></td>
                 <td><?= App\Models\Bid::where('user_id' ,$u->id)->groupBy('lot_id')->count()?></td>
                  <td><?= App\Models\Horse::where('seller_id' ,$u->id)->count()?></td>
              </tr>     
              
              <?php
              if($u->seen == -1){
              $u->seen=1;
              $u->save();
              }
         }
         
         ?>     
              
          </table>
                          </div>
   <div class="d-flex">
                {!!  $data->links() !!}
            </div></div></div>
        
          
      

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>

</html>