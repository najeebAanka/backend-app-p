
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - FCM messages  </title>
 
@include('dashboard.shared.css')
<?php    $request = request(); ?> 
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
      <h1>FCM messages</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">FCM messages</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
        
          @include('dashboard.shared.messages')
        
      <div class="row">
            <div class="col-md-12">      <div class="card p-5">
              <p class="p-1 font-bold "><b>Firebase messages </b></p>  
              <form action="{{url('operations/fcm-messages/create')}}" method="post">
                  {{csrf_field()}}
              <div class="row">
                  <div class="col-md-12 p-2" ><label class="p-1"><i class="fa-solid fa-user"></i> Message text</label><textarea 
                          
                     
                          name="message" class="form-control"  placeholder="Messages goes here.." ></textarea></div>
               
              
                
                  
              </div> 
              <hr />
              <div class="text-right">
                  <button class="btn btn-success"><i class="fa fa-envelope"></i> Send </button>  
              </div>
              </form>
          </div></div>
      
          
      

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>

</html>