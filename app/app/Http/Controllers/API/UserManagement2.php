<?php

namespace MobileOptin\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use Input;
use Log;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignStats;
use MobileOptin\Models\Roles;
use MobileOptin\Models\Package;
use MobileOptin\Models\TemplatesGroups;
use MobileOptin\Models\User;
use MobileOptin\Models\UserAllowedCampaigns;
use MobileOptin\Models\UserOwner;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UsersToTemplatesGroup;
use MobileOptin\Models\UserTemplates;
use MobileOptin\Transformers\RolesTransformer;
use MobileOptin\Transformers\TemplatesGroupsTransformer;
use MobileOptin\Transformers\User as UserTransformer;
use League\Fractal\ParamBag;
use \Swift_Mailer;
use \Swift_SmtpTransport as SmtpTransport;
use GuzzleHttp\Exception\ClientException;

class UserManagement extends Controller {

    protected $apiMethods = [
        'show' => [
            'level' => 10
        ],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function all() {
        $limit = Input::get('limit', 0);
        $offset = Input::get('offset', 0);

        $order_by = Input::get('order_by', 'id');
        $order_dir = Input::get('order_dir', 'asc');


        $User = User::orderBy($order_by, $order_dir);

        if ($limit > 0) {
            $User->take($limit);
        }
        if ($offset > 0) {
            $User->skip($offset);
        }
        $User = $User->get();

        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
//         return $User;
        return $resp->withCollection($User, new UserTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show() {
        $uid = Input::get('id', 0);
        $email = Input::get('email', '');

        if ($uid > 0) {
            $User = User::find($uid);
        } elseif (!empty($email)) {
            $User = User::where('email', $email)->first();
        }

        if (isset($User)) {

            return $this->response->withItem($User, new UserTransformer);
        } else {
            return $this->response->errorNotFound('User Not Found');
        }
    }

    /**
     * create the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function create() {


        $submited_data = Input::all();


        $validator = Validator::make(
                        $submited_data, [

                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'role_id' => 'required|integer',
                    'maximum_number_of_campaigns' => 'required|integer',
                    'allowed_split_testing' => 'required|integer',
                    'allowed_redirects' => 'required|integer',
                    'allowed_embed' => 'required|integer',
                    'allowed_hosted' => 'required|integer',
                    'allowed_template_groups' => 'required',
                    'analytics_retargeting' => 'required|integer',
                        ]
        );

        if ($validator->fails()) {
            return $this->response->errorNotFound($validator->errors());
        } else {
            $user_email = Input::get('email');
            $User = User::where('email', '=', $user_email)->first();


            if (!isset($User->id)) {


                $user = User::create([
                            'name' => Input::get('name'),
                            'email' => $user_email,
                            'password' => bcrypt(Input::get('password')),
                            'role_id' => Input::get('role_id'),
                ]);
                UserProfile::create([
                    'user_id' => $user->id,
                    'max_campaigns' => Input::get('maximum_number_of_campaigns', getenv('defautl_number_of_campaign')),
                    'split_testing' => Input::get('allowed_split_testing', 0),
                    'redirect_page' => Input::get('allowed_redirects', 0),
                    'embed' => Input::get('allowed_embed', 0),
                    'hosted' => Input::get('allowed_hosted', 0),
                    'analytics_retargeting' => Input::get('analytics_retargeting', 0),
                ]);


                $new_template_groups = explode(',', Input::get('allowed_template_groups'));
                UsersToTemplatesGroup::create([ 'user_id' => $user->id, 'template_group_id' => 1]);
                if (!empty($new_template_groups) && is_array($new_template_groups)) {
                    foreach ($new_template_groups as $tpg) {

                        if ($tpg !== 1) {
                            UsersToTemplatesGroup::create([ 'user_id' => $user->id, 'template_group_id' => $tpg]);
                        }
                    }
                }

                return $this->response->withArray([ 'code' => 200, 'success' => true, 'updated' => true]);
            } else {
                return $this->response->errorNotFound('User Not Found');
            }
        }
    }

    /**
     * create the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function createPackage() {

        $submited_data = Input::all();
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
        $validator = Validator::make(
                        $submited_data, [

                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'role_id' => 'required|integer',
                    'package_id' => 'required|integer',
                        ]
        );

        if ($validator->fails()) {
            return $resp->errorNotFound($validator->errors());
        } else {
            $user_email = Input::get('email');
            $User = User::where('email', '=', $user_email)->first();
            $package = Package::where('id', Input::get('package_id'))->first();

            if (!isset($package->id)) {
                return $resp->errorNotFound('Package Not found');
            }
            if (!isset($User->id)) {

                $user = User::create([
                            'name' => Input::get('name'),
                            'email' => $user_email,
                            'password' => bcrypt(Input::get('password')),
                            'role_id' => Input::get('role_id'),
                ]);
                UserProfile::create([
                    'user_id' => $user->id,
                    'max_campaigns' => 10,
                    'split_testing' => 1,
                    'redirect_page' => 1,
                    'embed' => 1,
                    'hosted' => 1,
                    'analytics_retargeting' => 1,
                    'package_id' => Input::get('package_id')
                ]);

                return $resp->withArray([ 'code' => 200, 'success' => true, 'updated' => true]);
            } else {
                return $resp->errorNotFound('User Already Exists');
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request) {


        $submited_data = Input::all();


        $validator = Validator::make(
                        $submited_data, [

//                'name'                        => 'required',
                    'email' => 'required|email',
//                'role_id'                     => 'required|integer',
//                'maximum_number_of_campaigns' => 'required|integer',
//                'allowed_split_testing'       => 'required|integer',
//                'allowed_redirects'           => 'required|integer',
//                'allowed_embed'               => 'required|integer',
//                'allowed_hosted'              => 'required|integer',
//                'allowed_template_groups'     => 'required|array'
                        ]
        );

        if ($validator->fails()) {
            return $this->response->errorNotFound($validator->errors());
        } else {
            if (empty($user_param)) {
                $user_param = Input::get('user_id');
            }
            if (empty($user_param)) {
                $user_param = Input::get('email');
            }
            if (is_numeric($user_param)) {
                $User = User::find($user_param);
            } else {
                $User = User::where('email', '=', $user_param)->first();
            };


            if (isset($User)) {

                if ($request->has('name')) {
                    $new_name = Input::get('name');
                    if (!empty($new_name)) {
                        $User->name = $new_name;
                    }
                }

                $User->email = Input::get('email');

                if ($request->has('role_id')) {
                    $role_id = Input::get('role_id');
                    if (!empty($role_id)) {
                        $User->role_id = $role_id;
                    }
                }
                $User->save();
                $user_profile = UserProfile::where('user_id', '=', $User->id)->first();
                if ($request->has('maximum_number_of_campaigns')) {
                    $maximum_number_of_campaigns = Input::get('maximum_number_of_campaigns', 0);

                    $user_profile->max_campaigns = $maximum_number_of_campaigns;
                }
                if ($request->has('allowed_split_testing')) {
                    $allowed_split_testing = Input::get('allowed_split_testing', 0);

                    $user_profile->split_testing = $allowed_split_testing;
                }
                if ($request->has('allowed_redirects')) {
                    $allowed_redirects = Input::get('allowed_redirects', 0);

                    $user_profile->redirect_page = $allowed_redirects;
                }
                if ($request->has('allowed_embed')) {
                    $allowed_embed = Input::get('allowed_embed', 0);

                    $user_profile->embed = $allowed_embed;
                }
                if ($request->has('allowed_hosted')) {
                    $allowed_hosted = Input::get('allowed_hosted', 0);

                    $user_profile->hosted = $allowed_hosted;
                }
                if ($request->has('analytics_retargeting')) {
                    $analytics_retargeting = Input::get('analytics_retargeting', 0);

                    $user_profile->analytics_retargeting = $analytics_retargeting;
                }
                $user_profile->save();

                if ($request->has('allowed_template_groups')) {
                    $allowed_templates_groups = Input::get('allowed_template_groups');
                    if (!empty($allowed_templates_groups)) {
                        UsersToTemplatesGroup::where('user_id', '=', $User->id)->delete();
                        $new_template_groups = explode(',', $allowed_templates_groups);

                        if (!empty($new_template_groups) && is_array($new_template_groups)) {
                            foreach ($new_template_groups as $tpg) {
                                UsersToTemplatesGroup::create([ 'user_id' => $User->id, 'template_group_id' => $tpg]);
                            }
                        }
                    }
                }
                return $this->response->withArray([ 'code' => 200, 'success' => true, 'updated' => true]);
            } else {
                return $this->response->errorNotFound('User Not Found');
            }
        }
    }

    /**
     * Assign Module to user
     * @param type $user_param
     * @param type $module_id
     */
    public function assignModule($user_param, $module_id) {
        if (empty($user_param)) {
            $user_param = Input::get('user_id');
        }
        if (empty($user_param)) {
            $user_param = Input::get('email');
        }
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        };
        if (is_numeric($user_param)) {
            $module = \MobileOptin\Models\Modules::where('id', $module_id)->first();
        }
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);

        if ($User !== null && $module !== null) {
            $userpack = \MobileOptin\Models\ModuleUser::where('user_id', $User->id)->where('module_id', $module_id)->first();
            if ($userpack === null) {
                $userpack = new \MobileOptin\Models\ModuleUser;
                $userpack->user_id = $User->id;
                $userpack->module_id = $module_id;
                $userpack->status = 1;
                $userpack->save();
            } else {
                $userpack->status = 1;
                $userpack->save();
            }
            return $resp->withArray([ 'code' => 200, 'success' => true]);
        } else {
            return $resp->errorNotFound('User or Module Not Found');
        }
    }

    /**
     * Assign Module to user
     * @param type $user_param
     * @param type $module_id
     */
    public function revokeModule($user_param, $module_id) {
        if (empty($user_param)) {
            $user_param = Input::get('user_id');
        }
        if (empty($user_param)) {
            $user_param = Input::get('email');
        }
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        };
        if (is_numeric($user_param)) {
            $module = \MobileOptin\Models\Modules::where('id', $module_id)->first();
        }
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);

