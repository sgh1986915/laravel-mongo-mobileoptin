<?php

namespace MobileOptin\Http\Controllers;

//use Input;
use Clockwork\Clockwork;
use Htmldom;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignStats;
use MobileOptin\Models\CampaignsTemplates;
use MobileOptin\Models\SplitTestingStats;
use MobileOptin\Models\TemplatesGroups;
use MobileOptin\Models\User;
use MobileOptin\Models\UserAllowedCampaigns;
use MobileOptin\Models\UserProfile;
use MobileOptin\Models\UserTemplates;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Html\FormBuilder;
use Illuminate\Support\Facades\File;

class CampaignsController extends Controller {
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


        \SEOMeta::setTitle('Campaigns - page ' . ( \Input::get('page') ? \Input::get('page') : 1 ));

        \SEOMeta::setDescription('meta desc');
        \SEOMeta::addKeyword([ 'key1', 'key2', 'key3']);

        $data['has_embed'] = Auth::user()->getProfileOption('embed');
        $data['has_hosted'] = Auth::user()->getProfileOption('hosted');

        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
        
        if($user_id == Auth::id()){
        	$data['campaigns'] = Campaigns::where('user_id', '=', $user_id)->with('template')->paginate(10)->setPath('campaigns');
        }else{
        	
			$data['campaigns'] = Campaigns::with('template')->whereHas('assigned', function ( $query ) {
					$query->where('user_id', Auth::id());
				})->paginate(10)->setPath('campaigns');
        }
        

        $cids = [];

        foreach ($data['campaigns'] as $c) {
            $cids[] = $c->id;
        }

        $data['splitTestStats'] = SplitTestingStats::getMultiBasicInfo($cids, '-30 days', 'now');

        //lotery to delete old templates
        UserTemplates::cleanOld();


