<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Horse;
use App\Models\Auction;
use App\Models\AuctionHorseReg;
use App\Models\Banner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;

class HomeController extends Controller {

    //


    public function searchItems(Request $request) {
        if ($request->has('term')) {
            $term = '%' . $request->term . '%';

            $horses = Horse::where('name_en', 'like', $term)->where('status' ,'accepted')->take(20)->get();
            $auctions = Auction::where('name', 'like', $term)->take(20)->get();
            $news = \App\Models\Blog::where('title', 'like', $term)->take(20)->get();

            $wrapper = new \stdClass();
            $wrapper->total_horses = Horse::where('name_en', 'like', $term)->where('status' ,'accepted')->count();
            $wrapper->total_auctions = Auction::where('name', 'like', $term)->count();
            $wrapper->total_news = \App\Models\Blog::where('title', 'like', $term)->count();

            $data = [];

            foreach ($horses as $n) {
                $p = new \stdClass();
                $p->type = "horse";
                $p->text = $n->name_en;
                $p->image = $n->getImage();
                $p->id = $n->id;
                $data[] = $p;
            }
            foreach ($auctions as $n) {
                $p = new \stdClass();
                $p->type = "auction";
                $p->text = $n->name;
                $p->image = $n->buildPoster();
                $p->id = $n->id;
                $data[] = $p;
            }
            foreach ($news as $n) {
                $p = new \stdClass();
                $p->type = "news";
                $p->text = $n->title;
                $p->image = $n->buildPoster();
                $p->id = $n->id;
                $data[] = $p;
            }

            $wrapper->data = $data;
            return response()->json($wrapper, 200);
        } else {
            return response()->json("Term was not sent", 400);
        }
    }

    public function getNotifications() {
        $nots = \App\Models\Notification::whereIn('user_id', [Auth::id(), -1])->orderBy('id', 'desc')->take('30')->get();
        $data = [];
        foreach ($nots as $n) {
            $p = new \stdClass();
            $p->icon = "https://netsequel.com/wp-content/uploads/2017/10/android-push-notification-icon-size-format-guidelines-1024x1024.png";
            $p->date = date('Y/m/d h:i a', strtotime($n->created_at));
            $p->text = $n->translate("en");
            $p->type = $n->not_type;
            $p->target = $n->not_id;
            $data[] = $p;
        }
        return response()->json($data, 200);
    }

    public function getNewsById(Request $request, $id) {

        $b = \App\Models\Blog::find($id);
        $b->views_count = $b->views_count + 1;
        $b->save();
        $b->thumb_url = $b->buildPoster();
        $b->time_formatted = \App\Http\Controllers\Helpers\TimeUtils::humanTiming($b->created_at);

        return $this->formResponse("News retrived", $b, 200);
    }

    public function getNews(Request $request) {

        $blog = \App\Models\Blog::orderBy('id', 'desc')->paginate(20);
        foreach ($blog as $b) {
            $b->thumb_url = $b->buildPoster();
            $b->time_formatted = \App\Http\Controllers\Helpers\TimeUtils::humanTiming($b->created_at);
            $b->details = strip_tags(implode(' ', array_slice(explode(' ', $b->details), 0, 10))) . "...";
        }


        return $this->formResponse("News retrived", $blog, 200);
    }

    public function homeWidgets(Request $request) {

        $home = new \stdClass();

        $banner = Banner::all();

        $home->no_auctions_banner = $banner->where('place', 'no_auctions')->first();
        $home->main_banner = $banner->where('place', 'main')->first();
        
        $data = Auction::whereIn('id' , AuctionHorseReg::select(['auction_id'])->where('status_string' ,'started')
                ->orWhere('status_string' ,'created')->pluck('auction_id')->toArray())->where('status', 1);
         

        $data = $data->take(10) ->orderBy('start_date', 'asc')->get();
        $user_id = $request->has('user_id') ? $request->user_id : -1;
        foreach ($data as $item) {

            
            unset($item->terms);
            unset($item->bidding_buttons);
            $item->in_favourite = \App\Models\Favourite::where('user_id', $user_id)->where('target_type', 1)
                            ->where('target_id', $item->id)->count() > 0;
            $item->end_date  = AuctionHorseReg::where('auction_id' ,$item->id)->max('lot_end_date');
            $item->auction_poster = $item->buildPoster();
            $item->count_horses = AuctionHorseReg::where('auction_id', $item->id)->count();
            $item->start_time_formatted = Carbon::parse($item->start_date)->format('Y/m/d h:i a');
            $item->end_time_formatted = $item->end_date ?  Carbon::parse($item->end_date)->format('Y/m/d h:i a') :"Unlimited";

           
            if ($item->accepts_offline_lots == -1 && $item->accepts_silent_lots == -1) {
                $item->type = "online";
            } else {
                $item->type = "mixed";
            
            }
            
           $item->status = "unknown";
            $item->time_remaining = 0;
            $item->type = "online";
            $count_live = AuctionHorseReg::where('auction_id' ,$item->id)->where('status_string' ,'started')->count();
            $count_waiting = AuctionHorseReg::where('auction_id' ,$item->id)->where('status_string' ,'created')->count();
            if($count_live >0) {
                     $item->status = "live";
            }else{
                  if($count_waiting >0) {
                       $item->status = "upcoming";
                  }else{
                      $item->status = "completed"; 
                  }
            }
            
            
            
             $item->auction_type =  $item->type;
        }



        $home->live_auctions = $data;

        $home->count_total_auctions = Auction::count();
        $home->count_live_lots = AuctionHorseReg::where('status_string', 'started')
                ->count();
        $home->count_active_bidders = \App\Models\User::where('user_type', 1)
                ->count();
        $home->blog = \App\Models\Blog::orderBy('id', 'desc')->take(10)->get();
        foreach ($home->blog as $b) {
            $b->thumb_url = $b->buildPoster();
            $b->time_formatted = \App\Http\Controllers\Helpers\TimeUtils::humanTiming($b->created_at) . " ago";
            $b->details = strip_tags(implode(' ', array_slice(explode(' ', $b->details), 0, 10))) . "...";
        }
      

        return $this->formResponse("Home Data retrived", $home, 200);
    }
    
    
    
   

}
