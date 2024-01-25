
<!DOCTYPE html>
<html lang="en">
<?php $horse = App\Models\Horse::find(Route::input('id')); ?>;
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - {{$horse->name_en}} Pedigree</title>
 <?php    $request = request(); 
 $currentUser = Auth::user(); ?>
@include('dashboard.shared.css')
<style>

.pkj tr td{
    text-align: center;
  
        vertical-align: top;
      

 
}
.table-node {
 
     color: #fff;
    padding: 4px;
    /* border-radius: 10px; */
    margin: 0 auto;
    width: 200px;
    font-size: 26.2px;
    min-height: 4rem;
    /* line-height: 4rem; */
    padding-top: 0.7rem;
     
    
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


                  <div class="table-responsive" style=" padding: 2rem">
            <?php
          
             echo   $horse->generatePedegreeHTML();
            
            
            ?>      
              </div>         
              
         
   


@include('dashboard.shared.footer')
@include('dashboard.shared.js')

</body>

</html>