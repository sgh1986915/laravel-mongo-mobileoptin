<?php
namespace MobileOptin\Http\Controllers;

use Htmldom;
use Illuminate\Http\Request;
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

class DomainsController extends Controller {

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
        $domains = Domains::where('user_id', '=', $user_id)->where('active', 0)->get();
        
        $ip_app = gethostbyname('app.mobileoptin.com');     
        foreach ($domains as $domain) {
            $ip_new = gethostbyname($domain->name);
            if ($ip_app == $ip_new) {                  
                $domain->active = 1;
                $domain->save();
                $s = shell_exec('sudo /etc/init.d/httpd graceful');
            }
        }

        \SEOMeta::setTitle('Domains - page ' . ( \Input::get('page') ? \Input::get('page') : 1 ));
        \SEOMeta::setDescription('meta desc');
        \SEOMeta::addKeyword([ 'key1', 'key2', 'key3']);


        $data['domains'] = Domains::where('user_id', '=', $user_id)->paginate(10)->setPath('domains');

        return view('domains.list', $data);
    }

    public function add() {

        $data = array();
        \SEOMeta::setTitle('Add - Domain ');
        \DB::enableQueryLog();


        $data['domain'] = new \stdClass();
        $data['domain']->id = 0;
        $data['domain']->name = \Input::old('name');
        $data['domain']->active = \Input::old('active');
        $data['domain']->status = \Input::old('status');
        $data['domain']->user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


        return view('domains.add_edit', $data);
    }

    public function edit($CId) {
        \SEOMeta::setTitle('Edit - Domain');


        try {
            $domain = Domains::where('id', '=', $CId)->firstOrFail();
        } catch (\Exception $e) {
            return redirect()->route('domains')->withError($e . 'Domain not found or you do not have permissions');
        }

        return view('domains.add_edit', ['domain' => $domain]);
    }

    public function upsert() {
        $validator = \Validator::make(\Input::only('id', 'name'), [
                    'id' => 'required|integer',
                    'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            if (!checkdnsrr(\Input::get('name'), 'A')) {
                return redirect()->back()->withInput()->withErrors(['name' => 'Invalid domain name']);
            }

            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
            $a = \File::get(base_path() . '/ServerAlias.conf');

            if (\Input::get('id') > 0) {
                $new_camp = Domains::where('id', '=', \Input::get('id'))->first();
                $old_name = $new_camp->name;
                if (\Input::get('name') != $old_name) {
                    if (strpos($a, 'ServerAlias ' . $old_name) !== false && checkdnsrr(\Input::get('name'), 'A')) {
                        $new_camp->name = \Input::get('name');
                        $contents = str_replace('ServerAlias ' . $old_name, 'ServerAlias ' . \Input::get('name'), $a);
                        $a = \File::put(base_path() . '/ServerAlias.conf', $contents);

                        $ip_app = gethostbyname('app.mobileoptin.com');
                        $ip_new = gethostbyname($new_camp->name);
                        $new_camp->status = 2;
                        if ($ip_app == $ip_new) {
                            $new_camp->active = 1;
                            $s = shell_exec('sudo /etc/init.d/httpd graceful');
                        } else {
                            $new_camp->active = 0;
                        }
                        $new_camp->save();
                    }
                }

                if (!$new_camp) {
                    return redirect()->back()->withInput()->withError('Domain not found');
                }
            } else {
                $new_camp = new Domains();
                $new_camp->user_id = $user_id;
                $new_camp->name = \Input::get('name');
                $new_camp->status = 2;

                if (checkdnsrr($new_camp->name, 'A') && strpos($a, 'ServerAlias ' . $new_camp->name) == false) {
                    $ip_app = gethostbyname('app.mobileoptin.com');
                    $ip_new = gethostbyname($new_camp->name);
                    if ($ip_app == $ip_new) {
                        $new_camp->active = 1;
                        $s = shell_exec('sudo /etc/init.d/httpd graceful');
                    } else {
                        $new_camp->active = 0;
                    }

                    \File::append(base_path() . '/ServerAlias.conf', 'ServerAlias ' . $new_camp->name . PHP_EOL);
                    $new_camp->save();
                } else {

                    return redirect()->back()->withInput()->withErrors(['name' => 'Invalid domain name']);
                }
            }

            return redirect()->route('domains')->withNotify('Domain saved');
        }
    }

    public function delete($CId) {
        try {
            if (Auth::user()->getOwner() == false) {
                $domain = Domains::where('user_id', '=', Auth::id())->where('id', '=', $CId)->firstOrFail();
                $a = \File::get(base_path() . '/ServerAlias.conf');
                if (strpos($a, 'ServerAlias ' . $domain->name) !== false) {
                    $contents = str_replace('ServerAlias ' . $domain->name . PHP_EOL, '', $a);
                    $a = \File::put(base_path() . '/ServerAlias.conf', $contents);
                }

                $domain->forceDelete();
                UserDomains::where('domain_id', '=', $CId)->delete();
                return redirect()->route('domains')->withSuccess('Domain Deleted');
            }
        } catch (\Exception $e) {
            
        }
        return redirect()->route('domains')->withError('Domain not removed ');
    }

    public function change_satus($CId, $new_status) {
        try {
            if (Auth::user()->getOwner() == false) {
                $domain = Domains::where('user_id', '=', Auth::id())->where('id', '=', $CId)->firstOrFail();
            } else {
                $domain = Domains::where('user_id', '=', Auth::user()->getOwner())->where('id', '=', $CId)->firstOrFail();
            }

            if ($new_status) {
                $domain->activated_on = date('Y-m-d H:i:s');
            } else {
                $domain->deactivated_on = date('Y-m-d H:i:s');
            }
            $domain->active = $new_status;
            $domain->save();

            return redirect()->route('domains')->withSuccess('Domain status changed');
        } catch (\Exception $e) {
            return redirect()->route('domains')->withError('Status not updated ');
        }
    }

    public function save_campaigns_assinged() {
        $cid = \Input::get('id');
        $uid = \Input::get('assing_other');

        $user = User::with('profile', 'allowed_campaigns', 'role', 'owner')->whereHas('owner', function ( $query ) {
                    $query->where('owner_id', '=', Auth::id());
                })->whereHas('allowed_campaigns', function ( $query ) use ( $cid ) {
                    $query->where('campaign_id', '=', $cid);
                }, ' != ')->where('id', '=', $uid)->first();

        if (isset($user->id)) {

            $ua = new UserAllowedCampaigns();
            $ua->user_id = $uid;
            $ua->campaign_id = $cid;
            $ua->save();
            return redirect()->route('campaigns_assinged', [ 'id' => $cid])->withSuccess('user saved ');
        }
        return redirect()->route('campaigns_assinged', [ 'id' => $cid])->withError('user not added ');
    }

}
