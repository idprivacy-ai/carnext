<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dealer;
use App\Models\DealerSource;
use App\Models\AssignDealer;

class checkStoreCount
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('dealer')->user(); 
        if ($user) {
            if (!$user->parent_id) {
                $mainDealer = $user;
                $storeList = $user->storeList()->get();
                $parentId = $user->id;
                if ($user->storeList()->count() == 0) {
                    return redirect(route('dealer.stores'))->with('error', 'You have no stores assigned.');
                }
            } else {
                $parentId = $user->parent_id;
                
                $assignedDealerSources = AssignDealer::where('dealer_id', $user->id)->pluck('dealer_source_id');
                $storeList = DealerSource::whereIn('id', $assignedDealerSources)->get();
                if ($storeList->count() == 0) {
                    return redirect(route('dealer.stores'))->with('error', 'You have no stores assigned.');
                }
            }

        }

        return $next($request);
    }
}
