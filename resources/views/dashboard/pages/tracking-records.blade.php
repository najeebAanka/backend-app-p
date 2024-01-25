
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <title>Dashboard - Tracking records</title>
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
      
  Users activity log
      </h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">  Users activity log</li>
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
                  <th>ID</th>
                  <th>Activity</th>
                  <th>Details</th>
                  <th>Date</th>
                  <th>User</th>
        
                  
              </tr>    
         <?php
         
         $data = \App\Models\ActivityTracker::orderBy('id' ,'desc')->paginate(20);
         foreach (  $data as $d){
             $u=App\Models\User::find($d->target_id);
             ?>
               <tr class="horse-{{$d->seen}}">
                   <td>{{$d->id}}</td>
                    <td>{{$d->target_type}}</td>
                    <td>{{$d->contents}}</td>
                    <td>{{$d->created_at}}</td>
                <td><a href="{{url('users/'.$d->target_id)}}">
                     <?= $u?$u->name : "Deleted account"?></a></td>
              </tr>     
                
              
              <?php
               if($d->seen == -1){
              $d->seen=1;
              $d->save();
              }
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

</body>

</html>