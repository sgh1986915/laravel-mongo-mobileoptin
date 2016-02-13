<?php

namespace MobileOptin\Http\Middleware;

use Closure;

use Illuminate\Http\RedirectResponse;

class ModuleAccessMiddleWare {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($request->is('messages')) return $next($request);

        $modules = \MobileOptin\Models\Modules::where('status',1)->get();
        $flag = false;
        $pack = [];
       foreach($modules as $p){
           if($request->is(str_replace(' ','_',strtolower($p->name))) || $request->is(str_replace(' ','_',strtolower($p->name)).'/*') ){
               $flag = true;
               $pack = $p;
           }
       } 
       
        $user = $request->user();
        if (!$user->hasRole('admin') && $flag == true) {
            $module = \MobileOptin\Models\Modules::where('name', 'LIKE', $pack->name)->first();
            $check = \MobileOptin\Models\ModuleUser::where('user_id', $user->id)->where('module_id', $module->id)->where('status', 1)->get();
           
            if (count($check) == 0) {
                return new RedirectResponse(url('/home'));
            }
        }
        return $next($request);
    }

}
