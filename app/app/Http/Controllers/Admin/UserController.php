<?php

namespace MobileOptin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use MobileOptin\Http\Controllers\Controller;
use MobileOptin\Models\Role;
use MobileOptin\Models\TemplatesGroups;
use MobileOptin\Models\User;
use MobileOptin\Models\UserOwner;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UsersToTemplatesGroup;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use MobileOptin\Models\Package;
use MobileOptin\Models\PackageToTemplatesGroup;
use Log;

class UserController extends Controller {
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
        $data = [ 'admin_navigation' => Auth::user()->hasRole('admin') ? 1 : 0];
        if (Auth::user()->hasRole('admin')) {
            $data = [ 'admin_navigation' => 1];
        } else {
            $data = [];
        }

        $find_by_email = Input::get('search_email');
        if (Auth::user()->hasRole('admin')) {
            $data['users'] = User::with('profile', 'campaigns', 'role', 'owner')->whereHas('owner', function ( $query ) {
                
            }, '!=');
        } else {
            $data['users'] = User::with('profile', 'campaigns', 'role', 'owner')->whereHas('owner', function ( $query ) {
                $query->where('owner_id', '=', Auth::id());
            });
        }
        if (!empty($find_by_email)) {
            $data['users'] = $data['users']->where('email', 'like', '%' . $find_by_email . '%');
        }


