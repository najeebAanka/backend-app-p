<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\ImageUtils;
use App\Models\Auction;
use App\Models\Banner;
use App\Models\Blog;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function show(){
        return view('dashboard.pages.banners', [
            'main_banner' => Banner::where('place', 'main')->first(),
            'no_auctions_banner' => Banner::where('place', 'no_auctions')->first(),
            'auctions' => Auction::latest()->get(['id', 'name']),
            'articles' => Blog::latest()->get(['id', 'title']),
        ]);
    }

    public function update(Request $request){
        $request->validate([
            'target_type' => 'required|in:outer_link,auction,article',
            'image' => 'required|image',
            'place' => 'required|in:main,no_auctions'
        ]);

        $target = '';
        if($request->target_type == 'outer_link'){
            $target = $request->target_link;
        }elseif($request->target_type == 'auction'){
            $target = $request->target_auction;
        }else{
            $target = $request->target_article;
        }

        $banner = Banner::where('place', $request->place)->first();

        $path =  ImageUtils::saveImage($request, "image", "banners", 1200, 1200);
        if($path)
            $banner->image = $path;
        else
            return back()->with('error', " 's image can not be saved");

        $banner->target_type = $request->target_type;
        $banner->target = $target;

        $banner->save();
        return back()->with('message', "banner updated succesfully.");
    }
}
