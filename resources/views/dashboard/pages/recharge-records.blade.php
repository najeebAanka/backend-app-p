
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <title>Dashboard - Recharging records</title>
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
    tr.inv-paid {
    background-color: #e7ffe8;
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
      
   Wallet recharge transactions
      
      </h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">Wallet recharge transactions</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    
         </div>
    <section class="section dashboard m-3">
                @include('dashboard.shared.messages')
                
                
                
                
      <div class="row">
          
            <div class="card p-2 table-container">
                

                
                       <table class="table  table-bordered bg-white">
                           <tr>
                               
                               <td colspan="100%">
                                   <a class="btn btn-link m-2 btn-sm" href="?filter=refund-requested">Refund requested
                                   ({{\App\Models\WalletRechargeRecord::where('order_status' ,'refund-requested')->count()}})
                                   </a>  
                                   
                                   
                                     <a class="btn btn-link m-2 btn-sm" href="?filter=refunded">Refunded
                                   ({{\App\Models\WalletRechargeRecord::where('order_status' ,'refunded')->count()}})
                                   </a>  
                                   
                                     <a class="btn btn-link m-2 btn-sm" href="?filter=paid">Paid
                                   ({{\App\Models\WalletRechargeRecord::where('order_status' ,'paid')->count()}})
                                   </a> 
                                   
                                   
                                     <a class="btn btn-link m-2 btn-sm" href="?filter=created">Unpaid
                                   ({{\App\Models\WalletRechargeRecord::where('order_status' ,'created')->count()}})
                                   </a> 
                                   
                                     <a class="btn btn-link m-2 btn-sm" href="{{url('recharge-records')}}">All
                                   ({{\App\Models\WalletRechargeRecord::count()}})
                                   </a> 
                                   
                               </td>
                           </tr>
     
              <tr>
                  <th>#</th>
                  <th>Status</th>
                  <th>Issue date</th>
                  <th>Issued by</th>
                  <th>Wallet amount</th>
                  <th>Amount</th>
                  <th>Reference</th>
                  <th>Actions</th>
        
                  
              </tr>    
         <?php
         
         $data = \App\Models\WalletRechargeRecord::orderBy('id' ,'desc');
        $is_admin = Auth::user()->isAdmin();
       
        if($request->has('filter')){
            $data = $data->where('order_status' ,$request->filter);
        }
         $data= $data ->paginate(20);
         foreach (  $data as $inv){
             $u = App\Models\User::find($inv->user_id);
             ?>
               <tr class="inv-{{$inv->order_status}}">
                <td><a href="{{url('recharge-records/'.$inv->id)}}">{{$inv->id}}<a/></td>
                   <td>
                    <?=$inv->order_status?></td>
                     <td>{{$inv->created_at}} </td>
                       <td>{!!$u? "<a href='".url('users/'.$u->id)."'>".$u->name."</a>":"Deleted account"!!} </td>
                       <td>{!!$u? $u->wallet_amount." AED":"Deleted account"!!} </td>
                   
                   <td>{{$inv->amount}} AED</td> 
                   <td><?php 
                   
                   if($inv->order_response != ""){
                       $response = json_decode($inv->order_response);
                       ?>
                       <p><b>Payment type : </b> <?=$response->card_type_name?></p>
                       <p><b>Transaction ID : </b> <?=$response->transaction_id?></p>
                       <p><b>Card : </b> <?=$response->req_card_number?></p>
                       <p><b>Response : </b> <?=$response->message?></p>
                  <?php }
                   ?></td>
                   <td> 
                           <?php if($inv->order_status == 'paid'){ ?> 
                       <a href="{{url('recharge-recepits/view/'.$inv->id)}}">Show receipt</a><br />
                           <?php } ?>
                       <?php if($inv->order_status == 'refund-requested'){ ?> 
                       
                             <form method="post"  action="{{url('remote-operations/wallet-recharge-records/set-as-refunded/'.$inv->id)}}">
                           {{csrf_field()}}
                              
                                 <a  class="btn btn-link btn-sm"  href='{{url('remote-operations/wallet-recharge-records/generate-refund-form/'.$inv->id)}}'>Generate refund form</a>
                           <button class="btn btn-link btn-sm"  >Set as refunded</button>
                           
                              </form>
                       
                       
                   <?php } ?>
                   </td>
                      
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