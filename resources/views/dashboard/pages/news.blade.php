
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - News & Blogs</title>
 
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
    <button type="button" class="btn btn-primary" style="float: right" data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
             <i class="fa fa-plus"></i>     Create a new article
              </button>
    <div class="pagetitle">
        
        
      <h1>test News & Blogs</h1>
  
      <nav>
          
          

          
          
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">News & Blogs</li>
        </ol>
          
      </nav>
      
   
      
    </div><!-- End Page Title -->
        </div>
    <section class="section dashboard">
        
        @include('dashboard.shared.messages')
        
        
      <div class="row">

         <?php
         $data = \App\Models\Blog::orderBy('id' ,'desc');
         
          $data=   $data  ->paginate(20);
         foreach ( $data  as $u){
         
             ?>
          <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
              <a href="{{url('news/'.$u->id)}}" style="color: #000"> 
                  
                  <div class="card p-3 ">
                  
           
                <div><div style="    width: 100%;
  
    height: 12rem;
       background: url('<?=$u->buildPoster()?>');
    background-size: 100% auto;
    background-repeat: no-repeat;
    background-position: center;
 " ></div></div>
    <div style="padding-top: 1rem;font-weight: bold;min-height: 5rem;color: #905420"><?=$u->title?></div>
               
                  <p>  <span style="    font-size: 1.5rem;"> <?=$u->views_count?>
                     </span> <span class="sm-text"> Views </span></p>
              </div>
            
                    </a>    
                </div> 
              
              <?php
         }
         
         ?>     
            </div>     
<!--          </table>-->
            <div class="d-flex">
                {!!  $data ->links() !!}
            </div>
          
  
              <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <form method="post" id="identifier" action="{{url('operations/blogs/create')}}" enctype="multipart/form-data"
                          onsubmit="populateQuiil()">
                      {{csrf_field()}}
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Create a new article</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Horizontal Form -->
                    <div class="row">
                        <div class="col-md-12"> 
                            <div class="row row g-3">
             
        
                      <div class="col-md-6">
                  <label for="inputName5" class="form-label">title</label>
                  <input type="text" class="form-control" name="title">
                </div>
                                
                                    <div class="col-md-6">
                  <label for="inputZip" class="form-label">Image Upload</label>
                  <input type="file" class="form-control"   name="poster">
                </div>
                                
                      <div class="col-md-12">
                          <div>
                   <label for="inputZip" class="form-label">Contents</label>
                           <div id="editor" ><p>Blog goes here</p></div>
                           <textarea  id="hiddenArea"  style="display: none"  name="contents" ></textarea>   
                   </div>        
                </div>
              
                    
                  
                
                        
                </div>
                    </div>
                    
                        
                    </div>
          
                  
                    
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Publish article</button>
                    </div>
                  </div>
                          </form><!-- End Horizontal Form -->
                </div>
              </div><!-- End Extra Large Modal-->

   
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