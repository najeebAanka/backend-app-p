
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <title>Dashboard - Invoices</title>
<?php    $request = request(); ?> 
@include('dashboard.shared.css')
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
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
      
      <div class="bg-trans p-2" style="margin: 2px">
      
 
    <div class="pagetitle">
      <h1>
      
   Invoices history
      
      </h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">Invoices</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    
         </div>
    <section class="section dashboard m-3">
                @include('dashboard.shared.messages')
                
                
                
                
      <div class="row">
          
            <div class="card p-2 table-container">
                
                
                    <div class="card p-2">
              <p class="p-1 font-bold "><b>Search filter </b></p>  
              <form>
              <div class="row">
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-horse-head"></i> Invoice number</p><input 
                          
                          value="{{$request->has('no') && $request->no!="" ? $request->no : ""}}"
                          name="no" class="form-control" type="text" placeholder="Invoice number" /></div>
                          
           
                          
                          
                          
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-check"></i> Type</p>
                      
                      
                      <select class="form-control"  name="type" >
                       <option value="all">All</option>
                          <option value="lots-won" {{ $request->has('type') && $request->type=="lots-won" ? "selected" : "" }} >Lots won invoices</option>
                      </select>
                  
                  </div>
                  <div class="col-md-4"><p class="p-1"><i class="fa-solid fa-check"></i> Status</p>
                      
                      
                   <select class="form-control"  name="status" >
                       <option value="all">All</option>
                          <option value="paid" {{ $request->has('status') && $request->status=="paid" ? "selected" : "" }} >Paid invoices</option>
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
                  <th>#</th>
                  <th>Number</th>
                  <th>Issue date</th>
                  <th>Issued by</th>
                  <th>User</th>
                  <th>Type</th>
                  <th>Status</th>
        
                  
              </tr>    
         <?php
         
         $data = \App\Models\Invoice::orderBy('id' ,'desc');
      
//         if(!$currentUser->isAdmin()){
//          $data = $data->where('seller_id' ,$currentUser->id);   
//         }
//         
//         
//         $name_pref = "";
//        if($request->has('name') && $request->name!=""){
//           $data= $data ->where('name_en' ,'like' ,'%'. $request->name .'%') ; 
//            $name_pref = $request->name ;
//        }
//        
//        
//         if($request->has('seller') && $request->seller!=""){
//           $data= $data ->whereIn('seller_id'  , \App\Models\User::where('name' ,'like' ,'%'.$request->seller.'%')
//                   ->orWhere('email' ,'like' ,'%'.$request->seller.'%')->orWhere('phone' ,'like' ,'%'.$request->phone.'%')
//                   ->where('user_type' ,2)->pluck('id')->toArray()) ; 
//           
//        }
//        
//        if($request->has('status') && $request->status!="all"){
//           $data= $data ->where('status' ,$request->status) ; 
//        }
        $is_admin = Auth::user()->isAdmin();
        $data= $data ->paginate(20);
         foreach (  $data as $inv){
             ?>
               <tr class="inv-{{$inv->status}}">
                   <td>{{$inv->id}}</td>
                   <td><a target="blank" href="{{url('remote-operations/invoices/view/'.$inv->id)}}">
                    <?=$inv->gen_id?> </a></td>
                     <td>{{$inv->created_at}} </td>
                       <td>{{App\Models\User::find($inv->created_by)->name}} </td>
                   
                   <td><?php
                   if($inv->user_type == 'user') echo \App\Models\User::find($inv->user_id)->name;
                   else echo "Hall bidder No(" .$inv->user_id.")"; 
                   ?></td> 
                     <td>{{$inv->invoice_type}} </td>
                       <td>{{$inv->status}}</td>
                      
              </tr>     
                
              
              <?php
         }
         
         ?>     
              
          </table>
          
           <div class="d-flex">
                {!!  $data ->links() !!}
            </div>
          
            </div>
    

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')
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
 

</script>
</body>

</html>