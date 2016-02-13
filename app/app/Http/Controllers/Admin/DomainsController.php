<?php namespace MobileOptin\Http\Controllers\Admin;

use Htmldom;
use Illuminate\Http\Request;
use MobileOptin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignStats;
use MobileOptin\Models\CampaignsTemplates;
use MobileOptin\Models\SplitTestingStats;
use MobileOptin\Models\UserDomains;
use MobileOptin\Models\Domains;
use MobileOptin\Models\User;
use MobileOptin\Models\UserAllowedCampaigns;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UserTemplates;
use PhpSpec\Exception\Exception;

class DomainsController extends Controller
{

       /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware( 'auth' );

             parent::__construct();

    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {

              
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => 1 ];
        } else {
            $data = [ ];
        }
        
        \SEOMeta::setTitle( 'Domains - page ' . ( \Input::get( 'page' ) ? \Input::get( 'page' ) : 1 ) );

        \SEOMeta::setDescription( 'meta desc' );
        \SEOMeta::addKeyword( [ 'key1', 'key2', 'key3' ] );

        $data[ 'domains' ] = Domains::paginate( 10 )->setPath( 'domains' );

        return view( 'admin.domains.list', $data );
    }


    public function add()
    {

                if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => 1 ];
        } else {
            $data = [ ];
        }
            \SEOMeta::setTitle( 'Add - Domain ' );
            \DB::enableQueryLog();


            $data[ 'domain' ]       = new \stdClass();
            $data[ 'domain' ]->id   = 0;
            $data[ 'domain' ]->name = \Input::old( 'name' );
            $data[ 'domain' ]->active = \Input::old( 'active' );
            $data[ 'domain' ]->status = \Input::old( 'status' );
            $data[ 'domain' ]->user_id                   = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
 

            return view( 'admin.domains.add_edit', $data );
        
    
    }

    public function edit( $CId )
    {
        \SEOMeta::setTitle( 'Edit - Domain' );
        try {
             $domain    = Domains::where( 'id', '=', $CId )->firstOrFail();
     
        } catch ( \Exception $e ) {
            return redirect()->route( 'domains' )->withError( $e . 'Domain not found or you do not have permissions' );
        }

        return view( 'admin.domains.add_edit', ['domain'=> $domain,'admin_navigation' => Auth::user()->hasRole( 'admin' ) ? 1 : 0] );
    }


    public function upsert()
    {
        $validator = \Validator::make( \Input::only( 'id', 'name'), [
            'id'   => 'required|integer',
            'name' => 'required'

        ] );


        if ( $validator->fails() ) {
            // The given data did not pass validation
            return redirect()->back()->withInput()->withErrors( $validator );
        } else {

            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            if ( \Input::get( 'id' ) > 0 ) {
                $new_camp = Domains::where( 'id', '=', \Input::get( 'id' ) )->first();
                if ( !$new_camp ) {
                    return redirect()->back()->withInput()->withError( 'Domain not found' );
                }
            } else {
                $new_camp = new Domains();
                $new_camp->user_id = $user_id;

            }

            $new_camp->name                      = \Input::get( 'name' );
            $new_camp->active                    = \Input::get( 'active' ) == 'on' ? 1 : 0;
            $new_camp->status                    = \Input::get( 'status' );
            $new_camp->save();



            return redirect()->route( 'admin.domains' )->withNotify( 'Domain saved' );
        }

    }

    public function delete( $CId )
    {
        try {
            if ( Auth::user()->getOwner() == false ) {
                $domain = Domains::where( 'user_id', '=', Auth::id() )->where( 'id', '=', $CId )->firstOrFail();
                $domain->forceDelete();

             
                UserDomains::where( 'domain_id', '=', $CId )->delete();
                return redirect()->route( 'admin.domains' )->withSuccess( 'Domain Deleted' );
            }

        } catch ( \Exception $e ) {
        }
        return redirect()->route( 'admin.domains' )->withError( 'Domain not removed ' );

    }

    public function change_satus( $CId, $new_status )
    {
        try {
            if ( Auth::user()->getOwner() == false ) {
                $domain = Domains::where( 'user_id', '=', Auth::id() )->where( 'id', '=', $CId )->firstOrFail();
            } else {
                $domain = Domains::where( 'user_id', '=', Auth::user()->getOwner() )->where( 'id', '=', $CId )->firstOrFail();
            }

            if ( $new_status ) {
                $domain->activated_on = date( 'Y-m-d H:i:s' );
            } else {
                $domain->deactivated_on = date( 'Y-m-d H:i:s' );

            }
            $domain->active = $new_status;
            $domain->save();

            return redirect()->route( 'admin.domains' )->withSuccess( 'Domain status changed' );

        } catch ( \Exception $e ) {
            return redirect()->route( 'admin.domains' )->withError( 'Status not updated ' );
        }
    }


}