        if ($User !== null && $module !== null) {
            $userpack = \MobileOptin\Models\ModuleUser::where('user_id', $User->id)->where('module_id', $module_id)->first();
            if ($userpack === null) {
                $userpack = new \MobileOptin\Models\ModuleUser;
                $userpack->user_id = $User->id;
                $userpack->module_id = $module_id;
                $userpack->status = 0;
                $userpack->save();
            } else {
                $userpack->status = 0;
                $userpack->save();
            }
            return $resp->withArray([ 'code' => 200, 'success' => true]);
        } else {
            return $resp->errorNotFound('User or Module Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($user_param) {
        if (empty($user_param)) {
            $user_param = Input::get('user_id');
        }
        if (empty($user_param)) {
            $user_param = Input::get('email');
        }
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        };
        if (!$User->isEmpty()) {
            $User->delete();
            UsersToTemplatesGroup::where('user_id', '=', $User->id)->delete();
            UserProfile::where('user_id', '=', $User->id)->delete();
            UserAllowedCampaigns::where('user_id', '=', $User->id)->delete();
            UserOwner::where('user_id', '=', $User->id)->delete();
            UserOwner::where('owner_id', '=', $User->id)->delete();
            UserTemplates::where('user_id', '=', $User->id)->delete();
            $campaings_ids = [];
            $allcampaings = Campaigns::where('user_id', '=', $User->id)->get();
            foreach ($allcampaings as $camp) {
                $campaings_ids[] = intval($camp->id);
            }
            if (count($campaings_ids) > 0) {
                Campaigns::where('user_id', '=', $User->id)->delete();
                CampaignStats::whereIn('campaign_id', $campaings_ids)->delete();
                SplitTestingStats::whereIn('campaign_id', $campaings_ids)->delete();
            }
            return $this->response->withArray([ 'code' => 200, 'success' => true, 'deleted' => true]);
        } else {
            return $this->response->errorNotFound('User Not Found');
        }
    }

