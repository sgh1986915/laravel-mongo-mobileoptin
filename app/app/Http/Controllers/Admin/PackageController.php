<?php

namespace MobileOptin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use MobileOptin\Http\Controllers\Controller;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignsTemplates;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use MobileOptin\Models\Role;
use MobileOptin\Models\TemplatesGroups;
use MobileOptin\Models\User;
use MobileOptin\Models\Package;
use MobileOptin\Models\PackageToTemplatesGroup;
use MobileOptin\Models\UserOwner;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UsersToTemplatesGroup;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller {


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct() {
        parent::__construct();
    }


    public function index() {
        $data = ['admin_navigation' => true];
        $data['packages'] = [];
        $packages = \MobileOptin\Models\Package::all();
        $data['packages'] = $packages;
        return \Response::view('admin.packages.list', $data);
    }
    
    public function activatePackage($id,$status){
        $module = Package::where('id',$id)->first();
    
        if($status == "true"){
            $module->status = 1;
        }else{
            $module->status = 0;
        }
        if($module->save())
            return json_encode(1);
        else
            return json_encode($module->getErrors());
    }
    
      public function add()
    {

                if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => 1 ];
        } else {
            $data = [ ];
        }
            \SEOMeta::setTitle( 'Add - Package ' );
            \DB::enableQueryLog();


            $data[ 'add' ]            = true;
            $data[ 'allowed_groups' ] = TemplatesGroups::get()->lists( 'name', 'id' );
            $data[ 'user_allowed_groups' ] = [];
            
            $data[ 'package' ]                          = new \stdClass();
            $data[ 'package' ]->id                      = 0;
            $data[ 'package' ]->name                    = \Input::old( 'name' );
            $data[ 'package' ]->status                  = \Input::old( 'status' );
            $data[ 'package' ]->max_campaigns           = \Input::old( 'max_campaigns', 0 );
            $data[ 'package' ]->jvzoo_id                = \Input::old( 'jvzoo_id', null );
            $data[ 'package' ]->split_testing           = \Input::old( 'split_testing' );
            $data[ 'package' ]->redirect_page           = \Input::old( 'redirect_page' );
            $data[ 'package' ]->embed                   = \Input::old( 'embed' );
            $data[ 'package' ]->hosted                  = \Input::old( 'hosted' );
            $data[ 'package' ]->analytics_retargeting   = \Input::old( 'analytics_retargeting' );
            $data[ 'package' ]->traffic_experts_academy = \Input::old( 'traffic_experts_academy' );
            $data[ 'package' ]->user_id               = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
 

            return view( 'admin.packages.add_edit', $data );
        
    
    }

    public function edit( $CId )
    {
        \SEOMeta::setTitle( 'Edit - Package' );
        try {
            $package    = Package::where( 'id', '=', $CId )->firstOrFail();
            $data['package'] = $package;
            $data[ 'add' ]            = true;
            $data[ 'allowed_groups' ] = TemplatesGroups::get()->lists( 'name', 'id' );
            $data[ 'user_allowed_groups' ] = $package->allowed_groups()->lists('template_group_id','template_group_id');
     
        } catch ( \Exception $e ) {
            return redirect()->route( 'packages' )->withError( $e . 'Package not found or you do not have permissions' );
        }

        return view( 'admin.packages.add_edit', $data +['admin_navigation' => Auth::user()->hasRole( 'admin' ) ? 1 : 0] );
    }


    public function upsert()
    {
        $validator = \Validator::make( \Input::only( 'id', 'name','max_campaigns'), [
            'id'   => 'required|integer',
            'name' => 'required',
            'max_campaigns'=>'required'
        ] );


        if ( $validator->fails() ) {
            // The given data did not pass validation
            return redirect()->back()->withInput()->withErrors( $validator );
        } else {

            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
            $max_camp = \Input::get( 'max_campaigns' );
        
            if ( \Input::get( 'id' ) > 0 ) {
                $new_camp = Package::where( 'id', '=', \Input::get( 'id' ) )->first();
                if ( !$new_camp ) {
                    return redirect()->back()->withInput()->withError( 'Package not found' );
                }
                $new_camp->name                    = \Input::get( 'name' );
                $new_camp->jvzoo_id                = \Input::get( 'jvzoo_id' );
                $new_camp->max_campaigns           = empty( $max_camp ) ? 0 : $max_camp;
                $new_camp->split_testing           = ( \Input::get( 'split_testing' ) === 'split_testing' ) ? 1 : 0;
                $new_camp->redirect_page           = ( \Input::get( 'redirect_page' ) === 'redirect_page' ) ? 1 : 0;
                $new_camp->embed                   = ( \Input::get( 'embed' ) === 'embed' ) ? 1 : 0;
                $new_camp->hosted                  = ( \Input::get( 'hosted' ) === 'hosted' ) ? 1 : 0;
                $new_camp->analytics_retargeting   = ( \Input::get( 'analytics_retargeting' ) === 'analytics_retargeting' ) ? 1 : 0;
                $new_camp->traffic_experts_academy = ( \Input::get( 'traffic_experts_academy' ) === 'traffic_experts_academy' ) ? 1 : 0;
                $new_camp->save();
                
                PackageToTemplatesGroup::where( 'package_id', $new_camp->id )->delete();
              
                if(null !== \Input::get( 'allowed_groups'  ))
                foreach ( \Input::get( 'allowed_groups' ) as $alg ) {
                    $utg                    = new PackageToTemplatesGroup();
                    $utg->package_id           = $new_camp->id;
                    $utg->template_group_id = $alg;
                    $utg->save();
                      
                }

                
            } else {
                    $package = Package::create( [
                        'name'                    => \Input::get( 'name' ),
                        'jvzoo_id'                => \Input::get( 'jvzoo_id' ),
                        'max_campaigns'           => empty( $max_camp ) ? 0 : $max_camp,
                        'split_testing'           => ( \Input::get( 'split_testing' ) === 'split_testing' ) ? 1 : 0,
                        'redirect_page'           => ( \Input::get( 'redirect_page' ) === 'redirect_page' ) ? 1 : 0,
                        'embed'                   => ( \Input::get( 'embed' ) === 'embed' ) ? 1 : 0,
                        'hosted'                  => ( \Input::get( 'hosted' ) === 'hosted' ) ? 1 : 0,
                        'status'                  => 1,
                        'analytics_retargeting'   => ( \Input::get( 'analytics_retargeting' ) === 'analytics_retargeting' ) ? 1 : 0,
                    	'traffic_experts_academy' => ( \Input::get( 'traffic_experts_academy' ) === 'traffic_experts_academy' ) ? 1 : 0,
                    ] );
                    
                   $allowed_groups = \Input::get( 'allowed_groups' );
                    if ( !empty( $allowed_groups ) ) {
                        foreach ( $allowed_groups as $alg ) {
                            $utg                    = new PackageToTemplatesGroup();
                            $utg->package_id        = $package->id;
                            $utg->template_group_id = $alg;
                            $utg->save();
                        }
                    }

            }




            return redirect()->route( 'admin.package' )->withNotify( 'Package saved' );
        }

    }

    public function delete( $CId )
    {
        try {
            if ( Auth::user()->getOwner() == false ) {
                $package = Package::where( 'id', '=', $CId )->firstOrFail();
                $package->forceDelete();

                PackageToTemplatesGroup::where( 'package_id', '=', $CId )->delete();
                
                $userTable = (new UserProfile())->getTable();
                DB::table($userTable)->where('package_id', '=', $CId)->update(array('package_id' => 0));            
                
                return redirect()->route( 'admin.package' )->withSuccess( 'Package Deleted' );
            }

        } catch ( \Exception $e ) {
        }
        return redirect()->route( 'admin.package' )->withError( 'Package not removed ' );

    }

}
