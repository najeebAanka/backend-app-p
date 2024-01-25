
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Static content </title>
 
@include('dashboard.shared.css')
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
      <h1>Bidders</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{url('users')}}">Static content</a></li>
         
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
          @include('dashboard.shared.messages')
      <div class="row">
          <div class="col-md-4">   
              
                    <div class="card p-2 table-container"> 
              
                        <h5>Current keys</h5>
                        <hr />
                        
                        
                        <ul class="list-group">
                      @foreach (App\Models\StaticContent::orderBy('order_sn')->get() as $item)
                      <li class="list-group-item"><a href="{{url('static-contents?target='.$item->static_key)}}">{{$item->title}}</a></li>
                      @endforeach
                            
                            
                        </ul>
                    
                    </div>
          
          </div>
          <div class="col-md-8">
               <div class="card p-2 table-container"> 
              <?php if(!isset($_GET['target'])){ ?>
                   <h4 class="mt-4 text-center">Select a key from the list on the left</h4>
              <?php }else{
                  
                  $item = App\Models\StaticContent::where('static_key' ,$_GET['target'])->first();
                  ?>     
                        
                 <h4>{{$item->title}}</h4>  
                 
                 
                 
                   <form method="post" action="{{url('operations/static-content/edit')}}" onsubmit="populateQuiil()"
                         >
                      {{csrf_field()}}
                  
              <input type="hidden" name="key" value="{{$item->static_key}}" />
              <input type="text" class="form-control mt-2 mb-2" name="title" value="{{$item->title}}" />
                 
                   <div id="editor" style="       min-height: 25rem;">{!!$item->static_content!!}</div>
                          
                           <textarea  id="hiddenArea"  style="display: none"  name="contents" >{!!$item->static_content!!}</textarea>   
                 
                 
                           <hr />
                           <button class="btn btn-primary" type="submit">Save changes</button>
                           
                   </form>  
                 
                   
              <?php } ?>
                    
                    </div>   
          
              
          </div>
  

      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')


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