    public function disable() {
        if (empty($user_param)) {
            $user_param = Input::get('user_id');
        }
        if (empty($user_param)) {
            $user_param = trim(Input::get('email'));
        }
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        }
        if (!empty($User)) {
            $User->role_id = 3;
            $User->save();
            return $this->response->withArray([ 'code' => 200, 'success' => true, 'disabled' => true]);
        } else {
            return $this->response->errorNotFound('User Not Found');
        }
    }

    public function roles() {
        return $this->response->withCollection(Roles::all(), new RolesTransformer);
    }

    public function availableTemplateGroups() {
        return $this->response->withCollection(TemplatesGroups::all(), new TemplatesGroupsTransformer);
    }

    public function addTemplateGroup($user_param) {
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        }
        if (!$User->isEmpty()) {
            UsersToTemplatesGroup::create([ 'user_id' => $User->id, 'template_group_id' => Input::get('tgid')]);

            return $this->response->withArray([ 'code' => 200, 'success' => true, 'saved' => true]);
        } else {
            return $this->response->errorNotFound('User Not Found');
        }
    }

    public function assignPackageToUser($user_param, $package_id) {
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        }
        if (is_numeric($package_id)) {
            $package = \MobileOptin\Models\Package::where('id', $package_id)->first();
        }
        if ($User !== null && ($package !== null || $package_id == 0)) {
            $user_profile = \MobileOptin\Models\UserProfile::where('user_id', $User->id)->first();
            if ($user_profile !== null) {
                $user_profile->package_id = $package_id;
                if ($user_profile->save())
                    return $resp->withArray([ 'code' => 200, 'success' => true, 'saved' => true]);
            } else {
                return $resp->errorNotFound('User Not Found');
            }
        } else {
            return $resp->errorNotFound('User Not Found');
        }
    }

    public function postAssignPackageToUser() {
        if (empty($user_param)) {
            $user_param = Input::get('user_id');
        }
        if (empty($user_param)) {
            $user_param = Input::get('email');
        }
        if (empty($package_id)) {
            $package_id = Input::get('package_id');
        }


        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        }
        if (is_numeric($package_id)) {
            $package = \MobileOptin\Models\Package::where('id', $package_id)->first();
        }
        if ($User !== null && ($package !== null || $package_id == 0)) {
            $user_profile = \MobileOptin\Models\UserProfile::where('user_id', $User->id)->first();
            if ($user_profile !== null) {
                $user_profile->package_id = $package_id;
                if ($user_profile->save())
                    return $resp->withArray([ 'code' => 200, 'success' => true, 'saved' => true]);
            } else {
                return $resp->errorNotFound('User Not Found');
            }
        } else {
            return $resp->errorNotFound('User Not Found');
        }
    }

    public function removeTemplateGroup($user_param) {
        if (is_numeric($user_param)) {
            $User = User::find($user_param);
        } else {
            $User = User::where('email', '=', $user_param)->first();
        }
        if (!$User->isEmpty()) {
            UsersToTemplatesGroup::where('user_id', '=', $User->id)->where('template_group_id', '=', Input::get('tgid'))->delete();

            return $this->response->withArray([ 'code' => 200, 'success' => true, 'deleted' => true]);
        } else {
            return $this->response->errorNotFound('User Not Found');
        }
    }

    public function parseResponce() {
        $result = Input::all();
        if (!is_array($result))
            $json = json_decode($result, true);
        else
            $json = $result;
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);



        Log::info('SEARCH ME');
        Log::info($json);

        if (isset($json['ctransaction']) && isset($json['ccustemail']) && isset($json['ccustname']) && isset($json['cproditem'])) {
            switch ($json['ctransaction']) {
                case 'SALE':
                    $password = explode(' ', $json['ccustname']);
                    $user = User::where('email', '=', $json['ccustemail'])->first();
                    if (!$user) {
                        $package = Package::where('jvzoo_id', $json['cproditem'])->first();
                        if ($package) {
                            $firstn = isset($password[0]) ? $password[0] : $json['ccustname'];
                            $pass = $firstn . '+19837';
                            $user = User::create([
                                        'name' => $json['ccustname'],
                                        'email' => $json['ccustemail'],
                                        'password' => bcrypt($pass),
                                        'role_id' => 2,
                            ]);
                            $user_profile = UserProfile::create([
                                        'user_id' => $user->id,
                                        'max_campaigns' => 0,
                                        'split_testing' => 0,
                                        'redirect_page' => 0,
                                        'embed' => 0,
                                        'hosted' => 0,
                                        'package_id' => $package->id,
                                        'analytics_retargeting' => 0,
                            ]);


                            $transport = SmtpTransport::newInstance('smtp.sendgrid.net', 587, 'tls');
                            $transport->setUsername('marketerscrm');
                            $transport->setPassword('RWawM4VHke46cm7zfCrGw9SFEE08MxF9');
                            $gmail = new Swift_Mailer($transport);
                            \Mail::setSwiftMailer($gmail);
                            \Mail::send('emails.create_user', ['FirstName' => $firstn, 'email' => $json['ccustemail']], function ($message) use ($json) {
                                $message->from('support@mobileoptin.com', 'Support');
                                $message->to($json['ccustemail'])->subject('Your MobileOptin.com Login');
                            });

                            return $resp->withArray([ 'code' => 200, 'success' => true, 'name' => $json['ccustname']]);
                        } else {
                            return $resp->withArray([ 'code' => 404, 'success' => false, 'msg' => 'no package']);
                        }
                    } else {
                        return $resp->withArray([ 'code' => 404, 'success' => false, 'msg' => 'user already exists']);
                    }
                    break;
                case 'RFND':
                case 'CGBK':
                case 'INSF':
                case 'CANCEL-REBILL':
                    $user = User::where('email', '=', $json['ccustemail'])->first();
                    if ($user) {
                        $user->role_id = 3;
                        $user->save();
                        return $resp->withArray([ 'code' => 200, 'success' => true]);
                    }
                    break;
                case 'BILL':
                case 'UNCANEL-REBILL':
                    $user = User::where('email', '=', $json['ccustemail'])->first();
                    if ($user) {
                        $user->role_id = 2;
                        $user->save();
                        return $resp->withArray([ 'code' => 200, 'success' => true]);
                    }
                    break;
            }
        } else {
            return $resp->withArray([ 'code' => 404, 'success' => false]);
        }
    }

    public function parseSendGridResponce() {
        $result = Input::all();
        if (!is_array($result))
            $json = json_decode($result, false);
        else
            $json = $result;
        $resp = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
        Log::info($json);
        if (isset($json['from']) && isset($json['to'])) {
            
            
            $to_array = explode('@', $json['to']);
            $to_array = explode('temp', $to_array[0]);
            if (isset($to_array[0]) && isset($to_array[1])) {
                $compaign_id = $to_array[0];
                $user_template = \MobileOptin\Models\UserTemplates::where('integration_id', $compaign_id)->first();

                if (isset($user_template)) {
                    $user_id = $user_template->user_id;
                    $integrations = \MobileOptin\Models\IntegrationsUser::where('user_id', $user_id)->where('integration_id', 1)->first();

                    if ($integrations && strlen($integrations->api_key) > 2 && $user_template && strlen($user_template->integration_id) > 0) {



                        if (isset($json['from'])) {
                            $from_array = explode('<', $json['from']);
                            if (isset($from_array[0]) && isset($from_array[1])) {
                                $user_name = $from_array[0];
                            }
                        }

                        if (isset($json['sender_ip'])) {
                            $user_ip = $json['sender_ip'];
                        }

                        Log::info(['name' => $user_name, 'email' => $user_email, 'campaign' => ['campaignId' => $user_template->integration_id]]);
                        $json_resp = [];
                        $client = new \GuzzleHttp\Client();
                        $arr = [];
                        try {
                            $response = $client->post('http://api.getresponse.com/v3/contacts', [
                                'headers' => ['X-Auth-Token' => 'api-key ' . $integrations->api_key, 'Content-Type' => 'application/json'],
                                'json' => ['name' => $user_name, 'email' => $user_email, 'campaign' => ['campaignId' => $user_template->integration_id]],
                                'allow_redirects' => false,
                                'timeout' => 5
                            ]);
                            
                               Log::info($response->getHeaders()); 
                            $json_resp = json_decode($response->getBody(), true);
                            
                        } catch (ClientException $e) {
                           Log::info('ERORRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR');
                           Log::info($e->getMessage());
                        }

                      Log::info($json_resp);
                      Log::info('compaign:' . $compaign_id . ' email:' . $user_email . ' name:' . $user_name . ' key:' . $integrations->api_key . ' ip:' . $user_ip);
                    }
                }
            }
        } else {
            return $resp->withArray([ 'code' => 404, 'success' => false]);
        }
    }

}
