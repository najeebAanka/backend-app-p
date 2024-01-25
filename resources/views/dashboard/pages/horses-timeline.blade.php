
<!DOCTYPE html>
<html lang="en">
<?php $horse = App\Models\Horse::find(Route::input('id')); ?>;
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - {{$horse->name_en}} Timeline</title>
 <?php    $request = request(); 
 $currentUser = Auth::user(); ?>
@include('dashboard.shared.css')
<style>
 * {
	border: 0;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}
:root {
	--hue: 3;
	--bg: hsl(var(--hue),10%,90%);
	--fg: hsl(var(--hue),10%,10%);
	--primary: hsl(var(--hue),90%,50%);
	--primaryT: hsla(var(--hue),90%,50%,0);
	--trans-dur: 0.3s;
	font-size: calc(16px + (24 - 16) * (100vw - 320px) / (2560 - 320));
}
#body-master {
background-color: #ffffff63;
    color: hsl(0deg 0% 0%);
    display: flex;
    font: 1em/1.5 "DM Sans", sans-serif;
    transition: background-color var(--trans-dur), color var(--trans-dur);
    width: 97%;
    margin: 0 auto;
    border-radius: 5px;
}
a {
	transition: color var(--trans-dur);
}
h1 {
	font-size: 2em;
	margin: 0 0 1.5rem;
	text-align: center;
}
.timeline {
	margin: auto;
	padding: 3em 0;
}
.timeline__items {
	list-style: none;
	margin-left: 0.75em;
}
.timeline__item {
	padding: 0 0 1.5em 1.25em;
	position: relative;
}
.timeline__item br {
	display: none;
}
.timeline__item:before,
.timeline__item:after,
.timeline__item .timeline__item-pub,
.timeline__item .timeline__item-time,
.timeline__item .timeline__item-link {
	transition:
		background-color var(--trans-dur),
		opacity var(--trans-dur) cubic-bezier(0.65,0,0.35,1),
		transform var(--trans-dur) cubic-bezier(0.65,0,0.35,1);
}
.timeline__item:before,
.timeline__item:after {
	background-color: var(--primary);
	content: "";
	display: block;
	position: absolute;
	left: 0;
}
.timeline__item:before {
	border-radius: 50%;
	top: 0.25em;
	width: 1em;
	height: 1em;
	transform: translateX(-50%) scale(0);
	z-index: 1;
}
.timeline__item:after {
	top: 0.75em;
	width: 0.125em;
	height: 100%;
	transform: translateX(-50%);
}
.timeline__item:last-child:after {
	display: none;
}
.timeline__item-pub,
.timeline__item-link,
.timeline__item-time {
	display: block;
	opacity: 0;
	transform: translateX(-0.75em);
}
.timeline__item-link,
.timeline__item-link:visited {
	color: var(--primary);
}
.timeline__item-link {
	transition: color var(--trans-dur);
}
.timeline__item-link:hover {
	text-decoration: underline;
}
.timeline__item-pub,
.timeline__item-time {
	font-size: 0.833em;
	line-height: 1.8;
}
.timeline__item-time {
	font-weight: bold;
}
.timeline__loading {
	text-align: center;
}

/* Observed items */
.timeline__item--in .timeline__item-pub,
.timeline__item--in .timeline__item-link,
.timeline__item--in .timeline__item-time {
	opacity: 1;
	transform: translateX(0);
}
.timeline__item--in:before {
	transform: translateX(-50%) scale(1);
}

/* Dark theme */
@media (prefers-color-scheme: dark) {
	:root {
		--bg: hsl(var(--hue),10%,10%);
		--fg: hsl(var(--hue),10%,90%);
		--primary: hsl(var(--hue),90%,70%);
	}
}

/* Beyond mobile */
@media (min-width: 768px) {
	.timeline__items {
		margin-left: 17em;
		width: 17em;
	}
	.timeline__item-time {
		position: absolute;
		top: 0;
		right: calc(100% + 1.25rem);
		text-align: right;
		width: 17rem;
		transform: translateX(0.75em);
                ransform: scale(1.5);
	}

}
    
    
</style>
</head>

<body>
@include('dashboard.shared.nav-top')

@include('dashboard.shared.side-nav')
  <main id="main" class="main">
  <div class="bg-trans p-2">
      <span style="float: right;
    background-color: #fff;
    padding: 1rem;">Status : <b>{{strtoupper($horse->status)}}</b></span>
      
    <div class="pagetitle">
      <h1>{{$horse->name_en}}'s Updates Timeline</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('home')}}">Home</a></li>
          <?php if($request->has('redirected') && $request->redirected == 'auction'){
              $a = App\Models\Auction::find($request->auction_id);
              ?>
           <li class="breadcrumb-item"><a href="{{url('auctions/'.$request->auction_id)}}">{{$a->name}}</a></li>
          <?php
              
          }else{ ?>
           <li class="breadcrumb-item"><a href="{{url('horses')}}">Horses</a></li>
           <li class="breadcrumb-item"><a href="{{url('horse-details/'.$horse->id)}}">{{$horse->name_en}}</a></li>
          <?php } ?>
          <li class="breadcrumb-item active">Timeline</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
  </div>
    <section class="section dashboard">
                @include('dashboard.shared.messages')
      <div class="row">
          <div class="col-12" id='body-master'>
              
              <div class="timeline">
	<ul class="timeline__items" data-items></ul>
	<div class="timeline__loading" data-loading>Loading timeline…</div>
</div>
              
              
          </div>
          
      </div>
    </section>

  </main><!-- End #main -->

@include('dashboard.shared.footer')
@include('dashboard.shared.js')
<script type="module">
		  
