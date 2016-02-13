<?php

namespace MobileOptin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use MobileOptin\Http\Controllers\Controller;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignsTemplates;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use MobileOptin\Models\UserContent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Home Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders your application's "dashboard" for users that
      | are authenticated. Of course, you are free to change or remove the
      | controller as you wish. It is just here to get your app started!
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index() {

        \SEOMeta::setTitle('dashboard');
        \SEOMeta::setDescription('meta desc');
        \SEOMeta::addKeyword([ 'key1', 'key2', 'key3']);
        $data = ['admin_navigation' => true];
        return \Response::view('admin.dashboard', $data);
    }
    
    public function user_content(){
    	$data        = ['admin_navigation' => true];
    	$userCotnent = UserContent::findOrCreate(1);
    	$data['user_content'] = $userCotnent;
    	return \Response::view('admin.user_content', $data);
    }
    
    public function save_user_content(){
    	// getting all of the post data
    	$input = [ 'content'    => \Input::get( 'content' ) ];
    	// setting up rules
    	$rules = [ 'content'    => 'required' ];
    	// doing the validation, passing post data, rules and the messages
    	$validator = Validator::make( $input, $rules );
    	if ( $validator->fails() ) {
    		// send back to the page with the input data and errors
    		return \Redirect::back()->withInput()->withErrors( $validator );
    	} else {
    		$userCotnent = UserContent::findOrCreate( \Input::get( 'id' ) );
    		$userCotnent->content   = \Input::get( 'content' );
    		$userCotnent->title     = \Input::get( 'title' );
    		$userCotnent->save();
    		return \Redirect::route( 'dashboard' )->withSuccess( 'User content Saved' );
    	}
    }
    
    public function reset_announcement_settings(){
    	DB::table('users')->update(array('popup_message' => 1));
    	return \Redirect::route( 'admin.save.user_content' )->withSuccess( 'Announcement Settings Reset' );
    }

    public function modules() {
        $data = ['admin_navigation' => true];
        $data['modules'] = [];
        $modules = \MobileOptin\Models\Modules::all();
        $data['modules'] = $modules;
        return \Response::view('admin.modules', $data);
    }
    
    public function activateModule($id,$status){
        $module = \MobileOptin\Models\Modules::where('id',$id)->first();
    
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

}
