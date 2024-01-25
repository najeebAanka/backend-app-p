
<!DOCTYPE html>
<html lang="en">
<?php $horse = App\Models\Horse::find(Route::input('id'));
if($horse->seller_id == Auth::id() || Auth::user() ->can('accept-reject-horses')){

?>;
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - {{$horse->name_en}}</title>
 <?php    $request = request(); 
 $currentUser = Auth::user(); ?>
@include('dashboard.shared.css')
<style>
    .plus-button {

        background-color: #e5e5e5;  
    text-align: center;
    width: 100px;
    cursor: pointer;
    display: inline-table;
        min-height: 7rem;
    margin: 1rem;
    text-align: center;
position: relative;
    border-radius: 5px;
    line-height: 7rem;
    
}
.plus-button-hover{
     background-color: #e5e5e5;  
}
.plus-button-hover:hover{
    background-color: #51a1ea;
    color: #fff;
}
.plus-button img{
      width: 100%;
    border-radius: 5px;
    max-width: 90px;
}
.plus-button input[type="checkbox"]{
position: absolute;
    top: 8px;
    right: 8px;
    transform: scale(1.5);
}
.tree {
    width: 100%;
}
.tree tr td{
    text-align: center;
    padding: 10px;
}
.tree tr td p{
       font-size: 11px;
    color: gray;
}
.pid-cell{
  
       width: 200px;
    height: 75px;
    font-size: 11px;
    text-align: center;
    /* padding: 7px; */
    border-radius: 50%;
    border: solid 1px #795548;
        color: green;
    font-weight: bold;
}

.pkj tr td{
    text-align: center;
    border : solid
        1px #ccc;
        vertical-align: top;
      

 
}
.table-node {
 
    color: #fff;
    padding: 4px;
    border-radius: 10px;
 
    margin: 0 auto;
        width: 200px;
      
        font-size: 11px;
     
    
}
.node-mare{
    background-color: #b93939 
}
.node-stallion{
    background-color: #3989b9;  
}
.diag-right  {
    width: 50%;
  background-image: linear-gradient(
    to top right,
    transparent calc(50% - 1px),
    black,
    transparent calc(50% + 1px)
  );
  height: 200px;
}


.diag-left {

}
.diag-left  {
       width: 50%;
  background-image: linear-gradient(
    to top left,
    transparent calc(50% - 1px),
    black,
    transparent calc(50% + 1px)
  );
  height: 200px;
}

    
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
  <div class="bg-trans p-2">
      <span style="float: right;
    background-color: #fff;
    padding: 1rem;">Status : <b>{{strtoupper($horse->status)}}</b> | <a href="{{url('horse-timeline/'.$horse->id)}}">Check timeline</a> 
          |  <a href="{{url('horse-pedigree/'.$horse->id)}}">Check full pedigree</a></span>
      
    <div class="pagetitle">
      <h1>{{$horse->name_en}}</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <?php if($request->has('redirected') && $request->redirected == 'auction'){
              $a = App\Models\Auction::find($request->auction_id);
              ?>
           <li class="breadcrumb-item"><a href="{{url('auctions/'.$request->auction_id)}}">{{$a->name}}</a></li>
          <?php
              
          }else{ ?>
           <li class="breadcrumb-item"><a href="{{url('horses')}}">Horses</a></li>
          <?php } ?>
          <li class="breadcrumb-item active">{{$horse->name_en}}</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
                @include('dashboard.shared.messages')
      <div class="row">
          
  
          
          <div class="col-md-6">
              
              <div class="card  p-2" >
                    <form method="post" action="{{url('operations/horses/update')}}" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                  <div >
                  
                    <div >
                    <!-- Horizontal Form -->
            
                <div class="row row g-3">
             
          <div class="col-md-6">
                  <label for="inputName5" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" value="{{$horse->name_en}}">
                </div>
                      <div class="col-md-6">
                  <label for="inputName5" class="form-label">Horse registration number</label>
                  <input type="text" class="form-control" name="reg_no" value="{{$horse->reg_no}}">
                </div>
                    
                    
                  
                         
                  <div class="col-md-6">
                  <label for="inputName5" class="form-label">Gender</label>
                  <select  class="form-control" name="gender">
                      <option value="stallion"  {{strtolower($horse->gender) == 'stallion' ? 'selected' : ''}}>Stallion</option>
                      <option value="mare" {{strtolower($horse->gender) == 'mare' ? 'selected' : ''}}>Mare</option>
                      
                  </select>
                </div>    
                    
               <div class="col-md-6">
                  <label for="inputName5" class="form-label">Color</label>
                  <input type="text" class="form-control" name="color"   value="{{$horse->color}}">
                </div>         
                    
           <div class="col-md-6">
                  <label for="inputName5" class="form-label">Date of birth</label>
                  <input type="date" class="form-control" name="dob"  value="<?= Carbon\Carbon::parse($horse->dob)->format('Y-m-d')?>">
                </div>   
                    
               
                    
                    
         
                    
                        <div class="col-md-6">
                  <label for="inputName5" class="form-label">Breeder</label>
                  <input type="text" class="form-control" name="breeder"  value="{{$horse->breeder_name}}">
                </div>   
                    
                         <div class="col-md-6">
                  <label for="inputName5" class="form-label">Owner</label>
                  <input type="text" class="form-control" name="owner" value="{{$horse->owner_name}}">
                </div>   
           
                    
                       <div class="col-md-6">
                  <label for="inputName5" class="form-label">Origin</label>
                  <input type="text" class="form-control" name="country" value="{{$horse->origin}}">
                </div>  
                    
               
                        <div class="col-md-6">
                  <label for="inputName5" class="form-label">Passport document</label>
                  <input type="file" class="form-control" name="passport_doc">
                   @if($horse->passport_doc != "")
                  <a href="{{url('storage/'.$horse->passport_doc)}}" >View uploaded passport</a>
                    @endif
                </div>  
                    
                        <div class="col-md-6">
                  <label for="inputName5" class="form-label">Veterinary</label>
                  <input type="file" class="form-control" name="vet_doc">
                  @if($horse->veterinary != "")
                     <a href="{{url('storage/'.$horse->veterinary)}}" >View uploaded document</a>
                     @endif
                </div>  
                    
                  
                   
             
                    
                       <div class="col-md-12">
                  <label for="inputEmail5" class="form-label">About this horse</label>
                  <textarea  rows="5"  class="form-control"  name="about">{{$horse->about_horse}}</textarea>
                </div>
                    
                    
              
              
                    
                        
                </div>
                    
                    
                    </div>
                    <div class="modal-footer mt-2">
                        
              
                      <button type="submit" class="btn btn-primary m-1">Save changes</button>
                    </div>
                  </div>
                          </form><!-- End Horizontal Form -->
                </div>
              
                  
          </div> 
         <div class="col-md-6">
        
                
                  <?php if( $currentUser ->can('accept-reject-horses')) {  ?>  
                        <div class="card bg-white p-2" >   
                            <p style="color: brown;font-weight: bold"><i class="fa fa-shield"></i> Admin operations</p>
                       <hr />
                    
                       
                       @if($horse->status != 'accepted')
<!--                    <form method="post" action="{{url('operations/horses/update')}}" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                      <input type="hidden" name='status' value="accepted" />
                        <div >
                  
                    <div >
              
                          <button type="submit" class="btn btn-success m-1 btn-block"><i class="fa fa-check"></i> Set as accepted</button>
                    </div>
                   
                  </div>
                          </form> End Horizontal Form -->
                          @endif
                                 @if($horse->status != 'rejected')
                            <form method="post" action="{{url('operations/horses/update')}}" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                      <input type="hidden" name='status' value="rejected" />
                        <div >
                  
                    <div >
              
                        <button type="submit" class="btn btn-danger m-1 btn-block"><i class="fa fa-multiply"></i>  Set as rejected</button>
                    </div>
                   
                  </div>
                          </form> 
                              @endif
                              
                              
                              
                              
                              <?php   if(App\Models\LotWinningRecord::where('selling_type' ,'horse')
                                      ->where('horse_id' ,$horse->id)->count() > 0  && $horse->status != 'shipped'){  ?>
                        <hr />
                              <h4>Shipping status</h4>
                              <p>Define the shipping status of this horse</p>
                    <form method="post" action="{{url('operations/horses/update')}}" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                      <input type="hidden" name='status' value="shipped" />
                        <div >
                  
                    <div >
                        
                        <textarea class="form-control" name="notes" style="background-color: #f5f5f5" placeholder="Add shipping notes"></textarea>
                        
              
                          <button type="submit" class="btn btn-primary mt-2 btn-block"><i class="fa fa-truck"></i> Set as shipped</button>
                    </div>
                   
                  </div>
                          </form><!-- End Horizontal Form -->
                  <?php  } ?>
                              
                              
                              
                              
                              
                     </div>       
                     <?php } ?>
              
             
             
                <div class="card  p-2" >
                  
                      <p>Gallery</p>
                      <form method="post" id="delete-form" action="{{url('operations/horses-gallery/delete')}}">   
                        {{csrf_field()}}
                        <div >
                         <?php foreach (\App\Models\HorseMultimedia::where('horse_id' ,$horse->id)->get() as $m){ ?>
                     
                   <div class="plus-button" >
                       <img src="{{url('storage/horses-gallery/'.$m->media_link)}}" /><br />
                       <input type="checkbox" name='ids[]' value="{{$m->id}}" />
                   </div>
                      
                   
             
                  
                  <?php } ?>
                       
                                           
                  <div class="plus-button plus-button-hover" onclick="$('#upload-imgs').click()">
                      <i  class="fa fa-plus"></i></div>
                  </div>
                          </form>  
                  
                      
                         
                  <form method="post" action="{{url('operations/horses/update')}}" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                
                 
                  
                  
                  <input id="upload-imgs" onchange="$('#status-b700').html('Images selected. Click (save changes) to upload them.')" style="display: none" name="bulk-images[]" type="file" multiple accept="image/png, image/gif, image/jpeg" />
                  
             
                  
         
                  
                  
                  <p id="status-b700" style="    padding: 1rem;
    color: green;"></p>
                  
                  
                  <hr />
                     <button type="submit" class="btn btn-success m-1">Save changes</button>
                     <button type="button" class="btn btn-danger m-1" onclick="$('#delete-form').submit()">Delete selected</button>
                  </form>
                
                </div>      
             
             
                    <div class="card  p-2" >
                  
                      <p>Performance records</p>
               
                      <table class="table table-bordered " >
                          
                          
                          <tr>
                              <th>Horse</th>
                              <th>Competition</th>
                              <th>Relation</th>
                              <th>Rank</th>
                              <th></th>
                              
                          </tr>
                         <?php
                         
                         $records = \App\Models\PerformanceTree::where('horse_id' ,$horse->id)->get();
                         if(count(   $records) == 0) {
                             ?>
                          <tr>
                              
                              <td colspan="100%">
                                  <p>No records added yet.</p>
                              </td>
                          </tr>
                          
                          <?php
                             
                         }
                         foreach (    $records as $m){ ?>
                     
                            <tr>
                                <td>{{$m->horse_name}}</td>
                                <td>{{$m->comp_name}}</td>
                                <td>{{$m->relation_name}}</td>
                                <td>{{$m->rank_name}}</td>
                                <td>{{$m->rank_name}}</td>
                                <td>
                                    
                                    
                                     <form method="post" id="delete-form" action="{{url('operations/horses/performance/delete')}}">   
                        {{csrf_field()}}
                           <input type="hidden" name='id' value="{{$m->id}}" />
                           <button type="submit" class="btn btn-danger btn-sm m-1"><i class="fa fa-multiply"></i> Remove</button>
                                     </form>
                                    
                                </td>
                                
                            </tr>
             
                  
                  <?php } ?>
                       
                                           
                            <tr>
                                <td colspan="100%">
                                    
                                    <p style="color: #51a1ea"><i class="fa fa-trophy"></i> Add new record</p>       
                                <form method="post" action="{{url('operations/horses/performance/add')}}" >
                      {{csrf_field()}}
                      <input type="hidden" name='horse_id' value="{{$horse->id}}" />
                    
                      
                          <div class="row row g-3">
                  <div class="col-md-4">
                  <label for="inputName5" class="form-label">Relation</label>
                  <select  class="form-control" name="relation">
                      <option value="Self">Self</option>
                      <option value="Dam">Dam</option>
                      <option value="Sire">Sire</option>
                      <option value="Dam of dam">Dam of dam</option>
                      <option value="Dam of sire">Dam of sire</option>
                       <option value="Sire of dam">Sire of dam</option>
                      <option value="Sire of sire">Sire of sire</option>
                      
                  </select>
                </div>
      
          <div class="col-md-4">
                  <label for="inputName5" class="form-label">Competition</label>
                  <input type="text" class="form-control" name="comp" >
                </div>
                 
                              
                         <div class="col-md-4">
                  <label for="inputName5" class="form-label">Rank</label>
                  <select  class="form-control" name="rank">
                    <option value="Gold">Gold champion</option>   
                    <option value="Silver">Silver champion</option>   
                    <option value="Bronze">Bronze champion</option>  
                    
                    <option value="First place">First place</option>      
                    <option value="Second place">Second place</option>      
                    <option value="Third place">Third place</option>      
                    <option value="Fourth place">Fourth place</option>      
                    <option value="Fifth place">Fifth place</option>      
                    <option value="Sixth place">Sixth place</option>      
                    <option value="Seventh place">Seventh place</option>      
                    
                      
                  </select>
                </div>        
                              
                          </div>
                      
                              <hr />
                     <button type="submit" class="btn btn-success m-1">Add to list</button
                                </form>
                             <!--<--> 
                            
                                    
                                </td>   
                                
                            </tr>
                  </table>
                        
                  
                      
       
                
                </div>      
             
             
              <div class="card  p-2" >
                  
             
                  <p>Seller information</p>
                       <?php $seller = \App\Models\User::find($horse->seller_id);
                       if($seller){
                       ?>
                       <table class="table table-bordered">
                           <tr>
                               <th>Seller name</th>
                               <td><a style="font-weight: bold" target="blank" href="{{url('users/'.$seller->id)}}"><?=$seller->name?></a></td>
                               
                           </tr>     
                            <tr>
                               <th>Join date</th>
                               <td>{{$seller->created_at}}</td>
                               
                           </tr> 
                           
                          
                             <tr>
                               <th>Email </th>
                               <td>{{$seller->email}}</td>
                               
                           </tr> 
                           
                    
                           
                           
                       </table> 
                       <?php } ?>
                       
                  
                        <p>Horse statistics</p>
                       <?php {
                       ?>
                       <table class="table table-bordered">
                           <tr>
                               <th>Auctions joined</th>
                               <td><?= App\Models\AuctionHorseReg::where('horse_id' ,$horse->id)->count()?></td>
                               
                           </tr>     
                       
                        <tr>
                               <th>Lowest Bid Achieved</th>
                               <td><?=
                               DB::select('SELECT min(bids.curr_amount) as v from bids join auction_horse_regs on auction_horse_regs.id = bids.lot_id WHERE auction_horse_regs.horse_id=?'
                                        ,[$horse->id])[0]->v;
                               ?></td>
                               
                           </tr>     
                        
                          <tr>
                               <th>Highest Bid Achieved</th>
                               <td><?=
                               DB::select('SELECT max(bids.curr_amount) as v from bids join auction_horse_regs on auction_horse_regs.id = bids.lot_id WHERE auction_horse_regs.horse_id=?'
                                        ,[$horse->id])[0]->v;
                               ?></td>
                               
                           </tr> 
                           
                           
                          
                           
                            
                           
                       </table> 
                       <?php } ?>
                       
                  
                
                </div>
             
             
          
              
          </div> 
        
      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>

</html>
<?php }else echo "Not allowed to access this page"; ?>