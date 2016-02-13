<?php

namespace MobileOptin\Http\Middleware;

use Closure;

use Illuminate\Http\RedirectResponse;

class PackageAccessMiddleWare {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $packages = \MobileOptin\Models\Packages::where('status',1)->get();
        $flag = false;
        $pack = [];
       foreach($packages as $p){
           if($request->is(str_replace(' ','_',strtolower($p->name))) || $request->is(str_replace(' ','_',strtolower($p->name)).'/*') ){
               $flag = true;
               $pack = $p;
           }
       } 
       
        $user = $request->user();
        if (!$user->hasRole('admin') && $flag == true) {
            $package = \MobileOptin\Models\Packages::where('name', 'LIKE', $pack->name)->first();
            $check = \MobileOptin\Models\PackageUser::where('user_id', $user->id)->where('package_id', $package->id)->where('status', 1)->get();
           
            if (count($check) == 0) {
                return new RedirectResponse(url('/home'));
            }
        }
        return $next($request);
    }

}
