
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Banners</title>
 
@include('dashboard.shared.css')
<?php    $request = request(); ?> 
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
      <h1>Banner Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <li class="breadcrumb-item active">Banner Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
        
          @include('dashboard.shared.messages')
        
      <div class="row">
            <div class="col-md-6 col-12">
              <div class="card p-5">
              <p class="p-1 font-bold "><b>Main Banner Management</b></p>  
              <form action="{{url('operations/banners')}}" method="post" enctype="multipart/form-data" id="form">
                {{csrf_field()}}
                <input type="hidden" name="place" value="main">
                <div class="row">
                    <div class="col-md-12 p-2" >
                      <label class="p-1">Target Type</label>
                      <select name="target_type" class="form-control" id="target-type">
                        <option value="">--</option>
                        <option value="outer_link" {{ $main_banner->target_type == 'outer_link' ? 'selected' : '' }}>Outer Link</option>
                        <option value="auction" {{ $main_banner->target_type == 'auction' ? 'selected' : '' }}>Auction</option>
                        <option value="article" {{ $main_banner->target_type == 'article' ? 'selected' : '' }}>Article</option>
                      </select>
                    </div>

                    <div class="col-md-12 p-2 d-none" id="target-link">
                      <label class="p-1">Target Link</label>
                      <input type="text" name="target_link" class="form-control" value="{{ $main_banner->target_type == 'outer_link' ? $main_banner->target : '' }}">
                    </div>
                    <div class="col-md-12 p-2 d-none" id="auctions">
                      <label class="p-1">Auctions</label>
                      <select name="target_auction" class="form-control">
                        <option value="">--</option>
                        @foreach ($auctions as $a)
                            <option value="{{ $a->id }}" {{ $main_banner->target_type == 'auction' && $main_banner->target == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-12 p-2 d-none" id="articles">
                      <label class="p-1">Articles</label>
                      <select name="target_article" class="form-control">
                        <option value="">--</option>
                        @foreach ($articles as $a)
                            <option value="{{ $a->id }}" {{ $main_banner->target_type == 'article' && $main_banner->target == $a->id ? 'selected' : '' }}>{{ $a->title }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-12 p-2 text-center">
                      <img src="{{ $main_banner->image }}" class="img-fluid">
                    </div>

                    <div class="col-md-12 p-2">
                      <label class="p-1">Banner</label>
                      <input type="file" class="form-control" name="image">
                    </div>
                </div> 
                <hr />
                <div class="text-right">
                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>  
                </div>
              </form>
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="card p-5">
                <p class="p-1 font-bold "><b>No Auctions Banner Management</b></p>  
                <form action="{{url('operations/banners')}}" method="post" enctype="multipart/form-data" id="form-no-auctions">
                  {{csrf_field()}}
                  <input type="hidden" name="place" value="no_auctions">
                  <div class="row">
                      <div class="col-md-12 p-2" >
                        <label class="p-1">Target Type</label>
                        <select name="target_type" class="form-control" id="target-type-no-auctions">
                          <option value="">--</option>
                          <option value="outer_link" {{ $no_auctions_banner->target_type == 'outer_link' ? 'selected' : '' }}>Outer Link</option>
                          <option value="auction" {{ $no_auctions_banner->target_type == 'auction' ? 'selected' : '' }}>Auction</option>
                          <option value="article" {{ $no_auctions_banner->target_type == 'article' ? 'selected' : '' }}>Article</option>
                        </select>
                      </div>

                      <div class="col-md-12 p-2 d-none" id="target-link-no-auctions">
                        <label class="p-1">Target Link</label>
                        <input type="text" name="target_link" class="form-control" value="{{ $no_auctions_banner->target_type == 'outer_link' ? $no_auctions_banner->target : '' }}">
                      </div>
                      <div class="col-md-12 p-2 d-none" id="auctions-no-auctions">
                        <label class="p-1">Auctions</label>
                        <select name="target_auction" class="form-control">
                          <option value="">--</option>
                          @foreach ($auctions as $a)
                              <option value="{{ $a->id }}" {{ $no_auctions_banner->target_type == 'auction' && $no_auctions_banner->target == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-12 p-2 d-none" id="articles-no-auctions">
                        <label class="p-1">Articles</label>
                        <select name="target_article" class="form-control">
                          <option value="">--</option>
                          @foreach ($articles as $a)
                              <option value="{{ $a->id }}" {{ $no_auctions_banner->target_type == 'article' && $no_auctions_banner->target == $a->id ? 'selected' : '' }}>{{ $a->title }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-12 p-2 text-center">
                        <img src="{{ $no_auctions_banner->image }}" class="img-fluid">
                      </div>

                      <div class="col-md-12 p-2">
                        <label class="p-1">Banner</label>
                        <input type="file" class="form-control" name="image">
                      </div>
                  </div> 
                  <hr />
                  <div class="text-right">
                      <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>  
                  </div>
                </form>
              </div>
            </div>
      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')

<script>
  var targetType = document.getElementById('target-type');
  targetType.addEventListener('change', function(e){
    var val = e.target.value;

    document.getElementById('target-link').classList.add('d-none');
    document.getElementById('auctions').classList.add('d-none');
    document.getElementById('articles').classList.add('d-none');

    if(val == 'outer_link')
      document.getElementById('target-link').classList.remove('d-none');
    else if(val == 'auction')
      document.getElementById('auctions').classList.remove('d-none');
    else
      document.getElementById('articles').classList.remove('d-none');
  });
  targetType.dispatchEvent(new Event('change'));

  var targetTypeNoAuctions = document.getElementById('target-type-no-auctions');
  targetTypeNoAuctions.addEventListener('change', function(e){
    var val = e.target.value;

    document.getElementById('target-link-no-auctions').classList.add('d-none');
    document.getElementById('auctions-no-auctions').classList.add('d-none');
    document.getElementById('articles-no-auctions').classList.add('d-none');

    if(val == 'outer_link')
      document.getElementById('target-link-no-auctions').classList.remove('d-none');
    else if(val == 'auction')
      document.getElementById('auctions-no-auctions').classList.remove('d-none');
    else
      document.getElementById('articles-no-auctions').classList.remove('d-none');
  });
  targetTypeNoAuctions.dispatchEvent(new Event('change'));
</script>

</body>

</html>