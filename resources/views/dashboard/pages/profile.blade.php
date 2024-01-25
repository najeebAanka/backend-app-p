
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
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url("")}}">Home</a></li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    </div><!-- End Page Title -->

    <section class="section profile">
        
           @include('dashboard.shared.messages')
        
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="{{url('dashboard')}}/assets/img/vector-users-icon.jpg" alt="Profile" class="rounded-circle">
              <h2>{{$u->name}}</h2>
              <h3><?=$u->user_type == 0  ?  "Super Adminstrator" :  \App\Models\Horse::where('seller_id' ,$u->id)->count()." horses  , ".
        App\Models\LotWinningRecord::where('winner_id' ,$u->id)->count()." lots won." ?></h3>
         
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>


                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">About</h5>
                  <p class="small fst-italic">{{$u->about}}</p>

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8">{{$u->name}}</div>
                  </div>

                
 

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Country</div>
                    <div class="col-lg-9 col-md-8">{{$u->country}}</div>
                  </div>

              

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8">{{$u->phone}}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8">{{$u->email}}</div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="post" action="{{url('operations/profile/edit')}}">
                      {{csrf_field()}}
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="{{url('dashboard')}}/assets/img/vector-users-icon.jpg" alt="Profile">
                      
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                          <input name="name" type="text" required class="form-control" id="fullName" value="{{$u->name}}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px">{{$u->about}}</textarea>
                      </div>
                    </div>
 
             

                    <div class="row mb-3">
                      <label for="Country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="country" type="text" class="form-control"  id="Country" value="{{$u->country}}">
                      </div>
                    </div>

         
                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" required id="Phone" value="{{$u->phone}}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" required id="Email" value="{{$u->email}}">
                      </div>
                    </div>

                 

        

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>
 
                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
              <form method="post" action="{{url('operations/profile/edit')}}">
                      {{csrf_field()}}

            

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" required type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

               

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
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