
<!DOCTYPE html>
<html lang="en">
<?php $u = Auth::user(); 

if($u){

?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Wallet </title>
 
@include('dashboard.shared.css')
<style>
    img.ico-sm {
    width: 75px;
}
   span.xl-text {
    font-size: 4rem;
} 
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
  <div class="bg-trans p-2">
    <div class="pagetitle">
      <h1>Wallet</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{url('users')}}">{{$u->name}}</a></li>
          <li class="breadcrumb-item active">Wallet</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
         @include('dashboard.shared.messages')
      <div class="row">
          <div class="col-md-6">   
              
                    <div class="card p-2 table-container text-center"> 
                        <p class="text-center"><span class='xl-text'>{{number_format($u->wallet_amount ,2)}}</span><span  class='md-text'>AED</span></p>  
                        
                        <hr />
                   
                        
         </div></div>
             <div class="col-md-6">       <div class="card p-2 table-container text-center"> 
                     <h3 class="m-4">Charging transactions</h3>   
                 <hr />
                 
                 <table class="table table-bordered">
                     <tr>
                         <th>ID</th>
                         <th>Date</th>
                         <th>Amount</th>
                         <th>Status</th>
                         
                     </tr>
                     
                 </table>
                 
                 </div>
                 </div>
    
          

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>
<?php }else echo 'This account has been deleted';  ?>
</html>