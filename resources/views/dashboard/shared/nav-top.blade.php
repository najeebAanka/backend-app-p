  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
<?php $user = Auth::user(); ?>
    <div class="d-flex align-items-center justify-content-between">
      <a href="{{url("")}}" class="logo d-flex align-items-center">
        <img src="<?=url('')?>/dist/assets/img/logo.png" alt="">
       
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        
    
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

       {!!config('app.debug') ? " <li>    Test mode | </li>" : ""!!}
        <?php
        
        $notifications = [];
        
      if(Auth::user()->hasRole('Admin')){  
       $horse_requests =  App\Models\Horse::where('status' ,'pending')->count();
       if( $horse_requests > 0){
           $notifications[]= [
               "title" => "New horses" ,
                   "time"=>"Now" ,
               "subtitle"=>"You have ".$horse_requests." horse registration requests waiting for your review."
                ,"link"=>"horses"];  
       
       }
       
       
          $lot_requests = App\Models\HorseRegRequest::where('status' ,'pending')->count();
       if( $lot_requests > 0){
           $notifications[]= [
               "title" => "New join requests" ,
                   "time"=>"Now" ,
               "subtitle"=>"You have ".$lot_requests." lot registration requests waiting for your review."
                ,"link"=>"auctions"];  
       
       }
       
          $new_users =  App\Models\User::where('seen' ,-1)->where('user_type' ,'<>' ,0)->count();
       if( $new_users > 0){
           $notifications[]= [
               "title" => "New users joined !" ,
                   "time"=>"Now" ,
               "subtitle"=>"You have ".$new_users." new users joined test , check thier accounts now !."
                ,"link"=>"users"];  
       
       }   
      }
        
        
        ?>
        <li class="nav-item"  style="    margin-right: 2rem;
            color: #885e4f;
    font-weight: bold;"><i class="fa fa-clock"></i>  <span id="nav-clock"></span></li>
        <li class="nav-item dropdown">
@if(count($notifications)>0 )
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">{{count($notifications)}}</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have {{count($notifications)}} new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
@foreach($notifications as $n)
<?php $n = (object)$n; 

?>
            <li class="notification-item">
                
              <i class="bi bi-exclamation-circle text-warning"></i>
              <a href="{{url($n->link)}}">
              <div>
                <h4>{{$n->title}}</h4>
                <p>{{$n->subtitle}}</p>
                <p>{{$n->time}}</p>
              </div>
              </a>
            </li>
              <li>
              <hr class="dropdown-divider">
            </li>

@endforeach
          


          
            

         
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->
@endif
        </li><!-- End Notification Nav -->
 

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?=url('')?>/dashboard/assets/img/vector-users-icon.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">{{$user->name}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{$user->name}}</h6>
              <span>Adminstartor</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('profile')}}">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('profile')}}?view=settings">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('admin-auth/logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->
  
  


                                     
                                   
