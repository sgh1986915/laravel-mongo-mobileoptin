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
use MobileOptin\Models\SplitTestingStats;
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
use MobileOptin\Classes\Apishoppingcart;
use MobileOptin\Classes\Tools;
use League\Fractal\ParamBag;
use \Swift_Mailer;
use \Swift_SmtpTransport as SmtpTransport;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\File;

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


            if (!isset($User->id) && Input::get('role_id') != 1) {


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
						$package = Package::where('jvzoo_id', 'regexp' , "[[:<:]]" . $json['cproditem'] . "[[:>:]]")->first();
                        if ($package) {
                            $firstn = isset($password[0]) ? $password[0] : $json['ccustname'];
                            $pass = $firstn . '19837';
                            $user = User::create([
                                        'name' => $json['ccustname'],
                                        'email' => $json['ccustemail'],
                                        'password' => bcrypt($pass),
                                        'role_id' => 4,
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
                                $message->from('support@mobileoptin.com', 'MobileOptin.com');
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
                        $user->role_id = 4;
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
            
            if (isset($json['envelope'])) {
                $envelope_array = json_decode($json['envelope'], false);
                if (isset($envelope_array->from)) {
                    $user_email = $envelope_array->from;
                }
                if (isset($envelope_array->to)) {
                    $json_to = $envelope_array->to[0];
                }

               Log::info($json_to);
                $to_array = explode('@', $json_to);
                $to_array = explode('temp', $to_array[0]);
                if (isset($to_array[0]) && isset($to_array[1])) {
                    $compaign_id = $to_array[0];
                    $user_template_id = $to_array[1];
                    
                    // NOTE: this code seemed distinctly buggy and has been modified.  the old code would load the UserTemplate based on the user_template.integration_id field, which contains a non-unique string of characters.  the new code loads the UserTemplate based on the user_template.id field, which is a unique number.
                    // eg: If the subscription request were sent to "xYcitemp734@mobileresponses.com".
                    // with the old code, the first UserTemplate found with an integration_id of "xYci" would be loaded, however 4 different UserTemplates have that integration_id and only the first one the database returns will be loaded (maybe not the right one).
                    // with the new code, the UserTemplate is loaded based on "734", which loads the only UserTemplate with that id

                    // The GetResponse code further below will still validate against the user_template->integration_id within context


                    //$user_template = \MobileOptin\Models\UserTemplates::where('integration_id', $compaign_id)->first();
                    $user_template = \MobileOptin\Models\UserTemplates::where('id', $user_template_id)->first();
                    
                    

                    if (isset($user_template)) {
                        $user_id = $user_template->user_id;
                        $integrations = \MobileOptin\Models\IntegrationsUser::where('id', $user_template->contact_type)->first();

                        /*
                        Log::info('integrations:');
                        Log::info($integrations);
                        Log::info('user_template:');
                        Log::info($user_template);
                        Log::info('result:');
                        Log::info($result);
                        */

                        $user_name = '';
                        if (isset($json['from'])) {
                            $from_array = explode('<', $json['from']);
                            if (isset($from_array[0]) && isset($from_array[1])) {
                                $user_name = $from_array[0];
                            }
                        }

                        $user_ip = '';
                        if (isset($json['sender_ip'])) {
                            $user_ip = $json['sender_ip'];
                        }

                        // record optin event to Mongo database
                        $event = 'optin';
                        $label = 'optin';
                        $name = $user_template->name;
                        $value = $json['to'];
                        $save_response = SplitTestingStats::record_event($user_template->campaign_id, $user_template_id, $event, $label, $name, $value, $user_ip);

                        Log::info('Recording optin event:');
                        Log::info($save_response);

                        if($integrations && $integrations->type_id == 2) {
                            // integration type (2) is Aweber
                            Log::info('Aweber integration: subscription received');

                            $campaign = $user_template->campaign;

                            $integration_types = \MobileOptin\Models\IntegrationsType::all()->keyBy('id');
                            // the AWeber package does not properly define a namespace for itself and is not getting mapped by Composer, so just include it directly

                            require_once base_path('vendor/aweber/aweber/aweber_api/aweber.php');
                            
                            $aweber_consumer_key    = !empty($integration->api_key) ? $integration->api_key : $integration_types[2]->oauth_key;
                            $aweber_consumer_secret = !empty($integration->access_token) ? $integration->access_token : $integration_types[2]->oauth_secret;
                            $aweber_access_key      = $integrations->organizerKey;
                            $aweber_access_secret   = $integrations->authorization;

                            try {
                            	
                                $aweber = new \AWeberAPI($aweber_consumer_key, $aweber_consumer_secret);
                                $aweber_account = $aweber->getAccount($aweber_access_key, $aweber_access_secret);

                                $aweber_list_id = $user_template->integration_id;
                                $list_url = "/accounts/{$aweber_account->id}/lists/{$aweber_list_id}";
                                $list = $aweber_account->loadFromUrl($list_url);

                                $aweber_subscriber = $list->subscribers->create([
				                                		'email'       => $user_email,
				                                		'name'        => str_limit( $user_name, 56),
				                                		'ip_address'  => $_SERVER['REMOTE_ADDR'],
				                                		'ad_tracking' => $campaign ? str_limit( $campaign->name, 16) : '',
				                                		'misc_notes'  => str_limit( $user_template->name, 56),
			                                		]);
                                 
                                Log::info(print_r($aweber_subscriber, true));
                                
                            } catch (\AWeberAPIException $e) {
                                Log::info('ERROR processing Aweber request:');
                                Log::info('Aweber Error: ' . $e->getMessage());
                            }

                        }  else if($integrations && $integrations->type_id == 4) {
                            // integration type (4) is Zapier
                            Log::info('Zapier integration: subscription received');

                            $campaign = $user_template->campaign;

                            // send request to URLs stored in zapier_webhook which correspond to the given integration
                            $client = new \GuzzleHttp\Client();
                            foreach($integrations->zapier_webhooks as $webhook)
                            {
                                try {
                                    $response = $client->post($webhook->url, [
                                        'headers'   => ['Content-Type' => 'application/json'],
                                        'json'      => [
                                            'name'  => $user_name,
                                            'email' => $user_email,
                                            'campaign_id' => $campaign ? $campaign->id : '',
                                            'campaign_name' => $campaign ? $campaign->name : '',
                                            'template_id' => $user_template_id,
                                            'template_name' => $user_template->name,
                                            #'dayOfCycle' => 0,
                                            #'campaign' => ['campaignId' => $user_template->integration_id]
                                        ],
                                        'allow_redirects' => false,
                                        'timeout'   => 5
                                        ]) ;

                                    if($response->getStatusCode() == 200)
                                    {
                                        Log::info('Zapier integration: webhook action successfully triggered from user_template ' . $user_template_id);
                                    }
                                    if($response->getStatusCode() == 410)
                                    {
                                        // Zapier docs: If Zapier responds with a 410 status code you should immediately remove the subscription to the failing hook (unsubscribe)
                                        $webhook->delete();
                                    }
                                    //Log::info($response->getHeaders());
                                } catch (ClientException $e) {
                                    Log::info('ERROR processing Zapier request:');
                                    Log::info($e->getMessage());
                                }
                            }

                        }else if($integrations && $integrations->type_id == 3){
                        	//integration type (3) is GotoWebinar
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                        	Log::info('GotoWebinar integration: subscription received');
                        	
                        	Log::info([ 'email' => $user_email, 'campaign_id' => $user_template->integration_id]);
                        	$json_resp = [];
                        	$client = new \GuzzleHttp\Client();
                        	$arr = [];
                        	
                        	try {
                        		$response = $client->post('https://api.citrixonline.com/G2W/rest/organizers/' . 
                        										$integrations->api_key .  
                        								  '/webinars/' . $user_template->integration_id . '/registrants', 
                        				[
                        					'headers' => [
                        						'Authorization' => 'OAuth oauth_token=' . $integrations->access_token, 
                        						'Content-Type' => 'application/json'
											],
                        					'json' => [
                        						"email"     => $user_email,
                        						"firstName" => $user_name,
                        						"lastName"  => " "
                        					],
                        					'allow_redirects' => false,
                        					'timeout' => 5
                        				]);
                        		 
                        		Log::info($response->getHeaders());
                        		$json_resp = json_decode($response->getBody(), true);
                        		 
                        	} catch (ClientException $e) {
                        		Log::info('[GOTOWEBINAR][ERROR]');
                        		Log::info($e->getMessage());
                        	}
                        	
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                        }else if($integrations && $integrations->type_id == 5){ 
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                        	Log::info([ 'email' => $user_email, 'campaign_id' => $user_template->integration_id]);
                        	$json_resp = [];
                        	$client = new \GuzzleHttp\Client();
                        	$arr = [];
                        	
                        	try {
                        		list($realApiKey, $datacenter) = explode('-', $integrations->api_key);
                        		$response = $client->post('https://' . $datacenter . '.api.mailchimp.com/3.0/lists/' . $user_template->integration_id . '/members', [
                        			'headers' => ['Authorization' => 'api-key ' . $integrations->api_key, 'Content-Type' => 'application/json'],
                        			'json' => [
                        				'email_address'  => $user_email, 
                        				'status' => 'subscribed'
									],
                        			'allow_redirects' => false,
                        			'timeout' => 5
                        		]);
                        	
                        		Log::info($response->getHeaders());
                        		$json_resp = json_decode($response->getBody(), true);
                        	
                        	} catch (ClientException $e) {
                        		Log::info('[MAILCHIMP][ERROR]');
                        		Log::info($e->getMessage());
                        	}
                        	
                        	Log::info($json_resp);
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                        } else if($integrations && $integrations->type_id == 7){ 
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                        	Log::info([ 'email' => $user_email, 'campaign_id' => $user_template->integration_id]);
                        	
                        	$json_resp = [];
                        	$client    = new \GuzzleHttp\Client();
                        	$arr       = [];
                        	$test      = true;
                        	
                        	try{
                        		
                        		$response = $client->post('http://gogvo.com/api/eresponder/add_subscriber', [
                        						'body' => [
			                        				"api_key"        => $integrations->api_key, 
													"CampaignId"     => $user_template->integration_id, 
													"FullName"       => $test ? 'testtesha' : $user_name,
													"Email"          => $test ? 'testtesha@gmail.com' : $user_email 
                        						 ],
                        						'allow_redirects' => false,
                        						'timeout' => 45
                        					]);
                        		 
                        		Log::info('=========================================================================================');
                        		Log::info($response->getHeaders());
                        		Log::info($response->getBody());
                        		Log::info('=========================================================================================');
                        		$json_resp = json_decode($response->getBody(), true);
                        		
                        	}catch(ClientException $e){
                        		Log::info('[GVO eRESPONDER PRO][ERROR]');
                        		Log::info($e->getMessage());
                        	}
                        	Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                       
                       } else if($integrations && $integrations->type_id == 8){
	                       	Log::info([ 'email' => $user_email, 'list_id' => $user_template->integration_id]);
	                       	$json_resp = [];
	                       	$client    = new \GuzzleHttp\Client();
	                       	$arr       = [];
	                       	 
	                       	try{
	                       	
	                       		$response = $client->post('https://' . $integrations->authorization . '/api/v1/list-subscribers-add', [
		                       				'body' => [
		                       					"api"            => $integrations->api_key,
	                       						"hash"           => $integrations->organizerKey,
		                       					"list_id"        => $user_template->integration_id,
		                       					"email"          => $user_name . '<' . $user_email . '>'
		                       				],
		                       				'allow_redirects' => false,
		                       				'timeout' => 45
	                       				]);
	                       		 
	                       		Log::info('=========================================================================================');
	                       		Log::info($response->getHeaders());
	                       		Log::info($response->getBody());
	                       		Log::info('=========================================================================================');
	                       		$json_resp = json_decode($response->getBody(), true);
	                       	
	                       	}catch(ClientException $e){
	                       		Log::info('[SENDLANE][ERROR]');
	                       		Log::info($e->getMessage());
	                       	}
                       } else if($integrations && $integrations->type_id == 9){ 
                       		Log::info([ 'email' => $user_email, 'list_id' => $user_template->integration_id]);
                       		$json_resp = [];
                       		$client    = new \GuzzleHttp\Client();
                       		$arr       = [];
                       		
                       		try{
                       			 
                       			$response = $client->post('https://app.webinarjam.com/api/v2/register', [
		                       					'body' => [
		                       						"api_key"        => $integrations->api_key,
		                       						"schedule"       => 0,
		                       						"webinar_id"     => $user_template->integration_id,
                       								"name"           => $user_name,
		                       						"email"          => $user_email
		                       					],
		                       					'allow_redirects' => false,
		                       					'timeout' => 45
	                       					]);
                       			 
                       			Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                       			Log::info($response->getHeaders());
                       			Log::info($response->getBody());
                       			Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
                       			$json_resp = json_decode($response->getBody(), true);
                       			 
                       		}catch(ClientException $e){
                       			Log::info('[WEBINARJAM][ERROR]');
                       			Log::info($e->getMessage());
                       		}
                       } else if($integrations && $integrations->type_id == 10){ 
                       		Log::info([ 'email' => $user_email, 'list_id' => $user_template->integration_id]);
                       		$json_resp = [];
                       		
                       		try{
                       			 
                       			\MailWizzApi_Autoloader::register();
                       			$storagePath = storage_path() . '/MailWizzApi/cache/' . $user_template->user_id;
                       			if ( !File::exists( $storagePath ) ) {
                       				File::makeDirectory( $storagePath, 0755, true);
                       			}
                       			// configuration object
                       			$config = new \MailWizzApi_Config([
		                       					'apiUrl'        => 'http://dashboard.sendreach.com/api/index.php',
		                       					'publicKey'     => $integrations->api_key,
		                       					'privateKey'    => $integrations->authorization,
	                       					 
		                       					// components
		                       					'components' => [
			                       					'cache'      => [
				                       					'class'     => 'MailWizzApi_Cache_File',
				                       					'filesPath' => $storagePath,
			                       					]
		                       					],
	                       					]);
                       			\MailWizzApi_Base::setConfig($config);
                       			
                       			 
                       			$endpoint   = new \MailWizzApi_Endpoint_ListSubscribers();
							    $response   = $endpoint->create($user_template->integration_id, [
							        'EMAIL' => $user_email,
							        'FNAME' => $user_name,
							        'LNAME' => '',
							    ]);
							    $response   = $response->body;
							    
							    Log::debug("SENDREACH CAMPAIGN LIST SUBSCRIPTION RESPONSE " . json_encode($response->toArray()));
							    
							    // if the returned status is success, we are done.
							    if ($response->itemAt('status') == 'success') {
							    	$json_resp = \MailWizzApi_Json::encode([
							    		'status'    => 'success',
							    		'message'   => 'Thank you for joining our email list. Please confirm your email address now!'
							    	]);
							    }
                       			 
                       		}catch(ClientException $e){
                       			Log::info('[WEBINARJAM][ERROR]');
                       			Log::info($e->getMessage());
                       		}
                       } else if($integrations && $integrations->type_id == 11){
	                       	Log::info([ 'email' => $user_email, 'list_id' => $user_template->integration_id]);
	                       	$json_resp = [];
	                       	$client    = new \GuzzleHttp\Client();
	                       	 
	                       	try{
	                       		$list_id = $user_template->integration_id;
	                       		$response = $client->post('https://em.fluttermail.com/admin/api.php', [
			                       				'body' 			  => [
								                       				"p[$list_id]"                 => $list_id,
								                       				"first_name"                  => $user_name,
								                       				"email"                       => $user_email,
	                       											"instantresponders[$list_id]" => 1
							                       				  ],
	                       						'query'           => [	
	                       											'api_key' 		  => $integrations->api_key,
	                       											'api_action'      => 'subscriber_add',
	                       											'api_output'      => 'json',
																  ],
			                       				'allow_redirects' => false,
			                       				'timeout' 		  => 45
		                       				]);
	                       		 
	                       		Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
	                       		Log::info('[FLUTTERMAIL][SUCCESS]');
	                       		Log::info($response->getHeaders());
	                       		Log::info($response->getBody());
	                       		Log::info('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
	                       		$json_resp = json_decode($response->getBody(), true);
	                       		 
	                       	}catch(ClientException $e){
	                       		Log::info('[FLUTTERMAIL][ERROR]');
	                       		Log::info($e->getMessage());
	                       	}
	                       	
                       }else {
                            // default integration type, use GetResponse by default

                            if ($integrations && strlen($integrations->api_key) > 2 && $user_template && strlen($user_template->integration_id) > 0) {

                                Log::info(['name' => $user_name, 'email' => $user_email, 'campaign' => ['campaignId' => $user_template->integration_id]]);
                                $json_resp = [];
                                $client = new \GuzzleHttp\Client();
                                $arr = [];
                                try {
                                    $response = $client->post('https://api.getresponse.com/v3/contacts', [
                                        'headers' => ['X-Auth-Token' => 'api-key ' . $integrations->api_key, 'Content-Type' => 'application/json'],
                                        'json' => ['name' => $user_name, 'email' => $user_email, 'dayOfCycle' => 0, 'campaign' => ['campaignId' => $user_template->integration_id]],
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
                }
            }
        } else {
            return $resp->withArray([ 'code' => 404, 'success' => false]);
        }
    }
	
	public function parseOneShopResponce(){
		$xml_content = file_get_contents("php://input");
		
		Log::info("[ALWAYS LOG ORDER INFO] !!!!!!!! ");
		Log::debug($xml_content);
		
		$requestBodyXML  = new \DOMDocument();
		$apishoppingcart = Apishoppingcart::getInstance();
		$resp            = new \EllipseSynergie\ApiResponse\Laravel\Response(new \League\Fractal\Manager);
		
		//set api oneshoppingcart configuration
		$apishoppingcart->setMerchandId(config('oneshop.merchant_id'));
		$apishoppingcart->setmerchantKey(config('oneshop.merchant_key'));
		
		if (!empty($xml_content) && $requestBodyXML->loadXML($xml_content) == true) {
			Log::info("[XML CONTENT FROM ONE SHOPPING CART] OK!!!!!!!! ");
			$notificationType = $requestBodyXML->documentElement->nodeName;
			$transaction_id   = $requestBodyXML->getElementsByTagName('Token')->item(0)->nodeValue;
			$order_info       = $apishoppingcart->GetOrderById($transaction_id);
			
			Log::info("[ALWAYS LOG ORDER INFO] !!!!!!!! ");
			Log::debug(json_encode(simplexml_load_string($order_info)));
			Log::debug('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			Log::debug("Transaction INFO " . $xml_content);
			Log::debug("ORDER TYPE " . $notificationType);
			Log::debug("TRANSACTION ID " . $transaction_id);
			Log::debug('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
			
			switch ($notificationType) {
				case "NewOrder":
					$order_info = simplexml_load_string($order_info);
					if ($order_info->attributes()->success == "true") {
						$order_info = $order_info->OrderInfo;
						
						Log::debug(json_encode($order_info));
						
						if ($order_info->ClientId) {
							$client_info = $apishoppingcart->GetClientById($order_info->ClientId);
							$client_info = simplexml_load_string($client_info);

							if ($client_info->attributes()->success == "true") {
								$client_info = $client_info->ClientInfo;
								
								Log::debug(json_encode($client_info));
								
								//Test for reccurring billing
								Log::debug('client 1shop identifier = ' . $order_info->ClientId . ' related to the mediam email user = ' . $client_info->Email);
								
								//first order ?
								$user = User::where('email', '=', $client_info->Email)->first();
								if (!$user && strtolower(trim($order_info->OrderStatusType)) == 'accepted') {
									if($order_info->LineItems->count() == 1){
										Log::info("[NEW USER CREATED] ");
										foreach($order_info->LineItems as $key => $itemInfos){
											$productInfoId = json_decode($itemInfos->LineItemInfo->ProductId);
										}
										$package = Package::where('jvzoo_id', 'regexp' , "[[:<:]]" . $productInfoId . "[[:>:]]")->first();
										if (!empty($package)) {
											$pass = ucfirst($client_info->FirstName) . '44#';
											Log::debug("[Password ] " . $pass);
											$user = User::create([
														'name'     => ($client_info->FirstName . ' ' . $client_info->LastName),
														'email'    => $client_info->Email,
														'password' => bcrypt($pass),
														'role_id'  => 4,
											]);
											$user_profile = UserProfile::create([
														'user_id'               => $user->id,
														'max_campaigns'         => 0,
														'split_testing'         => 0,
														'redirect_page'         => 0,
														'embed'                 => 0,
														'hosted'                => 0,
														'package_id'            => $package->id,
														'analytics_retargeting' => 0,
											]);

											$transport = SmtpTransport::newInstance('smtp.sendgrid.net', 587, 'tls');
											$transport->setUsername('marketerscrm');
											$transport->setPassword('RWawM4VHke46cm7zfCrGw9SFEE08MxF9');
											$gmail = new Swift_Mailer($transport);
											\Mail::setSwiftMailer($gmail);
											$name  = !empty($client_info->FirstName) ? $client_info->FirstName : $client_info->LastName;
											$email = "" . $client_info->Email;
											\Mail::send('emails.create_new_user', ['FirstName' => $name, 'email' => $client_info->Email, 'password' => $pass], function ($message) use ($email) {
												$message->from('support@mobileoptin.com', 'MobileOptin.com');
												$message->to($email)->subject('Your MobileOptin.com Login');
											});

											return $resp->withArray([ 'code' => 200, 'success' => true, 'name' => ($client_info->FirstName . ' ' . $client_info->LastName)]);
										}else {
											return $resp->withArray([ 'code' => 404, 'success' => false, 'msg' => 'no package']);
										}
									}else
										return $resp->withArray([ 'code' => 404, 'success' => false, 'msg' => 'should be one items info']);		
								} else if($user) {
									if(strtolower(trim($order_info->OrderStatusType)) == 'accepted'){
										$user->role_id = 4;
										$user->save();
										return $resp->withArray([ 'code' => 200, 'success' => true]);
									}else{
										$user->role_id = 3;
										$user->save();
										return $resp->withArray([ 'code' => 200, 'success' => true]);
									}
								}else{
									return $resp->withArray([ 'code' => 404, 'success' => false, 'msg' => 'user already exists']);
								}
								//reccurring order 
							}
						}
					}
				break;
				default:
				break;
			}
		}
    }

}