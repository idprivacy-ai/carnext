<?php 

// app/Http/Middleware/ShareUserData.php
namespace App\Http\Middleware;
use App\Models\Favourite;
use Closure;
use Illuminate\Support\Facades\View;

class ShareUserData
{
    public function handle($request, Closure $next)
    {
        $fav=[];
        if (auth()->check()) {
            $fav=[];
            $user_id =  auth()->user()->id; // Assuming there's a 'favorites' relationship or method
            $fav = Favourite::where('user_id',$user_id)->get()->pluck('vid')->toArray();
           
        }
        View::share('fav', $fav);
        return $next($request);
    }
}
