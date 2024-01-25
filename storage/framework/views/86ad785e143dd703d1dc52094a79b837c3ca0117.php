
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <title>Dashboard - Horses</title>
<?php    $request = request(); ?> 
<?php echo $__env->make('dashboard.shared.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 <?php $currentUser = Auth::user(); ?>
<style>
    .horse-rejected{
        background-color: #ffe7e7;
    }
     .horse-pending{
         background-color: #fffde7;
    }
    
</style>
</head>

<body>
<?php echo $__env->make('dashboard.shared.nav-top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('dashboard.shared.side-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <main id="main" class="main">
      
      <div class="bg-trans p-2" style="margin: 2px">
      
  <button type="button" class="btn btn-primary" style="float: right" data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
            <i class="fa fa-plus"></i>    Add a new horse
              </button>
    <div class="pagetitle">
      <h1>
      
      <?php if(!$currentUser->isAdmin()): ?>
      <?php echo e($currentUser->name); ?>'s Horses
      <?php else: ?>
      All Horses 
      <?php endif; ?>
      
      </h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo e(url('home')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Horses</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    
         </div>
    <section class="section dashboard m-3">
                <?php echo $__env->make('dashboard.shared.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                
                
                
      <div class="row">
          
            <div class="card p-2 table-container">
                
                
                    <div class="card p-2">
              <p class="p-1 font-bold "><b>Search filter </b></p>  
              <form>
              <div class="row">
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-horse-head"></i> Horse name</p><input 
                          
                          value="<?php echo e($request->has('name') && $request->name!="" ? $request->name : ""); ?>"
                          name="name" class="form-control" type="text" placeholder="Horse name" /></div>
                          
                          
                     <?php if($currentUser->isAdmin()): ?>        
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-user"></i> Seller</p><input 
                          
                            value="<?php echo e($request->has('seller') && $request->seller!="" ? $request->seller : ""); ?>"
                          name="seller"  class="form-control" type="text" placeholder="Seller name/phone/email" /></div>
                          <?php endif; ?>
                          
                          
                          
                          
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-check"></i> Status</p>
                      
                      
                      <select class="form-control"  name="status" >
                       <option value="all">All</option>
                          <option value="shipped" <?php echo e($request->has('status') && $request->status=="shipped" ? "selected" : ""); ?> >Shipped</option>
                          <option value="sold" <?php echo e($request->has('status') && $request->status=="sold" ? "selected" : ""); ?> >Sold</option>
                          <option value="unsold" <?php echo e($request->has('status') && $request->status=="unsold" ? "selected" : ""); ?> >Unsold</option>
                          <option value="created" <?php echo e($request->has('status') && $request->status=="created" ? "selected" : ""); ?> >Joined auction</option>
                          <option value="started" <?php echo e($request->has('status') && $request->status=="started" ? "selected" : ""); ?> >Live now</option>
                          
                          
                      </select>
                  
                  </div>
                
                  
              </div> 
              <hr />
              <div class="text-right">
                  <button class="btn btn-success"><i class="fa fa-search"></i> Search </button>  
              </div>
              </form>
          </div>
                
                
                       <table class="table  table-bordered bg-white">
          
     
              <tr>
                  <th>Name</th>
                  <th>Gender</th>
                  <th>Date of birth</th>
                  <th>Owner</th>
                  <th>Lots count</th>
                  <th>Latest Status</th>
                  <th></th>
        
                  
              </tr>    
         <?php
         
         $data = \App\Models\Horse::where('horses.status' ,'<>' ,'generated')->orderBy('horses.id' ,'desc');
      
         if(!$currentUser->isAdmin()){
          $data = $data->where('seller_id' ,$currentUser->id);   
         }
         
         
         $name_pref = "";
        if($request->has('name') && $request->name!=""){
           $data= $data ->where('name_en' ,'like' ,'%'. $request->name .'%') ; 
            $name_pref = $request->name ;
        }
        
        
         if($request->has('seller') && $request->seller!=""){
           $data= $data ->whereIn('seller_id'  , \App\Models\User::where('name' ,'like' ,'%'.$request->seller.'%')
                   ->orWhere('email' ,'like' ,'%'.$request->seller.'%')->orWhere('phone' ,'like' ,'%'.$request->phone.'%')
                   ->where('user_type' ,2)->pluck('id')->toArray()) ; 
           
        }
        
        if($request->has('status') && $request->status!="all"){
            if($request->status!="shipped"){
           $data= $data ->
                   join('auction_horse_regs' ,'auction_horse_regs.horse_id' ,'horses.id')
                   ->select(['horses.*'])
                   ->where('auction_horse_regs.status_string' ,$request->status) ; 
            }else{
              $data= $data ->where('status' ,'shipped');    
            }
        }
        $is_admin = Auth::user()->isAdmin();
        $data= $data ->paginate(20);
         foreach (  $data as $horse){
             ?>
               <tr class="horse-<?php echo e($horse->status); ?>">
             
                <td><a href="<?php echo e(url('horse-details/'.$horse->id)); ?>">
                     <?=strtoupper($name_pref == "" ? $horse->name_en : str_ireplace($name_pref, "<span style='color : red'>".$name_pref."</span>",
                             $horse->name_en) )?></a></td>
                   <td><?=$horse->gender?></td>
                   <td><?=$horse->dob?></td>
                   <td><?=$horse->owner_name?></td>
                   <td><button class="btn btn-light" onclick="loadHorseStats(<?php echo e($horse->id); ?>)">
                     <?= App\Models\AuctionHorseReg::where('horse_id' ,$horse->id)->count()?></button></td>
             
             
                <td><b style="color: #777777"><?php echo e(strtoupper($horse->status)); ?> | <?php
                
               $s = App\Models\AuctionHorseReg::where('horse_id' ,$horse->id)->orderby('id' ,'desc')->first();
                
                
                echo $s ? strtoupper($s->status_string != 'created' ? $s->status_string  :"In lot" ) : "Not registered yet";
                ?> </b></td>
             
                <td><a href="<?php echo e(url('horse-details/'.$horse->id)); ?>">Details</a></td>
                  
              </tr>     
                
              
              <?php
         }
         
         ?>     
              
          </table>
          
           <div class="d-flex">
                <?php echo $data ->links(); ?>

            </div>
          
            </div>
          <div class="modal fade" id="ExtralargeModal"  >
                <div class="modal-dialog modal-xl">
                    <form method="post" action="<?php echo e(url('operations/horses/create')); ?>" enctype="multipart/form-data">
                      <?php echo e(csrf_field()); ?>

                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Add a new horse</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Horizontal Form -->
            
                <div class="row row g-3">
             
          <div class="col-md-4">
                  <label for="inputName5" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name">
                </div>
                    
                       <div class="col-md-4">
                  <label for="inputName5" class="form-label">Sire</label>
                  <select id="select-horse-stallion" class="form-control" name="sire"></select>
                </div>
                    
                       <div class="col-md-4">
                  <label for="inputName5" class="form-label">Dam</label>
               <select id="select-horse-mare" class="form-control" name="dam"></select>
                </div>
                    
                    
                      <div class="col-md-4">
                  <label for="inputName5"  class="form-label">Horse registration number</label>
                  <input type="text" required class="form-control" name="reg_no">
                </div>
                    
                    
                  
                         
                  <div class="col-md-4">
                  <label for="inputName5" class="form-label">Gender</label>
                  <select  class="form-control" name="gender">
                      <option value="stallion">Stallion</option>
                      <option value="mare">Mare</option>
                      
                  </select>
                </div>    
                    
               <div class="col-md-4">
                  <label for="inputName5" class="form-label">Color</label>
                  <select  class="form-control" name="color">
                      <option value="GREY">GREY | أزرق</option>
                      <option value="BAY">BAY | أحمر</option>
                      <option value="CHESTNUT">CHESTNUT | أشقر</option>
                      <option value="BLACK">BLACK | أسود</option>
                      
                  </select>
                </div>         
                    
           <div class="col-md-4">
                  <label for="inputName5" class="form-label">Date of birth</label>
                  <input type="date" required  class="form-control" name="dob">
                </div>   
                    
                    
                
                    
                    
                         <div class="col-md-4">
                  <label for="inputName5" class="form-label">Breeder</label>
                  <input type="text" class="form-control" name="breeder">
                </div>   
                  
                    
                         <div class="col-md-4">
                  <label for="inputName5" class="form-label">Owner</label>
                  <input type="text" class="form-control" name="owner">
                </div>   
           
                    
                       <div class="col-md-4">
                  <label for="inputName5" class="form-label">Country Of Origin</label>
                  <input type="text" class="form-control" name="country">
                </div>  
                    
                    
                        <div class="col-md-4">
                  <label for="inputName5" class="form-label">Passport document</label>
                  <input type="file"    class="form-control" name="passport_doc">
                </div>  
                    
                        <div class="col-md-4">
                  <label for="inputName5" class="form-label">Veterinary</label>
                  <input type="file"    class="form-control" name="vet_doc">
                </div>  
         
                    
                   
             
                    
                       <div class="col-md-12">
                  <label for="inputEmail5" class="form-label">About this horse</label>
                  <textarea   class="form-control"  name="about"></textarea>
                </div>
                    
                    
          <div class="col-12">
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked   name="redirect_flag">
                    <label class="form-check-label" for="gridCheck">
                      Take me to horse details page after creation
                    </label>
                  </div>
                </div>        
              
                    
                        
                </div></div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Add horse</button>
                    </div>
                  </div>
                          </form><!-- End Horizontal Form -->
                </div>
              </div><!-- End Extra Large Modal-->

                 <div class="modal fade" id="HorseLotsPreview"  >
                <div class="modal-dialog modal-xl">
             
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Horse participation</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id='dic-code'>
                    <!-- Horizontal Form -->
            
                    
               
                    
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  
                    </div>
                  </div>
                      
                </div>
              </div><!-- End Extra Large Modal-->
              
      </div>
    </section>

  </main><!-- End #main -->

<?php echo $__env->make('dashboard.shared.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('dashboard.shared.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>


 $( '#select-horse-stallion' ).select2({
     tags: true, 
  theme: 'bootstrap-5' ,
        dropdownParent: $("#ExtralargeModal") ,
  ajax: {
    url: function () {
      return server+'/operations/ajax/horses?gender=stallion'
    } ,
    dataType: 'json'
    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
  }
});
 $( '#select-horse-mare' ).select2({
        tags: true, 
  theme: 'bootstrap-5' ,
      dropdownParent: $("#ExtralargeModal") ,
  ajax: {
    url: function () {
      return server+'/operations/ajax/horses?gender=mare'
    } ,
    dataType: 'json'
    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
  }
});
 
 function loadHorseStats(id){
 
 
 
 $('#dic-code').html('<div><h3>Loading data</h3><p>Please wait..</p></div>');
 
 
 
 $('#HorseLotsPreview').modal('show');
 

            $.ajax({
            type:'GET',
                    url:"<?php echo e(url('operations/ajax/horses-details')); ?>/" + id,
                    success:function(data){
               
                    if ($.isEmptyObject(data.error)){
                    data = data.data;
                    let str = "<table class='table table-bordered bg-white'>";
                    str+= '<tr>\n\
                <th>Auction</th>\n\
                <th>Lot</th>\n\
                <th>Selling</th>\n\
                <th>Started</th>\n\
                <th>Finished</th>\n\
                <th>Bids</th>\n\
                <th>Max bid</th>\n\
                <th>Status</th>\n\
</tr>';
                    
          data.forEach((d)=>{
                 str+= '<tr>\n\
                <td>'+d.auction+'</td>\n\
                <td>'+d.lot+'</td>\n\
                <td>'+d.selling+'</td>\n\
                <td>'+d.started+'</td>\n\
                <td>'+d.finished+'</td>\n\
                <td>'+d.num_bids+'</td>\n\
                <td>'+d.max_bid+'</td>\n\
                <td>'+d.status+'</td>\n\
</tr>';  
          })  ;        
                    
                    
                    str+= '</table>';
                    
                    
                      $('#dic-code').html(str);
                    
                    } else{
                    console.log(data.error);
                    }
                    }
            });
         


         
    
    
    
    }
 
 

</script>
</body>

</html><?php /**PATH C:\wamp64\www\test\resources\views/dashboard/pages/horses.blade.php ENDPATH**/ ?>