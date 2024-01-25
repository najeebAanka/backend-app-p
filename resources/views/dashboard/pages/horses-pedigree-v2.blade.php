
<!DOCTYPE html>
<html lang="en">
<?php $horse = App\Models\Horse::find(Route::input('id')); ?>
<head>
  <meta charset="utf-8">


  <title>Dashboard - {{$horse->name_en}} Pedigree</title>
 <?php    $request = request(); 
 $currentUser = Auth::user(); ?>
 

</head>

<body>


                  <div   style=" padding: 2rem;text-align: center;overflow: auto">
                      <div style="width: 1440px" id="element"></div>    
              </div>         
              
         
   



</body>

</html>