window.addEventListener("DOMContentLoaded",() => {
	const t = new Timeline(".timeline");
});

class Timeline {
	articles = [];

	constructor(el) {
		this.el = document.querySelector(el);

		this.init();
	}
	init() {
		this.generateArticles();
		this.removeLoading();
		this.build();
		this.observeArticles();
	}
	build() {
		const itemContainer = this.el.querySelector("[data-items]");
		if (!itemContainer) return;

		const localeCode = "en-US";

		this.articles.forEach(article => {
			const time = document.createElement("time");
			time.className = "timeline__item-time";

			const DRaw = new Date(article.date);
			const D = {
				y: DRaw.getFullYear(),
				m: DRaw.getMonth() + 1,
				d: DRaw.getDate() ,
				h: DRaw.getHours() ,
				m: DRaw.getMinutes() ,
				s: DRaw.getSeconds() 
			};

			if (D.m < 10) D.m = `0${D.m}`;
			if (D.d < 10) D.d = `0${D.d}`;

			time.setAttribute("datetime", `${D.y}-${D.m}-${D.d}`);

			const articleDateLocal = DRaw.toLocaleDateString(localeCode,{
				year: "numeric",
				month: "long",
				day: "numeric"
			});
			time.innerText = articleDateLocal +" " +`${D.h}:${D.m}:${D.s}`;

			const link = document.createElement("a");
			link.className = "timeline__item-link";
			link.href = "#";
			link.innerText = article.title;

			const published = document.createElement("small");
			published.className = "timeline__item-pub";
			published.innerText = article.publisher;

			const item = document.createElement("li");
			item.className = "timeline__item";
			item.appendChild(time);
			item.appendChild(document.createElement("br"));
			item.appendChild(link);
			item.appendChild(document.createElement("br"));
			item.appendChild(published);

			itemContainer.appendChild(item);
		});
	}
	generateArticles() {
            this.articles = 
		<?php 
                
           $events = [];
           //          
           $event = new stdClass();
          
                     $event-> title = "Horse is added";          
                     $event-> date = $horse->created_at;          
                     $event-> publisher = "Horse was submitted by seller (".App\Models\User::find($horse->seller_id)->name.")";  
                     $events[] = $event;
                     //
                      $event = new stdClass();
                     $event-> title = "Horse is accepted by adminstration";          
                  $event-> date = $horse->created_at;            
                     $event-> publisher = "Horse is accepted and eligable to join auctions";  
                     $events[] = $event;
                     
                     foreach (\App\Models\HorseTimelineRecord::where('horse_id' ,$horse->id)->get() as $r){
                         
                          $event = new stdClass();
                     $event-> title = $r->title;          
                  $event-> date = $r->created_at;            
                     $event-> publisher = $r->description; 
                     $events[] = $event;  
                         
                         
                     }
                     
                     
                        //
//                      $event = new stdClass();
//                     $event-> title = "Request is sent to join (test offline auction)";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(3);          
//                     $event-> publisher = "Joining request was sent to join this auction as (horse purchase)";  
//                     $events[] = $event;
//                     
//                     
//                          
//                        //
//                      $event = new stdClass();
//                     $event-> title = "Join request accepted";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(4);          
//                     $event-> publisher = "The horse registered in (test offline auction) in lot #342";  
//                     $events[] = $event;
//                     
//                            
//                        //
//                      $event = new stdClass();
//                     $event-> title = "Lot started";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(11);          
//                     $event-> publisher = "Lot started by auction manager";  
//                     $events[] = $event;
//                     
//                      //
//                      $event = new stdClass();
//                     $event-> title = "Lot Time extended";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(14);          
//                     $event-> publisher = "Lot time extended 60 more minutes ";  
//                     $events[] = $event;
//                     
//                      //
//                      $event = new stdClass();
//                     $event-> title = "Lot Time extended";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(18);          
//                     $event-> publisher = "Lot time extended 60 more minutes ";  
//                     $events[] = $event;
//                     
//                      //
//                      $event = new stdClass();
//                     $event-> title = "Lot Finihsed";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(21);          
//                     $event-> publisher = "Horse was sold for 44k AED to (Mr Osama Adel)";  
//                     $events[] = $event;
//                     
//                     
//                         //
//                      $event = new stdClass();
//                     $event-> title = "Delivery method set";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(31);          
//                     $event-> publisher = "Delivery method set to (Pick up from stud)";  
//                     $events[] = $event;
//                     
//                        //
//                      $event = new stdClass();
//                     $event-> title = "Horse delivered to new owner";          
//                     $event-> date = \Carbon\Carbon::now()->addHours(41);          
//                     $event-> publisher = "Horse is delivered to owner , payment completed";  
//                     $events[] = $event;
                     
                     
                echo json_encode($events);
                
                ?>
                            ;
		this.articles.sort((a,b) => b.date - a.date);
	}
	observeArticles() {
		this.observer = new IntersectionObserver(
			entries => { 
				entries.forEach(entry => {
					const { target } = entry;
					const itemIn = "timeline__item--in";

					if (entry.isIntersecting) target.classList.add(itemIn);
					else target.classList.remove(itemIn);
				});
			}, {
				root: null,
				rootMargin: "0px",
				threshold: 1
			}
		);
		// observe the items
		const items = document.querySelectorAll(".timeline__item");
		Array.from(items).forEach(item => {
			this.observer.observe(item);
		});
	}
	removeLoading() {
		const loading = this.el.querySelector("[data-loading]");
		if (!loading) return;

		this.el.removeChild(loading);
	}
	toTitleCase(title) {
		return title.split(" ").map(word => word[0].toUpperCase() + word.substring(1)).join(" ");
	}
}
</script>
</body>

</html>