        $data['users'] = $data['users']->paginate(10)->setPath('users')->appends(Input::except('page'));
        return \Response::view('admin.users.list', $data);
    }

    public function add() {

        if (Auth::user()->can('add_user')) {

            $data = array();
            \SEOMeta::setTitle('Add - User ');

            $data['roles'] = Role::get()->lists('role_title', 'id');
            $data['user'] = new \stdClass();
            $data['user']->id = 0;
            $data['user']->name = \Input::old('name');
            $data['user']->email = \Input::old('email');
            $data['user']->password = \Input::old('password');
            $data['user']->role_id = \Input::old('role_id');
            $data['user']->profile = new \stdClass();
            $data['user']->profile->max_campaigns = \Input::old('max_campaigns', 0);
            $data['user']->profile->split_testing = \Input::old('split_testing');
            $data['user']->profile->redirect_page = \Input::old('redirect_page');
            $data['user']->profile->embed = \Input::old('embed');
            $data['user']->profile->hosted = \Input::old('hosted');
            $data['user']->profile->analytics_retargeting = \Input::old('analytics_retargeting');
            $data['user']->profile->traffic_experts_academy = \Input::old('traffic_experts_academy');
            
            $data['add'] = true;
            $data['allowed_groups'] = TemplatesGroups::get()->lists('name', 'id');

            $data['packages'] = Package::where('status', 1)->lists('name', 'id');
            $data['packages'] += [0 => 'No Package'];

            $data['modules'] = \MobileOptin\Models\Modules::where('status', 1)->get();

            $data['this_user_allowed_groups'] = Auth::user()->allowed_groups()->get();
            
            $data['hide_campaign_data'] = true;

            return view('admin.users.add_edit', $data);
        }
        return redirect()->route('users')->withError('You can not add a user !');
    }
    
	public function connect_as_user($uid){
    	if (Auth::user()->hasRole('admin')) {
    		$admin_id       = Auth::user()->id;
    		$currentUser    = Auth::loginUsingId((int) $uid);
    		Auth::getSession()->set('current_admin_user', $admin_id);
    		return redirect()->route('home')->withSuccess('Connected as user ' . $currentUser->name);
    	}else{
    		return redirect()->route('admin.users')->withError('You can not connect as the current user !');
    	}
    }
    
    public function reconnect_as_admin($admin_id){
    	//get admin user 
    	$user = User::with('profile')->where('id', '=', $admin_id)->first();
    	if ($user->hasRole('admin')) {
    		$currentUser    = Auth::loginUsingId((int) $admin_id);
    		Auth::getSession()->set('current_admin_user', null);
    		return redirect()->route('admin.users')->withSuccess('Exit success!');
    	}else{
    		return redirect()->route('home')->withError('You can not connect as admin !');
    	}
    }

    public function edit($uid) {

        if (Auth::user()->can('add_user')) {


            $user = User::with('owner', 'profile', 'allowed_groups')->where('id', '=', $uid)->first();
            if ($user) {
                $user_owner = 0;
                if (isset($user->owner) && method_exists($user->owner, 'first')) {

                    $user_owner_r = $user->owner->first();
                    if (isset($user_owner_r->id)) {
                        $user_owner = $user_owner_r->id;
                    }
                }

                if (( $user_owner > 0 && $user_owner == Auth::id() ) || Auth::user()->hasRole('admin')) {
                    \SEOMeta::setTitle('Edit - User ');

                    $data = array();
                    $data['packages'] = Package::where('status', 1)->lists('name', 'id');
                    $data['packages'] += [0 => 'No Package'];
                    $data['roles'] = Role::get()->lists('role_title', 'id');
                    $data['user'] = $user;
                    $data['hide_campaign_data'] = $user_owner != 0;

                    $data['user_allowed_groups'] = [];

                    foreach ($user->allowed_groups as $alg) {
                        $data['user_allowed_groups'][$alg->template_group_id] = true;
                    }


                    $data['allowed_groups'] = TemplatesGroups::get()->lists('name', 'id');
                    $data['this_user_allowed_groups'] = Auth::user()->allowed_groups()->get();

                    $data['modules'] = \MobileOptin\Models\Modules::where('status', 1)->get();
                    $data['modules_user'] = \MobileOptin\Models\ModuleUser::where('user_id', $uid)->get();

                    return view('admin.users.add_edit', $data);
                }
                return redirect()->route('admin.users')->withError('You do not have access to this user');
            }
            return redirect()->route('admin.users')->withError('User not found');
        }
        return redirect()->route('admin.users')->withError('You can not add a user !');
    }

	public function upsert() {

        $uid         = \Input::get('id');
        $campaign_id = null;
        
        if ($uid > 0) {
        	
        		if(Auth::user()->hasRole('admin')){
        			$campaign_id = \Input::get('package_id');
        		}else{
        			$userProfile = UserProfile::where('user_id', Auth::id())->with('package')->first();
        			$campaign_id = $userProfile->package->id;
        		}
        		
        		$validator = \Validator::make(\Input::only('id', 'name', 'email', 'role_id'), [
			        				'id'       => 'required|integer',
			        				'name'     => 'required',
			        				'email'    => 'required|email',
			        				'password' => 'confirmed',
        						]);
        		
        } else {
        	if(Auth::user()->hasRole('admin')){
        		$campaign_id = \Input::get('package_id');
        		$validator = \Validator::make(\Input::only('id', 'name', 'email', 'role_id', 'package_id'), [
		        					'id'         => 'required|integer',
			        				'name'       => 'required',
			        				'email'      => 'required|email',
			        				'password'   => 'confirmed',
			        				'package_id' => 'required|integer|min:1'
		        				], [
	        						'package_id.min' => 'User must have an assigned package'
                    		]);
        	}else{
        		
        		$userProfile = UserProfile::where('user_id', Auth::id())->with('package')->first();
        		$campaign_id = $userProfile->package->id;
        		
        		$validator = \Validator::make(\Input::only('id', 'name', 'email', 'role_id', 'password', 'password_confirmation'), [
		        				'id'                    => 'required|integer',
		        				'name'                  => 'required',
		        				'email'                 => 'required|email',
		        				'password'              => 'required|confirmed',
		        				'password_confirmation' => 'required',
	        				]);
        	}
        }

        
        
        if ($validator->fails()) {
            // The given data did not pass validation
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $max_camp = \Input::get('max_campaigns');
            if ($uid > 0) {
                $user = User::where('id', '=', $uid)->first();
                if (!$user) {
                    return redirect()->back()->withInput()->withError('User not found');
                }

                $user->name    = \Input::get('name');
                $user->email   = \Input::get('email');
                $user->role_id = \Input::get('role_id');
                $newpassword   = \Input::get('password');
                if (!empty($newpassword)) {
                    $user->password = Hash::make($newpassword);
                }
                $user->role_id = \Input::get('role_id');
                $user->save();

                $puser = \MobileOptin\Models\ModuleUser::where('user_id', $uid)->delete();
                if (isset($_POST['module']))
                    foreach ($_POST['module'] as $p_id) {
                        $puser            = new \MobileOptin\Models\ModuleUser;
                        $puser->user_id   = $uid;
                        $puser->module_id = $p_id;
                        $puser->status    = 1;
                        $puser->save();
                    }

                $user_profile                          = UserProfile::where('user_id', '=', $uid)->first();
                $user_profile->max_campaigns           = empty($max_camp) ? 0 : $max_camp;
                $user_profile->split_testing           = ( \Input::get('split_testing') === 'split_testing' ) ? 1 : 0;
                $user_profile->redirect_page           = ( \Input::get('redirect_page') === 'redirect_page' ) ? 1 : 0;
                $user_profile->embed                   = ( \Input::get('embed') === 'embed' ) ? 1 : 0;
                $user_profile->hosted                  = ( \Input::get('hosted') === 'hosted' ) ? 1 : 0;
                $user_profile->package_id              =   $campaign_id;
                $user_profile->analytics_retargeting   = ( \Input::get('analytics_retargeting') === 'analytics_retargeting' ) ? 1 : 0;
                $user_profile->traffic_experts_academy = ( \Input::get('traffic_experts_academy') === 'traffic_experts_academy' ) ? 1 : 0;
                $user_profile->save();

                UsersToTemplatesGroup::where('user_id', $user->id)->delete();

                if (null !== \Input::get('allowed_groups'))
                    foreach (\Input::get('allowed_groups') as $alg) {
                        $utg = new UsersToTemplatesGroup();
                        $utg->user_id = $user->id;
                        $utg->template_group_id = $alg;
                        $utg->save();
                    }
            } else {
                $user = User::where('email', '=', \Input::get('email'))->first();
                if (!$user) {
                    $user = User::create([
                                'name' => \Input::get('name'),
                                'email' => \Input::get('email'),
                                'password' => bcrypt(\Input::get('password')),
                                'role_id' => \Input::get('role_id') ? \Input::get('role_id') : 2,
                            ]);
                    UserProfile::create([
                        'user_id' => $user->id,
                        'max_campaigns'           => empty($max_camp) ? 0 : $max_camp,
                        'split_testing'           => ( \Input::get('split_testing') === 'split_testing' ) ? 1 : 0,
                        'redirect_page'           => ( \Input::get('redirect_page') === 'redirect_page' ) ? 1 : 0,
                        'embed'                   => ( \Input::get('embed') === 'embed' ) ? 1 : 0,
                        'hosted'                  => ( \Input::get('hosted') === 'hosted' ) ? 1 : 0,
                        'package_id'              => $campaign_id,
                        'analytics_retargeting'   => ( \Input::get('analytics_retargeting') === 'analytics_retargeting' ) ? 1 : 0,
                        'traffic_experts_academy' => ( \Input::get('traffic_experts_academy') === 'traffic_experts_academy' ) ? 1 : 0,
                    ]);

                    if (Auth::user()->hasRole('advertiser')) {
                        $uo           = new UserOwner();
                        $uo->owner_id = Auth::id();
                        $uo->user_id  = $user->id;
                        $uo->save();
                    }

                    $puser = \MobileOptin\Models\ModuleUser::where('user_id', $user->id)->delete();
                    if(isset($_POST['module']))
                    foreach ($_POST['module'] as $p_id) {
                        $puser = new \MobileOptin\Models\ModuleUser;
                        $puser->user_id = $user->id;
                        $puser->module_id = $p_id;
                        $puser->status = 1;
                        $puser->save();
                    }

                    $allowed_groups = \Input::get('allowed_groups');
                    if (!empty($allowed_groups)) {
                        foreach ($allowed_groups as $alg) {
                            $utg = new UsersToTemplatesGroup();
                            $utg->user_id = $user->id;
                            $utg->template_group_id = $alg;
                            $utg->save();
                        }
                    }
                } else {
                    return redirect()->back()->withInput()->withError('email allready registerd');
                }
            }


            return redirect()->route('admin.users')->withNotify('User saved');
        }
    }

    public function delete($uid) {

        $user = User::with('owner', 'profile')->where('id', '=', $uid)->first();
        if ($user) {
            $user_owner = 0;
            if (isset($user->owner) && method_exists($user->owner, 'first')) {

                $user_owner_r = $user->owner->first();
                if (isset($user_owner_r->id)) {
                    $user_owner = $user_owner_r->id;
                }
            }

            if (( $user_owner > 0 && $user_owner == Auth::id() ) || Auth::user()->hasRole('admin')) {
                $user->delete();
                return redirect()->route('admin.users')->withError('user deleted');
            }
            return redirect()->route('admin.users')->withError('You do not have access to this user');
        }
        return redirect()->route('admin.users')->withError('User not found');
    }

    public function generateApiKey() {
        
    }

    public function deleteApiKey() {

        $apiKey = App::make(Config::get('apiguard.model', 'Chrisbjr\ApiGuard\Models\ApiKey'));
        $apiKey->key = $apiKey->generateKey();
        $apiKey->user_id = $this->getOption('user-id', 0);
        $apiKey->level = $this->getOption('level', 10);
        $apiKey->ignore_limits = $this->getOption('ignore-limits', 1);

        if ($apiKey->save() === false) {
            $this->error("Failed to save API key to the database.");
            return;
        }

        if (empty($apiKey->user_id)) {
            $this->info("You have successfully generated an API key:");
        } else {
            $this->info("You have successfully generated an API key for user ID#{$apiKey->user_id}:");
        }
        $this->info($apiKey->key);
    }

}
