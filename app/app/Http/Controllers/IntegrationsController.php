<?php
namespace MobileOptin\Http\Controllers;

#use AWeberAPI;
use Htmldom;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MobileOptin\Models\SplitTestingStats;
use MobileOptin\Models\IntegrationsUser;
use MobileOptin\Models\User;
use MobileOptin\Models\UserProfile;
use PhpSpec\Exception\Exception;
use GuzzleHttp\Exception\ClientException;

class IntegrationsController extends Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index() {
                
        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        \SEOMeta::setTitle('Integrations - page ' . ( \Input::get('page') ? \Input::get('page') : 1 ));
        \SEOMeta::setDescription('meta desc');
        \SEOMeta::addKeyword([ 'key1', 'key2', 'key3']);


        $data['integrations'] = IntegrationsUser::where('user_id', '=', $user_id)->paginate(10)->setPath('integrations');
        $data['types'] = \MobileOptin\Models\IntegrationsType::lists('name', 'id');

        return view('integrations.list', $data);
    }

    public function add() {

        $data = array();
        \SEOMeta::setTitle('Add - Integration ');
        \DB::enableQueryLog();

        $data['integration'] = new \stdClass();
        $data['integration']->id = 0;
        $data['integration']->name = \Input::old('name');
        $data['integration']->api_key = \Input::old('active');
        $data['integration']->user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
        $data['integration']->type_id = \Input::old('type_id');

        $types = \MobileOptin\Models\IntegrationsType::lists('name', 'id');
        $integration_types = \MobileOptin\Models\IntegrationsType::all()->keyBy('id');

        /*
        // the AWeber package does not seem to properly define a namespace for itself and is not apparently getting mapped by Composer in a useful way
        require_once base_path('vendor/aweber/aweber/aweber_api/aweber.php');

        $aweber_app_id = $integration_types[2]->app_id;
        $aweber_consumer_key = $integration_types[2]->oauth_key;
        $aweber_consumer_secret = $integration_types[2]->oauth_secret;

        $aweber = new \AWeberAPI($aweber_consumer_key, $aweber_consumer_secret);

        $aweber_callback_url = url('integrations/aweber-oauth');
        $aweber_authorize_url = "https://auth.aweber.com/1.0/oauth/authorize_app/$aweber_app_id?oauth_callback=$aweber_callback_url";

        #dd($aweber_authorize_url);

        // TODO: change 'oob' to the callback URL
        #list($aweber_request_token, $aweber_token_secret) = $aweber->getRequestToken('oob');
        #dd("$aweber_request_token - $aweber_token_secret " . $aweber->getAuthorizeUrl());

        #dd($aweber);
        */

        return view('integrations.add_edit', ['integration' => $data['integration'], 'types' => $types, 'integration_types' => $integration_types]);
    }

    public function edit($CId) {
        \SEOMeta::setTitle('Edit - Integration');


        try {
            $integration = IntegrationsUser::where('id', '=', $CId)->firstOrFail();
        } catch (\Exception $e) {
            return redirect()->route('integrations')->withError($e . 'Integration not found or you do not have permissions');
        }

        $types = \MobileOptin\Models\IntegrationsType::lists('name', 'id');
        $integration_types = \MobileOptin\Models\IntegrationsType::all()->keyBy('id');

        $aweber_account      = false;
        $gotoWebinar_account = false;
        if($integration->type_id == 2 && $integration->organizerKey && $integration->authorization) {
            // the AWeber package does not seem to properly define a namespace for itself and is not apparently getting mapped by Composer in a useful way
            require_once base_path('vendor/aweber/aweber/aweber_api/aweber.php');
            $aweber_app_id = $integration_types[2]->app_id;
            $aweber_consumer_key = $integration_types[2]->oauth_key;
            $aweber_consumer_secret = $integration_types[2]->oauth_secret;
            $aweber = new \AWeberAPI($aweber_consumer_key, $aweber_consumer_secret);
            $aweber_account = $aweber->getAccount($integration->organizerKey, $integration->authorization);
            #dd($aweber_account);
        }else if($integration->type_id == 3){
        	$organiser_key = $integration->api_key;
        	$gotoWebinar_account = true;
        }

        return view('integrations.add_edit', [
            'integration' => $integration,
            'types' => $types,
            'integration_types' => $integration_types,
            'aweber_account' => $aweber_account,
        	'gotoWebinar_account' => $gotoWebinar_account,
        	'organizer_key' => ($gotoWebinar_account ? $organiser_key : null)
        ]);
    }

    public function upsert() {
        $validator = \Validator::make(\Input::only('id','name','api_key','type_id','authorization','organizerKey'), [
                    'id' => 'required|integer',
                    'name' => 'required',
                    'api_key' => 'required_if:type_id,1',
                    'type_id' => 'required|integer',
                   /* 'authorization' => 'required_if:type_id,3', */
                   /* 'organizerKey' => 'required_if:type_id,3,'*/
        ],['required_if' => 'The :attribute field is required.']);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
			
            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

            if (\Input::get('id') > 0) {
                $new_camp = IntegrationsUser::where('id', '=', \Input::get('id'))->first();
                if (!$new_camp) {
                    return redirect()->back()->withInput()->withError('Integration not found');
                }
                $new_camp->name = \Input::get('name');
                $new_camp->api_key = \Input::get('api_key');
                $new_camp->type_id = \Input::get('type_id');
                $new_camp->authorization = \Input::get('authorization');
                $new_camp->organizerKey = \Input::get('organizerKey');
                if($new_camp->type_id == 4 && !$new_camp->local_api_key)
                {
                    // Zapier integration and no local key set yet
                    // generate a local api key which Zapier can authenticate with when connect back to this app
                    $new_camp->local_api_key = md5(microtime().rand());
                }
                $new_camp->save();
            } else {
            	$integration_type = \Input::get('type_id');
                $new_camp = new IntegrationsUser();
                $new_camp->user_id = $user_id;
                $new_camp->name = \Input::get('name');
                $new_camp->api_key = \Input::get('api_key');
                $new_camp->type_id = $integration_type;
                $new_camp->authorization = $integration_type == 3 ? '' : \Input::get('authorization');
                $new_camp->organizerKey = \Input::get('organizerKey');
                $new_camp->integration_id = 1;
                if($new_camp->type_id == 4)
                {
                    // Zapier integration
                    // generate a local api key which Zapier can authenticate with when connect back to this app
                    $new_camp->local_api_key = md5(microtime().rand());
                }
                $new_camp->save();
            }
			
            switch(\Input::get('redirect_api')){
            	case 2:
            		$integration_type = \MobileOptin\Models\IntegrationsType::find(2);
            		return redirect("https://auth.aweber.com/1.0/oauth/authorize_app/{$integration_type->app_id}?oauth_callback=".urlencode(url('integrations/aweber-oauth', ['integration' => $new_camp->id])) );
            	case 3:
            		$integration_type = \MobileOptin\Models\IntegrationsType::find(3);
            		return redirect("https://api.citrixonline.com/oauth/authorize?client_id=" . $integration_type->oauth_key . "&state=" . $new_camp->id . "?oauth_callback=".urlencode(url('integrations/aweber-gotowebinar-online'))
        );
            	break;
            }
            return redirect()->route('integrations')->withNotify('Integration saved');
        }
    }

    public function delete($CId) {
        try {
            if (Auth::user()->getOwner() == false) {
                $domain = IntegrationsUser::where('user_id', '=', Auth::id())->where('id', '=', $CId)->firstOrFail();
                $domain->forceDelete();
                return redirect()->route('integrations')->withSuccess('Integration Deleted');
            }
        } catch (\Exception $e) {
            
        }
        return redirect()->route('integrations')->withError('Integration not removed');
    }
    
    public function goto_webinar(Request $request){
    	
    	if ($request->has('code') && $request->has('state')){
    		
    		$user_id       = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
    		$code          = $request->input('code');
    		$campaign_id   = $request->input('state');
    		
    		$integration_type = \MobileOptin\Models\IntegrationsType::find(3);
    		
    		try {
    			$integration = IntegrationsUser::where('id', '=', $campaign_id)->whereUserId($user_id)->firstOrFail();
    		} catch (\Exception $e) {
    			return redirect()->route('integrations')->withError($e . 'Integration not found or you do not have permissions');
    		}
    		
    		//Request access token 
    		$client = new \GuzzleHttp\Client();
    		
    		try{
    			$response = $client->post('https://api.citrixonline.com/oauth/access_token', [
    					'headers' => [
    						'Accept'       => ' application/json',
    						'Content-Type' => 'application/x-www-form-urlencoded'
    					],
    					'body'    => [
    						"grant_type" => "authorization_code",
    						"code"       => $code,
    						"client_id"  => $integration_type->oauth_key
    					],
    					'allow_redirects' => false,
    					'timeout' => 5
    					]);
    			
    			if ($response->getStatusCode() == '200') {
    				 
    				Log::info($response->getBody());
    				$json_resp = json_decode($response->getBody(), true);
    				 
    				//access_token   => access_token
    				//organizer_key  => api_key
    				//refresh_token  => authorization
    				//account_key    => local_api_key
    				
    				$integration->api_key       = $json_resp['organizer_key'];
    				$integration->access_token  = $json_resp['access_token'];
    				$integration->authorization = $json_resp['refresh_token'];
    				$integration->local_api_key = $json_resp['account_key'];   //for Admin calls
    				$integration->save();
    				return redirect()->route('edit_integration', ['id' => $integration->id])->withSuccess('Integration added');
    				//return \Redirect::route('edit_integration', $integration->id);
    			}	
    		}catch(ClientException $exception){
    			Log::debug("[EXCEPTION MESSAGE ] " . $exception->getMessage() );
    		}
    	}
    	abort(403, 'There was a problem authorizing your GotoWebinar account. Please go back and try again.');
    }

    public function aweber_oauth(Request $request, $CId) {
        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        if($CId) {
            try {
                $integration = IntegrationsUser::where('id', '=', $CId)->whereUserId($user_id)->firstOrFail();
            } catch (\Exception $e) {
                return redirect()->route('integrations')->withError($e . 'Integration not found or you do not have permissions');
            }
        } else {
            $integration = new IntegrationsUser();
            $integration->user_id = $user_id;
        }
        $integration->type_id = 2; // AWeber

        $integration_types = \MobileOptin\Models\IntegrationsType::all()->keyBy('id');

        // the AWeber package does not seem to properly define a namespace for itself and is not apparently getting mapped by Composer in a useful way
        require_once base_path('vendor/aweber/aweber/aweber_api/aweber.php');
        #dd($request->input('authorization_code'));
        try {
            $credentials = \AWeberAPI::getDataFromAweberID($request->input('authorization_code'));
        } catch(\AWeberAPIException $e) {
            abort(401, 'Error granting AWeber permissions.  Please go back and try again.');
        }

        #dd($credentials);
        list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $credentials;

        #dd("$accessKey, $accessSecret");
        //$aweber_consumer_key = $integration_types[2]->oauth_key;
        //$aweber_consumer_secret = $integration_types[2]->oauth_secret;

        $aweber = new \AWeberAPI($consumerKey, $consumerSecret);
        $account = $aweber->getAccount($accessKey, $accessSecret);

        if($account)
        {
            $integration->organizerKey  = $accessKey;
            $integration->authorization = $accessSecret;
            $integration->api_key       = $consumerKey;
            $integration->access_token  = $consumerSecret;
            $integration->save();

            return \Redirect::route('edit_integration', $integration->id);
        }

        abort(403, 'There was a problem authorizing your AWeber account. Please go back and try again.');

        $list_name = 'default';
        try {
            $found_lists = $account->lists->find(['name' => $list_name]);
            //must pass an associative array to the find method
            #return $found_lists[0];
        }
        catch(Exception $exc) {
            abort(403, 'ERROR');
            //dd($exc);
        }

        dd($found_lists);

    }
}
