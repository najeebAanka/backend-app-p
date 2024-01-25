
<!DOCTYPE html>
<html lang="en">
<?php $horse = App\Models\Horse::find(Route::input('id')); ?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - {{$horse->name_en}} Pedigree</title>
 <?php    $request = request(); 
 $currentUser = Auth::user(); ?>
  <style>
      
      *, *:before, *:after {
  box-sizing: border-box;
}

body {
  min-width: 1200px;
  margin: 0;
  padding: 50px;
  color: #eee9dc;
  font: 16px Verdana, sans-serif;
background: rgb(149,81,30);
background: linear-gradient(36deg, rgba(149,81,30,1) 0%, rgba(106,50,8,1) 100%);
  user-select: none;
}

#wrapper {
  position: relative;
}

.branch {
  position: relative;
  margin-left: 250px;
}
.branch:before {
content: "";
    width: 10px;
    border-top: 2px solid #eee9dc;
    position: absolute;
    left: 79px;
    top: 50%;
    margin-top: 1px;
}

.entry {
  position: relative;
  min-height: 60px;
}
.entry:before {
  content: "";
  height: 100%;
  border-left: 2px solid #eee9dc;
  position: absolute;
  left: 87px;
}
.entry:after {
  content: "";
    width: 17px;
  border-top: 2px solid #eee9dc;
  position: absolute;
    left: 88px;
  top: 50%;
  margin-top: 1px;
}
.entry:first-child:before {
  width: 10px;
  height: 50%;
  top: 50%;
  margin-top: 2px;
  border-radius: 10px 0 0 0;
}
.entry:first-child:after {
  height: 10px;
  border-radius: 10px 0 0 0;
}
.entry:last-child:before {
  width: 10px;
  height: 50%;
  border-radius: 0 0 0 10px;
}
.entry:last-child:after {
  height: 10px;
  border-top: none;
  border-bottom: 2px solid #eee9dc;
  border-radius: 0 0 0 10px;
  margin-top: -9px;
}
.entry.sole:before {
  display: none;
}
.entry.sole:after {
  width: 50px;
  height: 0;
  margin-top: 1px;
  border-radius: 0;
}

.label {
      display: block;
      background-color: #fff;
      color: #000;
    min-width: 225px;
    padding: 5px 10px;
    line-height: 20px;
    text-align: center;
    border: 2px solid #eee9dc;
    border-radius: 5px;
    position: absolute;
left: 104px;
    top: 50%;
    margin-top: -15px;
}


      
  </style>
</head>

<body>

    <div>
        <h3>Pedigree of {{$horse->name_en}}</h3>
        <p><a href="{{url("horse-details/".$horse->id)}}" style="color: #fff">Back to horse details</a></p>
        
        
    </div>
    
