<?php namespace MobileOptin\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use PhpSpec\Exception\Exception;

class VerifyCsrfToken extends BaseVerifier
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        try {
            if ( !$request->is( 'api/*' ) ) {
                return parent::handle( $request, $next );
            }

            return $next( $request );
        } catch ( \Illuminate\Session\TokenMismatchException  $e ) {
            return redirect( '/auth/login' )->withErrors( 'Session time out' );
        }
    }

}