        //        \Clockwork::info( $splt );
        return view('campaigns.list', $data);
    }

    public function add(Request $request) {

        if (Auth::user()->getOwner() == false) {
            $campaign_limit = Auth::user()->campaignLimit();
        } else {

            $own_profile = UserProfile::where('user_id', '=', Auth::user()->getOwner())->first();
            if ($own_profile && $own_profile->package_id != 0) {
                $max_c = $own_profile->package->max_campaigns;
            } else {
                $max_c = $own_profile->max_campaigns;
            }

            $campaign_limit = Campaigns::where('user_id', '=', Auth::user()->getOwner())->count() < $max_c;
        }

        $user_id = \Auth::user()->getOwner() ? \Auth::user()->getOwner() : \Auth::id();
        session_start();
        $_SESSION["userID"] = '/public/upload/user_' . $user_id;

        if ($campaign_limit) {

            $data = array();
            \SEOMeta::setTitle('Add - Campaigns ');
            \DB::enableQueryLog();
            $rtg = Auth::user()->allowed_groups()->with('tplgroups.templates')->get();

            $availabl_template_groups = [];

            $templates_groups = [];
            foreach ($rtg as $a) {
                foreach ($a->tplgroups as $b) {
                    $availabl_template_groups[] = $b->id;
                    foreach ($b->templates as $tmp) {
                        if ($tmp->active == 1) {
                            $templates_groups[$b->name][] = $tmp;
                        }
                    }
                }
            }

            $data['templates'] = $templates_groups;
            $dis_templates_groups = [];
//            if ( !empty( $availabl_template_groups ) ) {
//                $ds_templategroups = TemplatesGroups::whereNotIn( 'id', $availabl_template_groups )->with( 'templates' )->get();
//
//
//                foreach ( $ds_templategroups as $b ) {
//
//                    foreach ( $b->templates as $tmp ) {
//                        if ( $tmp->active == 1 ) {
//                            $dis_templates_groups[ $b->name ][ ] = $tmp;
//                        }
//                    }
//
//                }
//            }
            $data['templates_disable'] = $dis_templates_groups;


            $data['campaign'] = new \stdClass();
            $data['campaign']->id = 0;
            $data['campaign']->name = \Input::old('name');

            $data['campaign']->slug = \Input::old('slug');
            $data['campaign']->user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
            $data['campaign']->template = [];
            $data['campaign']->ao_clicks = '';
            $data['campaign']->ao_threshold = '';
            $data['campaign']->domain_id = '';
            $data['campaign']->enable_optimization = 0;
            $data['campaign']->enable_retargeting = 0;
            $data['campaign']->enable_return_redirect = 0;
            $data['campaign']->redirect_return_after = 0;
            $data['campaign']->redirect_return_url = '';
            $data['campaign']->analitics_and_retargeting = '';

            $old_ids = [];
            $pst = \Input::old('template_id');
            
            if (!empty($pst)) {
                foreach ($pst as $a) {
                    $old_ids[] = $a;
                }
                if (!empty($old_ids)) {
                    $data['campaign']->template = UserTemplates::whereIn('id', $old_ids)->get();
                }
            }
            
            if (Auth::user()->getOwner() !== false) {
                $edior_id = Auth::user()->getOwner();
            } else {
                $edior_id = Auth::id();
            }
            
            $data['integrations'] = [];
            $data['original_template_id'] = 0;
            $data['can_analytics_retargeting'] = Auth::user()->getProfileOption('analytics_retargeting') ? '' : 'disabled="disabled"';
            $data['domains'] = [0 => 'app.mobileoptin.com'];
            $data['domains'] += \MobileOptin\Models\Domains::where('user_id', '=', $edior_id)->where('status', '=', '2')->where('active', '=', '1')->lists('name', 'id');
            $data['can_redirect'] = Auth::user()->getProfileOption('redirect_page') ? '' : 'disabled="disabled"';

            $data['contact_types'] = [0=>'Manual'];
            $data['contact_types'] += \MobileOptin\Models\IntegrationsUser::where('user_id', $edior_id)->lists('name', 'id');

            $data['user_integrations'] = \MobileOptin\Models\IntegrationsUser::where('user_id', $edior_id)->get();
            
            $request->session()->set('_old_input', array());
            return view('campaigns.add_edit', $data);
        }
        return redirect()->route('campaigns')->withError('You have used up all your campaigns !');
    }

    public function edit($CId) {

        $data = array();
        \SEOMeta::setTitle('Add - Campaigns ');

        $user_id = \Auth::user()->getOwner() ? \Auth::user()->getOwner() : \Auth::id();
        session_start();
        $_SESSION["userID"] = '/public/upload/user_' . $user_id;

        $rtg = Auth::user()->allowed_groups()->with('tplgroups.templates')->get();

        $availabl_template_groups = [];

        $templates_groups = [];
        foreach ($rtg as $a) {
            foreach ($a->tplgroups as $b) {
                $availabl_template_groups[] = $b->id;
                foreach ($b->templates as $tmp) {
                    if ($tmp->active == 1) {
                        $templates_groups[$b->name][] = $tmp;
                    }
                }
            }
        }
        $data['templates'] = $templates_groups;
        $dis_templates_groups = [];
//        if ( !empty( $availabl_template_groups ) ) {
//            $ds_templategroups = TemplatesGroups::whereNotIn( 'id', $availabl_template_groups )->with( 'templates' )->get();
//
//
//            foreach ( $ds_templategroups as $b ) {
//
//
//                foreach ( $b->templates as $tmp ) {
//                    if ( $tmp->active == 1 ) {
//                        $dis_templates_groups[ $b->name ][ ] = $tmp;
//                    }
//                }
//            }
//        }
          $data['integrations'] = [];
        $data['templates_disable'] = $dis_templates_groups;

        try {
            if (Auth::user()->getOwner() !== false) {
                $edior_id = Auth::user()->getOwner();
            } else {
                $edior_id = Auth::id();
            }
            $data['can_analytics_retargeting'] = Auth::user()->getProfileOption('analytics_retargeting') ? '' : 'disabled="disabled"';

            $data['can_redirect'] = Auth::user()->getProfileOption('redirect_page') ? '' : 'disabled="disabled"';
            $data['campaign'] = Campaigns::with('template', 'template.org_template')->where('user_id', '=', $edior_id)->where('id', '=', $CId)->firstOrFail();
            $data['domains'] = [0 => 'app.mobileoptin.com'];
            $data['domains'] += \MobileOptin\Models\Domains::where('user_id', '=', $edior_id)->where('status', '=', '2')->where('active', '=', '1')->lists('name', 'id');

            $data['original_template_id'] = 0;
        } catch (\Exception $e) {
            return redirect()->route('campaigns')->withError($e . 'Campaign not found or you do not have permissions');
        }

        $data['contact_types'] = [0=>'Manual'];
        $data['contact_types'] += \MobileOptin\Models\IntegrationsUser::where('user_id', $edior_id)->lists('name', 'id');
#dd($data['contact_types']);
        $data['user_integrations'] = \MobileOptin\Models\IntegrationsUser::where('user_id', $edior_id)->get();
        return view('campaigns.add_edit', $data);
    }

    public function upsert() {
    	
        $validator = \Validator::make(\Input::only('id', 'name', 'slug'), [
                    'id' => 'required|integer',
                    'name' => 'required',
                    'slug' => 'required',
        ]);

        if ($validator->fails()) {
            // The given data did not pass validation
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
        	
            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            if (\Input::get('id') > 0) {
                $new_camp = Campaigns::where('id', '=', \Input::get('id'))->where('user_id', '=', Auth::id())->first();
                if (!$new_camp) {
                    return redirect()->back()->withInput()->withError('Campaign not found');
                }
            } else {
                $new_camp = new Campaigns();

                $new_camp->user_id = $user_id;
            }

            $new_camp->name = \Input::get('name');
            $new_camp->slug = str_slug(\Input::get('slug'));
            $new_camp->domain_id = str_slug(\Input::get('domain_id'));
            $new_camp->ao_clicks = str_slug(\Input::get('ao_clicks'));
            $new_camp->ao_threshold = str_slug(\Input::get('ao_threshold'));
            $new_camp->enable_optimization = \Input::get('enable_optimization') == null ? 0 : 1;
            $new_camp->enable_retargeting = \Input::get('enable_retargeting') == null ? 0 : 1;
            $new_camp->enable_return_redirect = \Input::get('enable_return_redirect') == null ? 0 : 1;
            $new_camp->analitics_and_retargeting = (\Input::get('analitics_and_retargeting') == null) ? ' ' : \Input::get('analitics_and_retargeting');
            $new_camp->redirect_return_after = (\Input::get('redirect_return_after') == null) ? '' : \Input::get('redirect_return_after');
            $new_camp->redirect_return_url = (\Input::get('redirect_return_url') == null) ? '' : \Input::get('redirect_return_url');
            $new_camp->active = 1;

            $new_camp->save();


            $user_template = \Input::get('user_template');
            $percent_container = \Input::get('percent_container');

            if (!empty($user_template)) {
                foreach ($user_template as $k => $u_tid) {
                    $u_tmpl = UserTemplates::find($u_tid);
                    if ($u_tmpl) {
                        //find and now edit
                        $u_tmpl->affect_percentile = $percent_container[$k];
                        $u_tmpl->campaign_id = $new_camp->id;
                        $u_tmpl->save();
                    }
                }
            }
            $for_deletion = UserTemplates::where('campaign_id', '=', $new_camp->id)->whereNotIn('id', $user_template)->get();
            if ($for_deletion) {
                foreach ($for_deletion as $delte_this) {
                    $delte_this->delete();
                }
            }

            if (Auth::user()->getOwner() !== false) {

                $ua = new UserAllowedCampaigns();
                $ua->user_id = Auth::id();
                $ua->campaign_id = $new_camp->id;
                $ua->save();
            }

            return redirect()->route('campaigns')->withNotify('Campaign saved');
        }
    }

    public function delete($CId) {
        try {
            if (Auth::user()->getOwner() == false) {
                $Campaign = Campaigns::where('user_id', '=', Auth::id())->where('id', '=', $CId)->firstOrFail();
                $Campaign->forceDelete();

                SplitTestingStats::where('campaign_id', '=', $CId)->delete();
                CampaignStats::where('campaign_id', '=', $CId)->delete();
                UserTemplates::where('campaign_id', '=', $CId)->delete();
                return redirect()->route('campaigns')->withSuccess('Campaign Deleted');
            }
        } catch (\Exception $e) {
            
        }
        return redirect()->route('campaigns')->withError('Campaign not removed ');
    }

    public function change_satus($CId, $new_status) {
        try {
            if (Auth::user()->getOwner() == false) {
                $Campaign = Campaigns::where('user_id', '=', Auth::id())->where('id', '=', $CId)->firstOrFail();
            } else {
                $Campaign = Campaigns::where('user_id', '=', Auth::user()->getOwner())->where('id', '=', $CId)->firstOrFail();
            }

            if ($new_status) {
                $Campaign->activated_on = date('Y-m-d H:i:s');
            } else {
                $Campaign->deactivated_on = date('Y-m-d H:i:s');
            }
            $Campaign->active = $new_status;
            $Campaign->save();

            return redirect()->route('campaigns')->withSuccess('Campaign status changed');
        } catch (\Exception $e) {
            return redirect()->route('campaigns')->withError('Status not updated ');
        }
    }

    public function show_item($campaign_id, $slug = '', Request $request) {

        $request_id = \Request::ip();
        $campaign_clicks2 = SplitTestingStats::where('campaign_id', intval($campaign_id))
                        ->where('id_address', $request_id)->get();

        $data = [ 'mailto_link' => '', 'redirect_aft' => '', 'therms_of_link' => '', 'privacy_link' => '', 'contact_us_link' => ''];

        try {
            $campaign = Campaigns::with('template')->
                            where('id', '=', $campaign_id)->
                            where('active', '=', 1)->
                            whereHas('template', function ( $query ) {
                                $query->where('affect_percentile', '>', 0);
                            })->first();


            if ($campaign) {

                $active_templates = [];

                foreach ($campaign->template as $ct) {
                    $active_templates[] = intval($ct->id);
                }

                // pull data from mongo
                $campaign_clicks = CampaignStats::raw(function ( $collection ) use ( $campaign_id, $active_templates ) {
                            return $collection->aggregate([
                                        [ '$match' => [
                                                'campaign_id' => intval($campaign_id),
                                                'template' => [ '$in' => $active_templates],
                                            ]],
                                        [
                                            '$group' => [
                                                '_id' => '$template',
                                                'total' => [
                                                    '$sum' => 1
                                                ]
                                            ]
                                        ]
                            ]);
                        });
                $template_stats = $campaign_clicks['result'];


                $tmpstats = SplitTestingStats::raw(function ( $collection ) use ( $campaign_id, $active_templates ) {
                            return $collection->aggregate([
                                        [ '$match' => [
                                                'campaign_id' => intval($campaign_id),
                                                'template' => [ '$in' => $active_templates],
                                            ]],
                                        [
                                            '$group' => [
                                                '_id' => [ 'event' => '$event', 'template' => '$template'],
                                                'total' => [
                                                    '$sum' => 1
                                                ]
                                            ]
                                        ]
                            ]);
                        });


                // auto optimisation of campaigns start
                $tempalte_for_auto_optimisation = [];
                if ($campaign_clicks['ok'] == 1) {
                    $tce = 0; //total campaign events triggered
                    foreach ($campaign_clicks['result'] as $ceptr) {
                        $tce += $ceptr['total'];
                    }


                    $event_per_template_raw = []; // number of events triggered by template;


                    foreach ($tmpstats['result'] as $ceptr) {
                        $event_per_template_raw[$ceptr['_id']['template']][$ceptr['_id']['event']] = + $ceptr['total'];
                    }

                    $tmp_with_af_perc = [];
                    $tmp_with_af_perc_new = [];
                    foreach ($campaign->template as $tmp) {
                        if ($tmp->affect_percentile > 0) {
// sercul                            
                            if ($tmp->affect_percentile / 100 != 0) {
                                $tmp_with_af_perc[$tmp->id] = 1 - ( $tmp->affect_percentile / 100 );
                            } else {
                                $tmp_with_af_perc[$tmp->id] = 0.5;
                            }
                            $tmp_with_af_perc_new[$tmp->id] = 0.5;
                        }
                    }

                    $engagement_per_tmp = [];
                    $engagement_per_tmp_new = [];

                    foreach ($event_per_template_raw as $tmp_id => $tmp_e_t) {
                        // percent of user taht clicked someware
                        $nouna = ( isset($tmp_e_t['navigate']) && isset($tmp_e_t['page_open']) ) ? ( ( $tmp_e_t['navigate'] / $tmp_e_t['page_open'] ) * 4 ) : 0;

                        // percent of user that read the page
                        $noure = (isset($tmp_e_t['read']) && isset($tmp_e_t['page_open'])) ? ( $tmp_e_t['read'] / $tmp_e_t['page_open'] ) * 2 : 0;

                        // calculate the percentage
                        $engagement_per_tmp_new[$tmp_id] = (isset($tmp_e_t['navigate']) && isset($tmp_e_t['page_open'])) ? round($tmp_e_t['navigate'] / $tmp_e_t['page_open'] * 100) : 0;
                        $engagement_per_tmp[$tmp_id] = ( round(( $noure + $nouna ) / 2, 3) * 100 );
                    }



//                    foreach ( $ceptnum as $k => $v ) {
//                        $ceptper [ $k ] = round( $v / $tce, 3 ) * 100;
//                    }


                    if ($campaign->ao_clicks > 0 && $campaign->ao_threshold > 0 && $tce > 0) {

                        if ($campaign->ao_clicks % $tce == 0) {

                            foreach ($engagement_per_tmp as $tid => $evinp) {
                                // for each template as event in percent

                                if ($campaign->ao_threshold > $evinp) {
                                    $tempalte_for_auto_optimisation[$tid] = true;
                                }
                            }
                        }
                    }
                    if (count($active_templates) == count($tempalte_for_auto_optimisation)) {
                        $tempalte_for_auto_optimisation = [];
                    }

//                    \Clockwork::info( $engagement_per_tmp );
//                    \Clockwork::info( $tmp_with_af_perc );
                }

                $total_show = 0;
                foreach ($template_stats as $tmprow) {
                    $total_show += $tmprow['total'];
                }
                $variations_real = [];
                $variations_should_be = [];
                // get the stats from mogno for all campaing in the template as they where viewed
                foreach ($template_stats as $tmprow) {
                    $variations_real[$tmprow['_id']] = round($tmprow['total'] / $total_show, 3) * 100;
                }
                // all template for that cmapaing with the perentile of how they should be shown
                foreach ($campaign->template as $tmp) {
                    if ($tmp->affect_percentile > 0) {
                        if (isset($tempalte_for_auto_optimisation[$tmp->id])) {
                            $ustt = UserTemplates::find($tmp->id);
                            if ($ustt) {
                                $ustt->affect_percentile = 0;
                                $ustt->save();
                            }
                        } else {
                            $variations_should_be[$tmp->id] = $tmp->affect_percentile;
                        }
                    }
                }

                // auto optmisation end
                // remove from calulations the templates that do not exist any more
                foreach ($variations_real as $vrid => $vrv) {
                    if (!isset($variations_should_be[$vrid])) {
                        unset($variations_real[$vrid]);
                    }
                }

                // fill mising stats for tempalte with empty values
                foreach ($variations_should_be as $k => $v) {
                    if (!isset($variations_real[$k])) {
                        $variations_real[$k] = 0;
                    }
                }


                // sercul new winner


                $flag = false;
                foreach ($engagement_per_tmp_new as $k => $v) {
                    if ($v > $campaign->ao_threshold) {
                        $flag = true;
                    }
                }
                if ($flag == true && $tce >= $campaign->ao_clicks && $campaign->enable_optimization == 1) {
                    $max = 0;
                    $max_key = 0;
                    foreach ($engagement_per_tmp_new as $k => $v) {
                        if ($v > $max) {
                            $max_key = $k;
                            $max = $v;
                        }
                    }

                    foreach ($campaign->template as $rtmp) {
                        if ($max_key == $rtmp->id) {
                            $winer = $rtmp;
                            $winer->affect_percentile = 100;
                            $winer->save();
                        } else {
                            $rtmp->affect_percentile = 0;
                            $rtmp->save();
                        }
                    }
                } else {
//                    array:2 [▼
//  748 => 14.3
//  738 => 85.7
//]
                    // find the wining template
                    foreach ($variations_should_be as $k => $v) {
                        if (isset($variations_real[$k])) {


                            if ($variations_real[$k] < $v && $v > 0) {
                                foreach ($campaign->template as $rtmp) {
                                    if ($k == $rtmp->id) {
                                        $winer = $rtmp;
                                    }
                                }
                            }
                        }
                    }
                }

                if (!isset($winer)) {
                    $winer = $campaign->template->first();
                }

                if (isset($winer) && $winer->affect_percentile > 0) {

                    if ($winer->contact_type == 0) {
                        $email = $winer->notification_email;
                    } else {
                        $email = $winer->integration_id . 'temp' . $winer->id . '@mobileresponses.com';
                    }

                    $data['mailto_link'] = 'mailto:' . $email . '?subject=' . rawurlencode($winer->email_subject) .
                            '&body=' . rawurlencode($winer->email_message);

                    $allowed_custom_url_params = \Config::get('redirect_allowed');
                    $qstring = '';
//                    $rfrsa                     = [ ];

                    foreach ($request->query() as $qsk => $qsv) {
                        if (in_array($qsk, $allowed_custom_url_params) !== false) {
                            if (stripos($winer->redirect_after, $qsk) !== false) {
//                                $qstring .= trim($qsk) . '=' . trim($qsv);
//                                $rfrsa[ ] = $qsk;
                                $winer->return_redirect = str_replace('[' . $qsk . ']', trim($qsv), $winer->return_redirect);
                                $winer->redirect_after = str_replace('[' . $qsk . ']', trim($qsv), $winer->redirect_after);
                            }
                        }
                    }

                    $winer->redirect_after = preg_replace('#(\[)(.*)(\])#si', '', $winer->redirect_after);
                    $winer->return_redirect = preg_replace('#(\[)(.*)(\])#si', '', $winer->return_redirect);

                    if (!empty($winer->redirect_after)) {
                        $data['redirect_aft'] = urldecode($winer->redirect_after . $qstring);
                        $data['return_redirect'] = urldecode($winer->return_redirect . $qstring);
                    }
                    CampaignStats::record_event($campaign->id, $winer->id, $winer->original_template_id);

                    if (isset($winer->body) && !empty($winer->body)) {

                        $data['therms_of_link'] = $winer->terms;
                        $data['privacy_link'] = $winer->privacy;
                        $data['contact_us_link'] = $winer->contact_us;
                        $data['title'] = $campaign->title;
                        $data['template_id'] = $winer->id;
                        $data['campaign_id'] = $campaign->id;

                        $user_profile = UserProfile::where('user_id', '=', $campaign->user_id)->first();
                        
                        $data['analitics_and_retargeting'] = '';
                        if($user_profile->package){
                        	if($user_profile->package->analytics_retargeting){
                        		$data['analitics_and_retargeting'] = $campaign->analitics_and_retargeting;
                        	}
                        }else  if ($user_profile->analytics_retargeting) {
                        	$data['analitics_and_retargeting'] = $campaign->analitics_and_retargeting;
                        }
                        
                        $html = new Htmldom($winer->body);

                        // Find all images
                        foreach ($html->find('span') as $element) {
                            if (isset($element->class)) {
                                if ($element->class == 'link') {
                                    $text = $element->innertext;
                                    $element->outertext = '<a href="{{$mailto_link}}" data-onclick="' . $data['redirect_aft'] . '" target="_blank">' . $text . '</a>';
                                }
                            }
                        }

                        foreach ($html->find('meta') as $meta_elem) {

                            if ($meta_elem->name == 'template_id') {
                                $meta_elem->content = $winer->id;
                            }
                            if ($meta_elem->name == 'campaign_id') {
                                $meta_elem->content = $winer->campaign_id;
                            }
                        }
                        $winer->body = $html->save();

                        if ($campaign->enable_return_redirect == 1 && count($campaign_clicks2) >= $campaign->redirect_return_after && $campaign->redirect_return_url != ' ' && strlen($campaign->redirect_return_url) > 3) {
                            return redirect()->away($campaign->redirect_return_url);
                        } else {
                            return \StringView::make(
                                            array(
                                        // this actual blade template
                                        'template' => $winer->body . '{!! $analitics_and_retargeting !!}',
                                        // this is the cache file key, converted to md5
                                        'cache_key' => 'campaign_campaign_preview_' . md5($campaign_id . "_" . $winer->id),
                                        // timestamp for when the template was last updated, 0 is always recompile
                                        'updated_at' => time()
                                            ), $data
                            );
                        }
                    }
                }
            } else {
                return '<h5 style="margin: 10px auto 10px">Sorry this campaign has been disabled.</h5>';
            }
        } catch (Exception $e) {
            return '<h5 style="margin: 10px auto 10px">Sorry this campaign has been disabled.</h5>';
        }
    }

    public function add_template($campaign_id) {
        try {
            $new_tmp_id = 0;


            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            $tmp_template = new UserTemplates();
            $tmp_template->campaign_id = $campaign_id;
            $tmp_template->user_id = $user_id;
            $tmp_template->body = '';
            $tmp_template->name = '';
            $tmp_template->terms = '';
            $tmp_template->privacy = '';
            $tmp_template->return_redirect = '';
            $tmp_template->contact_us = '';
            $tmp_template->affect_percentile = '';

            $tmp_template->redirect_after = '';
            $tmp_template->notification_email = '';
            $tmp_template->integration_id = '';
            $tmp_template->contact_type = 0;
            $tmp_template->email_subject = '';
            $tmp_template->email_message = '';
            $tmp_template->name = '';


            $tmp_template->save();
            $new_tmp_id = $tmp_template->id;


            $any_existinttmp = UserTemplates::where('campaign_id', '=', $campaign_id)->first();

            if ($any_existinttmp) {

                $tmp_template->terms = $any_existinttmp->terms;
                $tmp_template->privacy = $any_existinttmp->privacy;
                $tmp_template->privacy = $any_existinttmp->privacy;
                $tmp_template->contact_us = $any_existinttmp->contact_us;

                $tmp_template->integration_id = $any_existinttmp->integration_id;
                $tmp_template->contact_type = $any_existinttmp->contact_type;


                $tmp_template->return_redirect = $any_existinttmp->return_redirect;
                $tmp_template->notification_email = $any_existinttmp->notification_email;
                $tmp_template->email_subject = $any_existinttmp->email_subject;
                $tmp_template->email_message = $any_existinttmp->email_message;
                $tmp_template->save();
            }


            return response()->json([ 'tmpl_id' => $new_tmp_id]);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }
    
    public function add_change_template_modal($template_id){
    	try {
    		$rtg = Auth::user()->allowed_groups()->with('tplgroups.templates')->get();
    		$availabl_template_groups = [];
    		$templates_groups = [];
    		foreach ($rtg as $a) {
    			foreach ($a->tplgroups as $b) {
    				$availabl_template_groups[] = $b->id;
    				foreach ($b->templates as $tmp) {
    					if ($tmp->active == 1) {
    						$templates_groups[$b->name][] = $tmp;
    					}
    				}
    			}
    		}
    		$tabHeader  = '';
    		$tabContent = '';
    		$contentTab = '';
    		$any_existinttmp = UserTemplates::where('id', '=', $template_id)->first();
    
    		
    		if ($any_existinttmp) {
    			$tabCount = 0;
    			foreach ($templates_groups as $grpname => $pgrpvalue){
    				$countTemplate = 1;
    				foreach ($pgrpvalue as $tmp){
    					$selectedGroup = $any_existinttmp->original_template_id == $tmp->id ? 'active in' : '';
    					$contentTab .= '<li class="dd-subopt col-md-3">
    										<label class="dd-option col-xs-12">
    											<input class="hidden" value="' . $tmp->id . '" type="radio" name="template_choosen" />
    											<img class="dd-option-image" src="' . \URL::to('templates/' . $tmp->path) . '/preview.png" />
    											<label class="dd-option-text hidden">' . $tmp->name . '</label>	
    											<small class="dd-option-description dd-desc hidden"> Variation : ( ' . $any_existinttmp->name . ' )  <br> 
    											Redirect url :   ' . $any_existinttmp->redirect_after     . ' <br>
    											Notify E-mail :  ' . $any_existinttmp->notification_email . ' <br> 
    											E-mail Subject : ' . $any_existinttmp->email_subject      . ' </small>
    										</label>
    									</li>';
    					
    					if($countTemplate % 4 == 0)
    						$contentTab .= '<li class="clear clearfix"></li>';
    					$countTemplate++;
    				}
    				++$tabCount;
    				$tabContent .= '<div class="tab-pane fade ' . $selectedGroup . '" id="cat' . $tabCount . '">
    								<ul class="row" style="list-style: none; overflow-y: scroll; overflow-x: none; padding: 0; height: 500px;">' . 
    									$contentTab . 
    								'</ul></div>';
    				$tabHeader  .= '<li class="' . $selectedGroup . '"><a href="#cat' . $tabCount . '" data-toggle="tab">' . $grpname . '</a></li>';
    				$contentTab = '';
    			}
    		}
    		return response()->json([ 'tabHeader' => $tabHeader, 'tabContent' => $tabContent]);
    	} catch (Exception $e) {
    		return response()->json([]);
    	}
    }

    public function add_change_template($template_id) {
        try {

            $rtg = Auth::user()->allowed_groups()->with('tplgroups.templates')->get();

            $availabl_template_groups = [];

            $templates_groups = [];
            foreach ($rtg as $a) {
                foreach ($a->tplgroups as $b) {
                    $availabl_template_groups[] = $b->id;
                    foreach ($b->templates as $tmp) {
                        if ($tmp->active == 1) {
                            $templates_groups[$b->name][] = $tmp;
                        }
                    }
                }
            }

            $res = '';
            $any_existinttmp = UserTemplates::where('id', '=', $template_id)->first();

            if ($any_existinttmp) {
                $res .='<select name="template_selector" class="template_selector" id="selector_for_' . $template_id . '">';
                foreach ($templates_groups as $grpname => $pgrpvalue):
                    $res .='<optgroup label="' . $grpname . '">';
                    foreach ($pgrpvalue as $tmp):
                        $sel = $any_existinttmp->original_template_id == $tmp->id ? ' selected="selected" ' : '';
                        $res .='<option data-enabled_field="true" ' . $sel . ' value="' . $tmp->id . '" data-imagesrc="' . \URL::to('templates/' . $tmp->path) . '/preview.png" data-description=" Variation : ( ' . $any_existinttmp->name . ' )  </br> Redirect url : ' . $any_existinttmp->redirect_after . ' </br> Notify E-mail : ' . $any_existinttmp->notification_email . '</br> E-mail Subject : ' . $any_existinttmp->email_subject . '">';
                        $res .= $tmp->name;
                        $res .='</option>';
                    endforeach;
                    $res .='</optgroup>';
                endforeach;
                $res .='</select >';
            }


            return response()->json([ 'content' => $res]);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function get_fresh_stats() {
        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        $campaigns = Campaigns::where('user_id', '=', $user_id)->with('template')->get();

        $cids = [];

        foreach ($campaigns as $c) {
            $cids[] = $c->id;
        }

        $from = \Request::input('from', '-30days');
        $to = \Request::input('to', 'now');
        $spitstats = SplitTestingStats::getMultiBasicInfo($cids, $from, $to);


        $return = [];
        foreach ($campaigns as $campaign) {
            $return[$campaign->id]['html'] = '';
            $return[$campaign->id]['totalamount'] = 0;
            if (isset($spitstats[$campaign->id])) {
                $overall = '';
                $per_variation = '';
                $return[$campaign->id]['html'] .= '<a href="' . route('extended_testing_results', [ 'campaign_id' => $campaign->id]) . '" class="btn btn-extended_stats pull-right">Results</a> ';
                $return[$campaign->id]['html'] .= '<br/>';
                $return[$campaign->id]['html'] .= '<br/>';


                $total_campaing_mto = 0;
                $total_campaing_clicks = 0;
                $total_campaing_visitors = 0;
                foreach ($spitstats[$campaign->id] as $tmp_id => $twe) {

                    if (isset($twe['total_events'])) {
                        $total_ev = $twe['total_events'];
                    } else {
                        $total_ev = 0;
                    }
                    if (isset($twe['total_opened'])) {
                        $total_opened = $twe['total_opened'];
                    } else {
                        $total_opened = 0;
                    }

                    if (isset($twe['total_mailto'])) {
                        $total_mailto = $twe['total_mailto'];
                    } else {
                        $total_mailto = 0;
                    }

                    if (isset($total_mailto) && $total_mailto > 0 && isset($total_opened) && $total_opened > 0) {

                        $conversion = (round($total_mailto / $total_opened, 3) * 100 ) . '%';
                    } else {
                        $conversion = '0%';
                    }
                    $array_of_ids_templates[] = $tmp_id;
                    $afp = 0;
                    $name = '';
                    $flag = false;
                    foreach ($campaign->template as $tmpinfo) {


                        if ($tmpinfo->id == $tmp_id) {
                            $afp = $tmpinfo->affect_percentile;
                            $name = $tmpinfo->name;
                            $flag = true;
                        }
                    }
                    if ($flag == true) {
                        if ($afp == 0) {
                            $return[$campaign->id]['totalamount'] += $total_opened;
                            $per_variation .= '<div class="stats_for_t"><h5 class="template_title_qs">';
                            $per_variation .= 'Variation: ( ' . $name . ' )</h5>';
                            $per_variation .= '<div class="row">';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Visitors</span>';
                            $per_variation .= $total_opened;
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Clicks</span>';
                            $per_variation .= $total_ev;
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Conversion</span>';
                            $per_variation .= $conversion;
                            $per_variation .= '</div>';
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="row">';

                            $per_variation .= '       <div class="col-xs-12" style="background-color:grey">Disabled</div>';

                            $per_variation .= '</div>';
                            $per_variation .= '</div>';
                        } else {
                            $return[$campaign->id]['totalamount'] += $total_opened;
                            $per_variation .= '<div class="stats_for_t"><h5 class="template_title_qs">';
                            $per_variation .= 'Variation: ( ' . $name . ' )</h5>';
                            $per_variation .= '<div class="row">';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Visitors</span>';
                            $per_variation .= $total_opened;
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Clicks</span>';
                            $per_variation .= $total_ev;
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="col-xs-4">';
                            $per_variation .= '<span class="sttitle">Conversion</span>';
                            $per_variation .= $conversion;
                            $per_variation .= '</div>';
                            $per_variation .= '</div>';
                            $per_variation .= '<div class="row">';
                            $per_variation .= '<div class="col-xs-12">';
                            $per_variation .= 'Traffic Allocation:';
                            $per_variation .= '<span class="tmp_percentage">' . $afp . '%</span>';
                            $per_variation .= '</div>';
                            $per_variation .= '</div>';
                            $per_variation .= '</div>';
                        }
                        $total_campaing_mto += $total_mailto;
                        $total_campaing_clicks += $total_ev;
                        $total_campaing_visitors += $total_opened;
                    }
                }

                foreach ($campaign->template as $tmpinfo) {

                    if (!in_array($tmpinfo->id, $array_of_ids_templates)) {

                        if ($tmpinfo->affect_percentile == 0) {
                            $per_variation .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $tmpinfo->name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                   0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                   0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    0
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="background-color:grey">
                                    Disabled
                                </div>
                            </div>
                        </div>';
                        } else {
                            $per_variation .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $tmpinfo->name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                    0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                    0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    0
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    Traffic Allocation:
                                    <span class="tmp_percentage">
                                         ' . $tmpinfo->affect_percentile . '
                                        %
                                        </span>
                                </div>
                            </div>
                        </div>';
                        }
                        $total_campaing_mto += 0;
                        $total_campaing_clicks += 0;
                        $total_campaing_visitors += 0;
                    }
                }



                $overall .= '<div class="stats_for_t"><h5 class="template_title_qs">';
                $overall .= 'Overall Campaign Stats</h5>';
                $overall .= '<div class="row">';
                $overall .= '<div class="col-xs-4">';
                $overall .= '<span class="sttitle">Visitors</span>';
                $overall .= $total_campaing_visitors;
                $overall .= '</div>';
                $overall .= '<div class="col-xs-4">';
                $overall .= '<span class="sttitle">Clicks</span>';
                $overall .= $total_campaing_clicks;
                $overall .= '</div>';
                $overall .= '<div class="col-xs-4">';
                $overall .= '<span class="sttitle">Conversion</span>';
                if ($total_campaing_visitors > 0) {

                    $overall .= ( round($total_campaing_mto / $total_campaing_visitors, 3) * 100 );
                } else {
                    $overall .= 0;
                }
                $overall .= '%</div>';
                $overall .= '</div>';
                $overall .= '</div>';

                $return[$campaign->id]['html'] .= $overall . $per_variation;
            } else {
                $return[$campaign->id]['html'] .= '<h4>This campaign currently does not have any stats.</h4>';
            }
        }

        return response()->json($return);
    }

    public function preview_template($template_id, $org_tmp_id) {
        try {
            $otmptemplate = false;

            $data = [
                'mailto_link' => '',
                'redirect_aft' => '',
                'therms_of_link' => '',
                'privacy_link' => '',
                'contact_us_link' => '',
                'title' => '',
                'template_path' => '',
                'campaign_id' => '',
                'template_id' => '',
                'template_name' => '',
                'contact_type'=>0,
            ];

            $template = UserTemplates::find($template_id);


            $template_body = $template->body;
            if (empty($template_body) || $template->original_template_id != $org_tmp_id) {

                $otmptemplate = CampaignsTemplates::find($org_tmp_id);


                $data['template_id'] = $otmptemplate->id;
                $data['template_path'] = $otmptemplate->path;

                $template_path = 'template.' . $otmptemplate->path . '.index';
                $template_body = view($template_path, $data)->render();
            }

            $template->body = $template_body;
            $template->original_template_id = $org_tmp_id;
            $template->contact_type = $template->contact_type != null?$template->contact_type:0;
            $template->integration_id = $template->integration_id != null?$template->integration_id:0;
            $template->save();

            return response()->json(
                            [
                                'tmpl_body' => $template_body,
                                'template' => $template,
                                'data' => $data
                            ]
            );
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function save_user_template(\Illuminate\Http\Request $request) {
        try {


            $validator = \Validator::make($request->all(), [
                        'template_id' => 'required',
                        'template_name' => 'required',
                        'template_return_email_content' => 'required',
                        'email_subject' => 'required',
                        /*'redirect_after' => 'required',*/
                        'notification_email' => 'required',
                        'return_redirect_link' => 'string',
                        /*'contact_link' => 'required',
                        'privacy_link' => 'required',
                        'therms_link' => 'required',*/
                        'org_tmp_id' => 'required',
                        'page_content' => 'required',
            ]);

            if ($validator->fails()) {


                return response()->json([ 'errors' => $validator->errors()->all()]);
            } else {
                $template = UserTemplates::find(\Input::get('template_id'));


                $html = new Htmldom(\Input::get('page_content'));
                $ignoreparse = 'data-ignore';
                // Find all images
                foreach ($html->find('a') as $element) {

                    if (isset($element->href) && !isset($element->$ignoreparse)) {
                        $element->href = '{{$mailto_link}}';
                    }
                    if (isset($element->onclick)) {
                        $element->onclick = '{!!$redirect_aft!!}';
                    }
                    if (isset($element->class)) {
                        switch ($element->class) {
                            case 'therms_of_s':
                                $element->href = '{{$therms_of_link}}';
                                break;
                            case 'privacy':
                                $element->href = '{{$privacy_link}}';
                                break;
                            case 'contact_us':
                                $element->href = '{{$contact_us_link}}';
                                break;
                        }
                    }
                }
                foreach ($html->find('meta') as $meta_elem) {

                    if ($meta_elem->name == 'template_id') {
                        $meta_elem->content = $template->id;
                    }
                    if ($meta_elem->name == 'campaign_id') {
                        $meta_elem->content = $template->campaign_id;
                    }
                }
                $template_content = $html->save();


                $template->original_template_id = \Input::get('org_tmp_id');
                $template->terms = \Input::get('therms_link');
                $template->privacy = \Input::get('privacy_link');
                $template->contact_us = \Input::get('contact_link');
                $template->return_redirect = \Input::get('return_redirect_link');
                $template->notification_email = \Input::get('notification_email');
                $template->redirect_after = \Input::get('redirect_after');
                $template->email_subject = \Input::get('email_subject');
                $template->contact_type = \Input::get('contact_type');
                $template->integration_id = \Input::get('integration_id');
                $template->email_message = \Input::get('template_return_email_content');
                $template->name = \Input::get('template_name');
                $template->body = $template_content;

                $template->save();
            }

            return response()->json([]);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function remove_user_template() {
        $template_id = \Input::get('tempalte_id');
        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        if (!empty($template_id) && !empty($user_id)) {
            try {
                $template = UserTemplates::where('user_id', '=', $user_id)->where('id', '=', $template_id)->first();
                if ($template) {
                    $template->delete();
                    SplitTestingStats::where('template', '=', intval($template_id))->delete();
                    CampaignStats::where('template', '=', intval($template_id))->delete();
                    return response()->json([ 'status' => true]);
                }
            } catch (\Exception $e) {
                
            }
        }
        return response()->json([ 'status' => false]);
    }

    public function campaigns_assinged($cid) {
        $data = [];
        $data['campaign'] = Campaigns::with('template')->where('user_id', '=', Auth::id())->where('id', '=', $cid)->firstOrFail();
        $data['assinged'] = User::with('profile', 'allowed_campaigns', 'role', 'owner')->whereHas('owner', function ( $query ) {
                    $query->where('owner_id', '=', Auth::id());
                })->whereHas('allowed_campaigns', function ( $query ) use ( $cid ) {
                    $query->where('campaign_id', '=', $cid);
                })->paginate(10)->setPath('campaigns/assinged /' . $cid);

        $data['not_assinged'] = User::with('profile', 'allowed_campaigns', 'role', 'owner')->whereHas('owner', function ( $query ) {
                    $query->where('owner_id', '=', Auth::id());
                })->whereHas('allowed_campaigns', function ( $query ) use ( $cid ) {
                    $query->where('campaign_id', '=', $cid);
                }, ' != ')->get()->lists('name', 'id');

        return view('campaigns.assinged', $data);
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

    public function remove_assingment($cid, $uid) {
    	
    		$user = User::whereHas('owner', function ( $query ) {
    			$query->where('owner_id', Auth::id());
    		})->whereHas('allowed_campaigns', function ( $query ) use ( $cid ) {
    			$query->where('campaign_id', $cid);
    		})->where('id', $uid)->first();
    		
        if (isset($user->id)) {
            UserAllowedCampaigns::where('user_id', '=', $uid)->where('campaign_id', '=', $cid)->delete();
            return redirect()->route('campaigns_assinged', [ 'id' => $cid])->withSuccess('user removed ');
        }
        return redirect()->route('campaigns_assinged', [ 'id' => $cid])->withError('user not removed ');
    }

    public function camplist($integration_id) {
        $integration = \MobileOptin\Models\IntegrationsUser::where('id', $integration_id)->first();
        if ($integration) {
            $client = new \GuzzleHttp\Client();

            try {
                $arr = [];

                if (!$integration->type_id || $integration->type_id == 1) {
                	Log::info("[+++++++++++++++++++++++++++++++++++++++++++++++++++++++++]");
                	Log::info("[GETRESPONSE API KEY ] " . $integration->api_key);
                    // GetResponse
                    $response = $client->get('https://api.getresponse.com/v3/campaigns', [
                        'headers' => ['X-Auth-Token' => 'api-key ' . $integration->api_key, 'Content-Type' => ' application/json'],
                        'allow_redirects' => false,
                        'timeout' => 5
                    ]);
                    
                    Log::info("[STATUS CODE ] " . $response->getStatusCode());
                    Log::info("[+++++++++++++++++++++++++++++++++++++++++++++++++++++++++]");
                    
                    if ($response->getStatusCode() == '200') {
                        $json_resp = json_decode($response->getBody(), true);
                        foreach ($json_resp as $jsp) $arr[$jsp['campaignId']] = $jsp['name'];
                    }
                } else if($integration->type_id == 2) {
                    
                    // Aweber
                    $integration_types = \MobileOptin\Models\IntegrationsType::all()->keyBy('id');
                    
                    // the AWeber package does not properly define a namespace for itself and is not getting mapped by Composer, so just include it
                    require_once base_path('vendor/aweber/aweber/aweber_api/aweber.php');
                    
                    $aweber_consumer_key    = !empty($integration->api_key) ? $integration->api_key : $integration_types[2]->oauth_key;
                    $aweber_consumer_secret = !empty($integration->access_token) ? $integration->access_token : $integration_types[2]->oauth_secret;
                    $aweber_access_key      = $integration->organizerKey;
                    $aweber_access_secret   = $integration->authorization;
                    $aweber                 = new \AWeberAPI($aweber_consumer_key, $aweber_consumer_secret);
                    $aweber_account         = $aweber->getAccount($aweber_access_key, $aweber_access_secret);
                    $list_url               = "/accounts/{$aweber_account->id}/lists/";
                    $lists                  = $aweber_account->loadFromUrl($list_url);
                    
                    foreach($lists->data['entries'] as $list) {
                        $arr[$list['id']] = $list['name'];
                    }
                    
                    $nb_list_loaded       = count($lists->data['entries']);
                    $total_list_available = $lists->data['total_size'];
                    
                    if($total_list_available  > $nb_list_loaded){
                    	//load all the remain list
                    	$list_url .= '?ws.start=' . $nb_list_loaded . '&ws.size=' . ($total_list_available - $nb_list_loaded); 
                    	$lists     = $aweber_account->loadFromUrl($list_url);
                    	
                    	foreach($lists->data['entries'] as $list) {
                    		$arr[$list['id']] = $list['name'];
                    	}
                    }

                } else if($integration->type_id == 3) {
                    // Gotowebinar
                    
                	//access_token   => api_key
                	//refresh_token  => authorization
                	//account_key    => local_api_key
                	//consumer_key   => organizerKey
                	Log::info("[API KEY ] " . $integration->api_key);
                	Log::info("[ACCESS TOKEN ] " .  $integration->access_token);
                	try{
                		$response = $client->get(
	                				'https://api.citrixonline.com/G2W/rest/organizers/' . $integration->api_key .  '/webinars',
	                				[
		                				'headers' => [
			                				'Authorization' => 'OAuth oauth_token=' . $integration->access_token,
			                				'Content-Type'  => 'application/json',
			                				'Accept'        => 'application/json'
			                				],
		                				'allow_redirects' => false,
		                				'timeout' => 5
	                				]);
                		
                		
                		if ($response->getStatusCode() == '200') {
                			$json_resp = json_decode($response->getBody(), true);
                			foreach ($json_resp as $jsp) $arr[$jsp['webinarKey']] = $jsp['subject'];
                		}
                	}catch (Exception $exception){
                		Log::debug("[EXCEPTION MESSAGE ] " . $exception->getMessage());
                	}
                } else if($integration->type_id == 5) {
                	
                	list($realApiKey, $datacenter) = explode('-', $integration->api_key);
                	// MailChimp
                    $response = $client->get('https://' . $datacenter . '.api.mailchimp.com/3.0/lists', [
                        'headers'         => [
                    							'Authorization' => 'api-key ' . $integration->api_key, 
                    							'Accept' => ' application/json'
                            				 ],
                        'allow_redirects' => false,
                        'timeout'         => 10
                    ]);
                	
                    if ($response->getStatusCode() == '200') {
                    	$campaignsList = json_decode($response->getBody(), true);
                    	if(!empty($campaignsList['lists']) && count($campaignsList['lists']) == (int)$campaignsList['total_items'] ){
	                    	foreach ($campaignsList['lists'] as $key => $campaign) $arr[$campaign['id']] = $campaign['name'];
                    	}else{
                    		if((int)$campaignsList['total_items'] > 0){
                    			$response = $client->get('https://' . $datacenter . '.api.mailchimp.com/3.0/lists', [
                    					'headers'         => [
						                    					'Authorization' => 'api-key ' . $integration->api_key,
						                    					'Accept' => ' application/json'
						                    				 ],
                    					'allow_redirects' => false,
                    					'timeout'         => 10,
                    					'query'           => array('count' => $campaignsList['total_items'])
                    			]);
                    			
                    			if ($response->getStatusCode() == '200') {
                    				$campaignsList = json_decode($response->getBody(), true);
                    				if(!empty($campaignsList['lists']))
                    					foreach ($campaignsList['lists'] as $key => $campaign) $arr[$campaign['id']] = $campaign['name'];
                    			}
                    		}	
                    	}
                    }
                	
                }else if($integration->type_id == 7) {
                	// GVO eResponder Pro [Fecth campaign lists]
                	Log::info(" API KEY FOR GVO REQUEST " . $integration->api_key );
                	
                    $response = $client->send($client->createRequest('POST', 'http://gogvo.com/api/eresponder/get_campaigns', [
                    	'headers'         => [ 'Accept'  => ' application/json', 'Content-Type'  => 'application/x-www-form-urlencoded' ],
                        'body'            => [ 'api_key' => $integration->api_key ],
                        'allow_redirects' => false,
                    	'timeout'         => 45
                    ]));
                	
                   if ($response->getStatusCode() == '200') {
                   		Log::info($response->getBody());
                    	$campaignsListResponse = json_decode($response->getBody(), true);
                    	if($campaignsListResponse['status'] == 'success'){
                    		if(!empty($campaignsListResponse['Campaigns'])){
                    			$campaignsList = $campaignsListResponse['Campaigns'];
                    			foreach ($campaignsList as $key => $campaign) $arr[$key] = $campaign['Name'];
                    		}	
                    	}
                   }
                }else if($integration->type_id == 8){
                	//SendLane
                	$response = $client->send($client->createRequest('POST', 'https://'. $integration->authorization .'/api/v1/lists', [
	                			'headers'         => [ 
	                				'Accept'  => ' application/json', 
	                				'Content-Type'  => 'application/x-www-form-urlencoded' 
	                           	],
	                			'body'            => [
                					'api'  => $integration->api_key,
                					'hash' => $integration->organizerKey 
                            	],
	                			'allow_redirects' => false,
	                			'timeout'         => 45
                			]));
                	 
                	if ($response->getStatusCode() == '200') {
                		$campaignsListResponse = json_decode($response->getBody(), true);
                		Log::debug("CAMPAIGN SENDLANE LIST RESPONSE " . $response->getBody());
                		if(!empty($campaignsListResponse)){
                			if(empty($campaignsListResponse['info'])){
                				foreach ($campaignsListResponse as $key => $campaign) 
                					$arr[$campaign['list_id']] = $campaign['list_name'];
                			}
                		}
                	}
                }else if($integration->type_id == 9){
                	//WebinarJam
                	$response = $client->send($client->createRequest('POST', 'https://app.webinarjam.com/api/v2/webinars', [
		                			'headers'         => [
		                				'Accept'        => ' application/json',
		                				'Content-Type'  => 'application/x-www-form-urlencoded'
		                			],
		                			'body'            => [
		                				'api_key' => $integration->api_key
		                			],
		                			'allow_redirects' => false,
		                			'timeout'         => 45
	                			]));
                	
                	if ($response->getStatusCode() == '200') {
                		$campaignsListResponse = json_decode($response->getBody(), true);
                		Log::debug("CAMPAIGN WEBINARJAM LIST RESPONSE " . $response->getBody());
                		if(!empty($campaignsListResponse)){
                			if(!empty($campaignsListResponse['status']) && 
                					 $campaignsListResponse['status'] == 'success' &&
                					 count($campaignsListResponse['webinars'])  > 0
                    			){
                				foreach ($campaignsListResponse['webinars'] as $key => $campaign)
                					$arr[$campaign['webinar_id']] = $campaign['name'];
                			}
                		}
                	}
                }else if($integration->type_id == 10){
                	
                	\MailWizzApi_Autoloader::register();
                	
                	$storagePath = storage_path() . '/MailWizzApi/cache/' . Auth::id();
                	
                	if ( !File::exists( $storagePath ) ) {
                		File::makeDirectory( $storagePath, 0755, true);
                	}
                	
                	// configuration object
                	$config = new \MailWizzApi_Config([
                			'apiUrl'        => 'http://dashboard.sendreach.com/api/index.php',
                			'publicKey'     => $integration->api_key,
                			'privateKey'    => $integration->authorization,
                	
                			// components
                			'components' => [
                				'cache' => [
                					'class'     => 'MailWizzApi_Cache_File',
                					'filesPath' => $storagePath,
                				]
                			],
                	]);
                	
                	\MailWizzApi_Base::setConfig($config);
                	$currentPage           = 1;
                	$nbByPage              = 10;
                	$endpoint              = new \MailWizzApi_Endpoint_Lists();
                	$response              = $endpoint->getLists($currentPage, $nbByPage);
                	$campaignsListResponse = $response->body->toArray();
                	
                	Log::debug("CAMPAIGN SENDREACH LIST RESPONSE " . json_encode($campaignsListResponse));
                	if(!empty($campaignsListResponse)){
                		if(!empty($campaignsListResponse['status']) &&  $campaignsListResponse['status'] == 'success'){
                			if($campaignsListResponse['data']['count'] > 0){
                				foreach ($campaignsListResponse['data']['records'] as $key => $campaign)
                					$arr[$campaign['general']['list_uid']] = $campaign['general']['name'];
                				
                				
                				if((int)$campaignsListResponse['data']['count'] > $nbByPage){
                					$arr       = [];
                					$response  = $endpoint->getLists(1, $campaignsListResponse['data']['count']);
                					$campaignsListResponse = $response->body->toArray();
                					Log::debug("ALL CAMPAIGN SENDREACH LIST FETCHED " . json_encode($campaignsListResponse));
                				
                					if(!empty($campaignsListResponse)){
                						if(!empty($campaignsListResponse['status']) &&  $campaignsListResponse['status'] == 'success'){
                							if($campaignsListResponse['data']['count'] > 0){
                								foreach ($campaignsListResponse['data']['records'] as $key => $campaign)
                									$arr[$campaign['general']['list_uid']] = $campaign['general']['name'];
                							}
                						}
                					}
                				}
                			}
                		}
                	}
                }else if($integration->type_id == 11){
                	// Fluttermail
                	$response = $client->get('https://em.fluttermail.com/admin/api.php', [
		                			'headers'         => [ 'Accept'  => ' application/json' ],
		                			'allow_redirects' => false,
		                			'timeout'         => 45,
	                				'query'           => [
			                			'api_key'    => $integration->api_key,
			                			'api_action' => 'list_list',
			                			'api_output' => 'json',
			                			'ids'        => 'all',
			                			'full'       => 1
		                			]
	                			]);
                	
                	if ($response->getStatusCode() == '200') {
                		$campaignsListResponse = json_decode($response->getBody(), true);
                		Log::debug("FLUTTER MAIL LIST RESPONSE " . $response->getBody());
                		if(!empty($campaignsListResponse)){
                			if( ! empty($campaignsListResponse['result_code']) && $campaignsListResponse['result_code'] == 1 ){
                				foreach ($campaignsListResponse as $key => $campaign)
                					if(is_numeric($key))
                						$arr[$campaign['id']] = $campaign['name'];
                			}
                		}
                	}
                }
 
                return response()->json($arr);
            } catch (ClientException $e) {
                return response()->json([]);
            }
        }

        return response()->json([]);
    }

    // display pixel and log event to database
    public function get_pixel($user_template_id)
    {
        $user_template = \MobileOptin\Models\UserTemplates::where('id', $user_template_id)->first();

        // record pixel event to Mongo database
        $event = 'pixel';
        $label = 'pixel';
        $name = $user_template->name;
        $value = 1;
        $save_response = SplitTestingStats::record_event($user_template->campaign_id, $user_template_id, $event, $label, $name, $value);

        Log::info('Recording pixel event:');
        Log::info($save_response);

        // output 1x1 pixel
        header('Content-Type: image/gif');
        //equivalent to readfile('pixel.gif')
        echo "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
    }
}
