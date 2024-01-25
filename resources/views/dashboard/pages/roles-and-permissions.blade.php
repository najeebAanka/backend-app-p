
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Roles and permissions</title>
 
@include('dashboard.shared.css')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    
    span.sm-text {
    font-size: 11px;
}
    
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
        <div class="bg-trans p-2">
    <!-- Extra Large Modal -->
 
    <div class="pagetitle">
        
        
      <h1>test  Roles and permissions</h1>
  
      <nav>
          
          

          
          
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active"> Roles and permissions</li>
        </ol>
          
      </nav>
      
   
      
    </div><!-- End Page Title -->
        </div>
    <section class="section dashboard">
        
        @include('dashboard.shared.messages')
        <?php
        
        $roles = DB::select("select * from roles");
        $perms = DB::select("select * from permissions");
        ?>
        
      <div class="row">
          <div class="col-12">
              <table class="table table-bordered bg-white">
                  <tr>
                      <th></th>
                      
                      <?php foreach($roles as $role){ ?>

                      <th>{{$role->name}}</th>
                      <?php } ?>                      
                      
                  </tr>   
                  <?php foreach ($perms as $p){ ?>
                  <tr>
                      <td>{{$p->name}}</td>
                  
                      <?php foreach($roles as $role){ ?>

                      <td><input onchange="changePRbinding({{$role->id}},{{$p->id}} ,this)" <?=DB::select("select count(*) as c from role_has_permissions where role_id=? "
                              . "and permission_id=?" ,[$role->id ,$p->id])[0]->c>0 ? "checked" : ""?> type="checkbox" /></td>
                      <?php } ?>  
                  </tr>
                  <?php } ?>
                  
              </table>     
              
              
          </div>
        
            </div>     


    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')
<script>


function changePRbinding(role ,perm ,c){
   c.disabled =true;
   
   
       $.ajax({
            type:'POST',
                    url:"{{url('operations/roles-and-permission/change-single')}}",
                    data:{"role_id" :  role, "perm_id" :perm, "status" : c.checked ? 1 : 0 },
                    success:function(data){
 c.disabled =false;
                    }
            });
   
   
    
}


</script>
 
</body>

</html>