<div id="wrapper"><span class="label">{{$horse->name_en}}</span>
  <div class="branch lv1">
      <?php $sire =$horse->getSire();
      if($sire){?>
      <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire->id)}}">{{$sire->name_en}}</a></span>
      <div class="branch lv2">
       <?php $sire_of_sire =$sire->getSire();
      if($sire_of_sire){?>
          <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_sire->id)}}">{{$sire_of_sire->name_en}}</a></span>
          <div class="branch lv3">
              
              
               <?php $sire_of_sire_of_sire =$sire_of_sire->getSire();
      if($sire_of_sire_of_sire){?> 
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_sire_of_sire->id)}}">
                        {{$sire_of_sire_of_sire->name_en}}</a></span>
             <div class="branch lv4">
                 <?php if($sire_of_sire_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_sire_of_sire->getSire()->id)}}">{{$sire_of_sire_of_sire->getSire()->name_en}}</a></span></div>
                 <?php } ?>
                 <?php if($sire_of_sire_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_sire_of_sire->getDam()->id)}}">
                            {{$sire_of_sire_of_sire->getDam()->name_en}}</a></span></div>
                  <?php } ?>
              </div>
            
            </div>
      <?php } ?>
              
               <?php $dam_of_sire_of_sire =$sire_of_sire->getDam();
      if($dam_of_sire_of_sire){?>   
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_sire_of_sire->id)}}">
                        {{$dam_of_sire_of_sire->name_en}}</a></span>
              <div class="branch lv4">
                      <?php if($dam_of_sire_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_sire_of_sire->getSire()->id)}}">
                             {{$dam_of_sire_of_sire->getSire()->name_en}}</a></span></div>
                      <?php } ?>
                       <?php if($dam_of_sire_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_sire_of_sire->getDam()->id)}}">
                            {{$dam_of_sire_of_sire->getDam()->name_en}}</a></span></div>
                 <?php } ?>
              </div>
            </div>
                <?php } ?> 
         
          </div>
        </div>
      <?php } ?>
          
          
  
          
            <?php $dam_of_sire =$sire->getDam();
      if($dam_of_sire){?>
          <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_sire->id)}}">{{$dam_of_sire->name_en}}</a></span>
          <div class="branch lv3">
              
              
               <?php $sire_of_dam_of_sire =$dam_of_sire->getSire();
      if($sire_of_dam_of_sire){?> 
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->id)}}">
                        {{$sire_of_dam_of_sire->name_en}}</a></span>
             <div class="branch lv4">
                 <?php if($sire_of_dam_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->getSire()->id)}}">{{$sire_of_dam_of_sire->getSire()->name_en}}</a></span></div>
                 <?php } ?>
                 <?php if($sire_of_dam_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->getDam()->id)}}">
                            {{$sire_of_dam_of_sire->getDam()->name_en}}</a></span></div>
                  <?php } ?>
              </div>
            
            </div>
      <?php } ?>
              
               <?php $dam_of_dam_of_sire =$dam_of_sire->getDam();
      if($dam_of_dam_of_sire){?>   
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->id)}}">
                        {{$dam_of_dam_of_sire->name_en}}</a></span>
              <div class="branch lv4">
                      <?php if($dam_of_dam_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->getSire()->id)}}">
                             {{$dam_of_dam_of_sire->getSire()->name_en}}</a></span></div>
                      <?php } ?>
                       <?php if($dam_of_dam_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->getDam()->id)}}">
                            {{$dam_of_dam_of_sire->getDam()->name_en}}</a></span></div>
                 <?php } ?>
              </div>
            </div>
                <?php } ?> 
         
          </div>
        </div>
      <?php } ?>
          
          
          
      </div>
    </div>
      <?php } ?>
    
      
      
           <?php $dam =$horse->getDam();
      if($dam){?>
      <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam->id)}}">{{$dam->name_en}}</a></span>
      <div class="branch lv2">
       <?php $sire_of_dam =$dam->getSire();
      if($sire_of_dam){?>
          <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam->id)}}">{{$sire_of_dam->name_en}}</a></span>
          <div class="branch lv3">
              
              
               <?php $sire_of_dam_of_sire =$sire_of_dam->getSire();
      if($sire_of_dam_of_sire){?> 
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->id)}}">
                        {{$sire_of_dam_of_sire->name_en}}</a></span>
             <div class="branch lv4">
                 <?php if($sire_of_dam_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->getSire()->id)}}">{{$sire_of_dam_of_sire->getSire()->name_en}}</a></span></div>
                 <?php } ?>
                 <?php if($sire_of_dam_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_sire->getDam()->id)}}">
                            {{$sire_of_dam_of_sire->getDam()->name_en}}</a></span></div>
                  <?php } ?>
              </div>
            
            </div>
      <?php } ?>
              
               <?php $dam_of_dam_of_sire =$sire_of_dam->getDam();
      if($dam_of_dam_of_sire){?>   
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->id)}}">
                        {{$dam_of_dam_of_sire->name_en}}</a></span>
              <div class="branch lv4">
                      <?php if($dam_of_dam_of_sire->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->getSire()->id)}}">
                             {{$dam_of_dam_of_sire->getSire()->name_en}}</a></span></div>
                      <?php } ?>
                       <?php if($dam_of_dam_of_sire->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_sire->getDam()->id)}}">
                            {{$dam_of_dam_of_sire->getDam()->name_en}}</a></span></div>
                 <?php } ?>
              </div>
            </div>
                <?php } ?> 
         
          </div>
        </div>
      <?php } ?>
          
          
 
          
            <?php $dam_of_dam =$dam->getDam();
      if($dam_of_dam){?>
          <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam->id)}}">{{$dam_of_dam->name_en}}</a></span>
          <div class="branch lv3">
              
              
               <?php $sire_of_dam_of_dam =$dam_of_dam->getSire();
      if($sire_of_dam_of_dam){?> 
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_dam->id)}}">
                        {{$sire_of_dam_of_dam->name_en}}</a></span>
             <div class="branch lv4">
                 <?php if($sire_of_dam_of_dam->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_dam->getSire()->id)}}">{{$sire_of_dam_of_dam->getSire()->name_en}}</a></span></div>
                 <?php } ?>
                 <?php if($sire_of_dam_of_dam->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$sire_of_dam_of_dam->getDam()->id)}}">
                            {{$sire_of_dam_of_dam->getDam()->name_en}}</a></span></div>
                  <?php } ?>
              </div>
            
            </div>
      <?php } ?>
              
               <?php $dam_of_dam_of_dam =$dam_of_dam->getDam();
      if($dam_of_dam_of_dam){?>   
            <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_dam->id)}}">
                        {{$dam_of_dam_of_dam->name_en}}</a></span>
              <div class="branch lv4">
                      <?php if($dam_of_dam_of_dam->getSire()){ ?>
                 <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_dam->getSire()->id)}}">
                             {{$dam_of_dam_of_dam->getSire()->name_en}}</a></span></div>
                      <?php } ?>
                       <?php if($dam_of_dam_of_dam->getDam()){ ?>
                <div class="entry"><span class="label"><a href="{{url('horse-pedigree/'.$dam_of_dam_of_dam->getDam()->id)}}">
                            {{$dam_of_dam_of_dam->getDam()->name_en}}</a></span></div>
                 <?php } ?>
              </div>
            </div>
                <?php } ?> 
         
          </div>
        </div>
      <?php } ?>
          
          
          
      </div>
    </div>
      <?php } ?>
    
  </div>
</div> 
    

</body>

</html>