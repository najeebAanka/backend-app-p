
<!DOCTYPE html>
<html lang="en">
<?php
$auction  = \App\Models\Auction::find(Route::input('auction_id'));

?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - {{$auction->name}}'s Hall bidders  </title>
 
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
      <h1>{{$auction->name}}'s Hall bidders </h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">{{$auction->name}}'s Hall bidders </li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
     </div>
    <section class="section dashboard">
        
           @include('dashboard.shared.messages')
        
      <div class="row">
            <div class="col-md-4">      <div class="card p-2">
             
                     <p class="p-1 font-bold "><b>Add new Hall bidder</b></p>  
                      <form method="post" action="{{url('operations/auctions/hall-bidders/add')}}">
                      {{csrf_field()}}
                      
                      <input value="{{$auction->id}}"  name="auction_id"  type="hidden" />
              <div class="row">
                  <div class="col-md-12 mb-2" ><label class="p-1">Name</label><input 
                          value=""
                          name="name" class="form-control" type="text" placeholder="Name" required/></div>
               
                  <div class="col-md-12 mb-2" ><label class="p-1">Email</label><input 
                          value=""
                          name="email" class="form-control" type="text" placeholder="Email"  /></div>
               
                  <div class="col-md-12 mb-2" ><label class="p-1">Phone</label><input 
                          value=""
                          name="phone" class="form-control" type="text" placeholder="Phone" required /></div>
               
                  <div class="col-md-12 mb-2" ><label class="p-1">Bidding number</label><input 
                          value=""
                          name="number" class="form-control" type="number" placeholder="Number" /></div>
                  <div class="col-md-12 mb-2" ><label class="p-1">Paid deposit</label><input 
                          value=""
                          name="deposit" class="form-control" type="text" placeholder="Deposit" /></div>
                  <div class="col-md-12 mb-2" ><label class="p-1">Country</label><input 
                          value=""
                          name="country" class="form-control" type="text" placeholder="Country" /></div>
               
                
                
                  
              </div> 
              <hr />
                    
                  <div class="text-right">
                      <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Add Hall bidder </button>  
              </div>
              </form>   
                  
          
          </div></div>
          <div class="col-md-8">  
                  <div class="card p-2 table-container">   
                      <div class="table-responsive">
              <table class="table table-bordered bg-white">
              
         
              <tr style="background-color: #fff5ee">
                 
                  <th>Name</th>
                   <th>Phone</th>
                    <th>Email</th>
                    <th>Number</th> 
                    <th>Paid</th> 
                    <th>Country</th> 
           
              </tr>    
         <?php
         
         
         $data = \App\Models\HallBidder::where('auction_id' ,$auction->id)->orderBy('id' ,'desc');
     
         $data = $data->paginate(20);
         foreach ( $data as $u){
             ?>
              
              
              
              <tr  >
                
                
            
                <td ><?=$u->name?></td>
                <td><?=$u->phone?></td>
                <td><?=$u->email?></td>
                <td><?=$u->bidding_number?></td>
                <td><?=$u->paid_deposit?></td>
                <td><?=$u->country?></td>
               
              </tr>     
              
              <?php
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