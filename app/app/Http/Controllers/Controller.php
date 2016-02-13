<?php namespace MobileOptin\Http\Controllers;

use Auth;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{

    use DispatchesCommands, ValidatesRequests;

    function __construct()
    {
        if (!Auth::guest()){
            $userowner = Auth::user()->getOwnerObj();
            if ( Auth::user()->is( 'suspended' ) || ( $userowner && $userowner->is( 'suspended' ) ) ) {
                Auth::logout();
                return redirect()->to( '/auth/login' )->withError( 'This account is currently suspended.  If you have have any questions about this please email <a href="mailto:support@mobileoptin.com">support@mobileoptin.com</a>' );
            }
         
        }

    }
}
