
<!DOCTYPE html>
<html lang="en">
<?php
$blog = \App\Models\Blog::find(Route::input('id'));
$u = $blog;
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - <?=$blog->title?></title>
 
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
    <!-- Extra Large Modal -->
     <div class="bg-trans p-2">
    <div class="pagetitle">
        
        
      <h1><?=$blog->title?></h1>
    
      <nav>
          
          

          
          
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{url('news')}}">News & blogs</a></li>
          <li class="breadcrumb-item active"><?=$blog->title?></li>
        </ol>
          
      </nav>
      
   
      
    </div><!-- End Page Title -->
     </div>
    <section class="section dashboard">
        
        @include('dashboard.shared.messages')
        
        
      <div class="row">
<!--          <table class="table table-bordered bg-white">
              <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                    <th>Total lots</th>
                    <th>Live Lots</th>
                    <th>Finished</th>
                    <th>Total bidders</th>
                     <th>Published</th>
                  <th></th>
        
                  
              </tr>    -->
         <?php
      {
         
             ?>
          <div class="col-xl-12 ">
             <form method="post" id="identifier" action="{{url('operations/blogs/edit')}}" enctype="multipart/form-data"
                          onsubmit="populateQuiil()">
                      {{csrf_field()}}
                      <input type="hidden" name="id" value="<?=$u->id?>" />
                 <div >
                   
                    <div class="card p-2">
                    <!-- Horizontal Form -->
                    <div class="row">
                        <div class="col-md-12"> 
                            <div class="row row g-3">
             
        
                      <div class="col-md-12">
                  <label for="inputName5" class="form-label">Article title</label>
                  <input type="text" class="form-control" value="<?=$u->title?>" name="title">
                </div>
                                
                                    <div class="col-md-12">
                  <label for="inputZip" class="form-label">Image Upload</label>
                  
                      
                <div><div style="    width: 200px;
  
    height: 12rem;
       background: url('<?=$u->buildPoster()?>');
    background-size: 100% auto;
    background-repeat: no-repeat;
    background-position: center;
 " ></div></div>
                  
                  <input type="file" class="form-control"   name="poster">
                </div>
                                
                      <div class="col-md-12">
                          <div>
                   <label for="inputZip" class="form-label">Contents</label>
                           <div id="editor" >{!!$u->details!!}</div>
                           <textarea  id="hiddenArea"  style="display: none"  name="contents" ></textarea>   
                   </div>        
                </div>
              
                    
                  
                
                        
                </div>
                    </div>
                    
                        
                    </div>
          
                  
                    <hr />
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn m-1 btn-primary">Save changes</button>
                    </div>
                  </div>
                          </form><!-- End Horizontal Form --> 
                </div> 
              
              <?php
         }
         
         ?>     
            </div>     

  
       
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>

 var quill = new Quill('#editor', {
    theme: 'snow'
  });
  
 function populateQuiil() {
     
$("#hiddenArea").val($("#editor").find('.ql-editor').html());
  return true;
}


</script>
</body>

</html>