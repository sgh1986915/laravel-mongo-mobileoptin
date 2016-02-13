<?php namespace MobileOptin\Http\Controllers;

use Clockwork\Clockwork;
use Htmldom;
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

class CampaignsController extends Controller
{

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

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {

        \SEOMeta::setTitle( 'Campaigns - page ' . ( \Input::get( 'page' ) ? \Input::get( 'page' ) : 1 ) );

        \SEOMeta::setDescription( 'meta desc' );
        \SEOMeta::addKeyword( [ 'key1', 'key2', 'key3' ] );

        $data[ 'has_embed' ]  = Auth::user()->getProfileOption( 'embed' );
        $data[ 'has_hosted' ] = Auth::user()->getProfileOption( 'hosted' );

        $user_id             = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
        $data[ 'campaigns' ] = Campaigns::where( 'user_id', '=', $user_id )->with( 'template' )->paginate( 10 )->setPath( 'campaigns' );

        $cids = [ ];

        foreach ( $data[ 'campaigns' ] as $c ) {
            $cids[ ] = $c->id;

        }

        $data[ 'splitTestStats' ] = SplitTestingStats::getMultiBasicInfo( $cids, '-30 days', 'now' );

        //lotery to delete old templates

        UserTemplates::cleanOld();

        //        \Clockwork::info( $splt );
        return view( 'campaigns.list', $data );
    }


    public function add()
    {


        if ( Auth::user()->getOwner() == false ) {
            $campaign_limit = Auth::user()->campaignLimit();

        } else {
            $own_profile    = UserProfile::where( 'user_id', '=', Auth::user()->getOwner() )->first();
            $campaign_limit = Campaigns::where( 'user_id', '=', Auth::user()->getOwner() )->count() < $own_profile->max_campaigns;
        }


        if ( $campaign_limit ) {

            $data = array();
            \SEOMeta::setTitle( 'Add - Campaigns ' );
            \DB::enableQueryLog();
            $rtg = Auth::user()->allowed_groups()->with( 'tplgroups.templates' )->get();

            $availabl_template_groups = [ ];

            $templates_groups = [ ];
            foreach ( $rtg as $a ) {
                foreach ( $a->tplgroups as $b ) {
                    $availabl_template_groups[ ] = $b->id;
                    foreach ( $b->templates as $tmp ) {
                        if ( $tmp->active == 1 ) {
                            $templates_groups[ $b->name ][ ] = $tmp;
                        }
                    }
                }
            }
            $data[ 'templates' ]  = $templates_groups;
            $dis_templates_groups = [ ];
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
            $data[ 'templates_disable' ] = $dis_templates_groups;


            $data[ 'campaign' ]       = new \stdClass();
            $data[ 'campaign' ]->id   = 0;
            $data[ 'campaign' ]->name = \Input::old( 'name' );

            $data[ 'campaign' ]->slug                      = \Input::old( 'slug' );
            $data[ 'campaign' ]->user_id                   = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
            $data[ 'campaign' ]->template                  = [ ];
            $data[ 'campaign' ]->ao_clicks                 = '';
            $data[ 'campaign' ]->ao_threshold              = '';
            $data[ 'campaign' ]->analitics_and_retargeting = '';

            $old_ids = [ ];
            $pst     = \Input::old( 'template_id' );
            if ( !empty( $pst ) ) {
                foreach ( $pst as $a ) {
                    $old_ids[ ] = $a;
                }
                if ( !empty( $old_ids ) ) {
                    $data[ 'campaign' ]->template = UserTemplates::whereIn( 'id', $old_ids )->get();

                }
            }

            $data[ 'original_template_id' ]      = 0;
            $data[ 'can_analytics_retargeting' ] = Auth::user()->getProfileOption( 'analytics_retargeting' ) ? '' : 'disabled="disabled"';


            $data[ 'can_redirect' ] = Auth::user()->getProfileOption( 'redirect_page' ) ? '' : 'disabled="disabled"';


            return view( 'campaigns.add_edit', $data );
        }
        return redirect()->route( 'campaigns' )->withError( 'You have used up all your campaigns !' );
    }

    public function edit( $CId )
    {

        $data = array();
        \SEOMeta::setTitle( 'Add - Campaigns ' );


        $rtg = Auth::user()->allowed_groups()->with( 'tplgroups.templates' )->get();

        $availabl_template_groups = [ ];

        $templates_groups = [ ];
        foreach ( $rtg as $a ) {
            foreach ( $a->tplgroups as $b ) {
                $availabl_template_groups[ ] = $b->id;
                foreach ( $b->templates as $tmp ) {
                    if ( $tmp->active == 1 ) {
                        $templates_groups[ $b->name ][ ] = $tmp;
                    }
                }
            }
        }
        $data[ 'templates' ]  = $templates_groups;
        $dis_templates_groups = [ ];
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
//
//
//            }
//        }
        $data[ 'templates_disable' ] = $dis_templates_groups;


        try {
            if ( Auth::user()->getOwner() !== false ) {
                $edior_id = Auth::user()->getOwner();
            } else {
                $edior_id = Auth::id();
            }
            $data[ 'can_analytics_retargeting' ] = Auth::user()->getProfileOption( 'analytics_retargeting' ) ? '' : 'disabled="disabled"';

            $data[ 'can_redirect' ] = Auth::user()->getProfileOption( 'redirect_page' ) ? '' : 'disabled="disabled"';
            $data[ 'campaign' ]     = Campaigns::with( 'template', 'template.org_template' )->where( 'user_id', '=', $edior_id )->where( 'id', '=', $CId )->firstOrFail();
            //            $data[ 'original_template_id' ] = $data[ 'campaign' ]->template->original_template_id ? $data[ 'campaign' ]->template->original_template_id : 0;

            $data[ 'original_template_id' ] = 0;
        } catch ( \Exception $e ) {
            return redirect()->route( 'campaigns' )->withError( $e . 'Campaign not found or you do not have permissions' );
        }

        return view( 'campaigns.add_edit', $data );
    }


    public function upsert()
    {
        $validator = \Validator::make( \Input::only( 'id', 'name', 'slug' ), [
            'id'   => 'required|integer',
            'name' => 'required',
            'slug' => 'required',

        ] );


        if ( $validator->fails() ) {
            // The given data did not pass validation
            return redirect()->back()->withInput()->withErrors( $validator );
        } else {

            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            if ( \Input::get( 'id' ) > 0 ) {
                $new_camp = Campaigns::where( 'id', '=', \Input::get( 'id' ) )->where( 'user_id', '=', Auth::id() )->first();
                if ( !$new_camp ) {
                    return redirect()->back()->withInput()->withError( 'Campaign not found' );
                }
            } else {
                $new_camp = new Campaigns();

                $new_camp->user_id = $user_id;

            }

            $new_camp->name                      = \Input::get( 'name' );
            $new_camp->slug                      = str_slug( \Input::get( 'slug' ) );
            $new_camp->ao_clicks                 = str_slug( \Input::get( 'ao_clicks' ) );
            $new_camp->ao_threshold              = str_slug( \Input::get( 'ao_threshold' ) );
            $new_camp->ao_threshold              = str_slug( \Input::get( 'ao_threshold' ) );
            $new_camp->analitics_and_retargeting = (\Input::get( 'analitics_and_retargeting' ) == null)?' ':\Input::get( 'analitics_and_retargeting' );
            $new_camp->active                    = 1;

            $new_camp->save();


            $user_template     = \Input::get( 'user_template' );
            $percent_container = \Input::get( 'percent_container' );

            if ( !empty( $user_template ) ) {
                foreach ( $user_template as $k => $u_tid ) {
                    $u_tmpl = UserTemplates::find( $u_tid );
                    if ( $u_tmpl ) {
                        //find and now edit
                        $u_tmpl->affect_percentile = $percent_container[ $k ];
                        $u_tmpl->campaign_id       = $new_camp->id;
                        $u_tmpl->save();
                    }

                }
            }
            $for_deletion = UserTemplates::where( 'campaign_id', '=', $new_camp->id )->whereNotIn( 'id', $user_template )->get();
            if ( $for_deletion ) {
                foreach ( $for_deletion as $delte_this ) {
                    $delte_this->delete();
                }
            }

            if ( Auth::user()->getOwner() !== false ) {

                $ua              = new UserAllowedCampaigns();
                $ua->user_id     = Auth::id();
                $ua->campaign_id = $new_camp->id;
                $ua->save();

            }

            return redirect()->route( 'campaigns' )->withNotify( 'Campaign saved' );
        }

    }

    public function delete( $CId )
    {
        try {
            if ( Auth::user()->getOwner() == false ) {
                $Campaign = Campaigns::where( 'user_id', '=', Auth::id() )->where( 'id', '=', $CId )->firstOrFail();
                $Campaign->forceDelete();

                SplitTestingStats::where( 'campaign_id', '=', $CId )->delete();
                CampaignStats::where( 'campaign_id', '=', $CId )->delete();
                UserTemplates::where( 'campaign_id', '=', $CId )->delete();
                return redirect()->route( 'campaigns' )->withSuccess( 'Campaign Deleted' );
            }

        } catch ( \Exception $e ) {
        }
        return redirect()->route( 'campaigns' )->withError( 'Campaign not removed ' );

    }

    public function change_satus( $CId, $new_status )
    {
        try {
            if ( Auth::user()->getOwner() == false ) {
                $Campaign = Campaigns::where( 'user_id', '=', Auth::id() )->where( 'id', '=', $CId )->firstOrFail();
            } else {
                $Campaign = Campaigns::where( 'user_id', '=', Auth::user()->getOwner() )->where( 'id', '=', $CId )->firstOrFail();
            }

            if ( $new_status ) {
                $Campaign->activated_on = date( 'Y-m-d H:i:s' );
            } else {
                $Campaign->deactivated_on = date( 'Y-m-d H:i:s' );

            }
            $Campaign->active = $new_status;
            $Campaign->save();

            return redirect()->route( 'campaigns' )->withSuccess( 'Campaign status changed' );

        } catch ( \Exception $e ) {
            return redirect()->route( 'campaigns' )->withError( 'Status not updated ' );
        }
    }

    public function show_item( $campaign_id, $slug = '', Request $request )
    {

        $data = [ 'mailto_link' => '', 'redirect_aft' => '', 'therms_of_link' => '', 'privacy_link' => '', 'contact_us_link' => '' ];

        try {
            $campaign = Campaigns::with( 'template' )->
            where( 'id', '=', $campaign_id )->
            where( 'active', '=', 1 )->
            whereHas( 'template', function ( $query ) {
                $query->where( 'affect_percentile', '>', 0 );
            } )->first();


            if ( $campaign ) {

                $active_templates = [ ];

                foreach ( $campaign->template as $ct ) {
                    $active_templates[ ] = intval( $ct->id );
                }

                // pull data from mongo
                $campaign_clicks = CampaignStats::raw( function ( $collection ) use ( $campaign_id, $active_templates ) {
                    return $collection->aggregate( [
                        [ '$match' => [
                            'campaign_id' => intval( $campaign_id ),
                            'template'    => [ '$in' => $active_templates ],

                        ] ],
                        [
                            '$group' => [
                                '_id'   => '$template',
                                'total' => [
                                    '$sum' => 1
                                ]
                            ]
                        ]

                    ] );
                } );
                $template_stats  = $campaign_clicks[ 'result' ];


                $tmpstats = SplitTestingStats::raw( function ( $collection ) use ( $campaign_id, $active_templates ) {
                    return $collection->aggregate( [
                        [ '$match' => [
                            'campaign_id' => intval( $campaign_id ),
                            'template'    => [ '$in' => $active_templates ],

                        ] ],
                        [
                            '$group' => [
                                '_id'   => [ 'event' => '$event', 'template' => '$template' ],

                                'total' => [
                                    '$sum' => 1
                                ]
                            ]
                        ]

                    ] );
                } );


                // auto optimisation of campaigns start
                $tempalte_for_auto_optimisation = [ ];
                if ( $campaign_clicks[ 'ok' ] == 1 ) {
                    $tce = 0; //total campaign events triggered
                    foreach ( $campaign_clicks[ 'result' ] as $ceptr ) {
                        $tce += $ceptr[ 'total' ];
                    }
                    $event_per_template_raw = [ ];// number of events triggered by template;


                    foreach ( $tmpstats[ 'result' ] as $ceptr ) {
                        $event_per_template_raw[ $ceptr[ '_id' ][ 'template' ] ][ $ceptr[ '_id' ][ 'event' ] ] = + $ceptr[ 'total' ];
                    }

                    $tmp_with_af_perc = [ ];

                    foreach ( $campaign->template as $tmp ) {
                        if ( $tmp->affect_percentile > 0 ) {
                            if ( $tmp->affect_percentile / 100 != 0 ) {
                                $tmp_with_af_perc[ $tmp->id ] = 1 - ( $tmp->affect_percentile / 100 );
                            } else {
                                $tmp_with_af_perc[ $tmp->id ] = 0.5;
                            }
                        }
                    }

                    $engagement_per_tmp = [ ];

                    foreach ( $event_per_template_raw as $tmp_id => $tmp_e_t ) {
                        // percent of user taht clicked someware
                        $nouna = ( isset( $tmp_e_t[ 'navigate' ] ) ) ? ( ( $tmp_e_t[ 'navigate' ] / $tmp_e_t[ 'page_open' ] ) * 4 ) : 0;

                        // percent of user that read the page
                        $noure = ( $tmp_e_t[ 'read' ] / $tmp_e_t[ 'page_open' ] ) * 2;

                        // calculate the percentage
                        $engagement_per_tmp[ $tmp_id ] = ( round( ( $noure + $nouna ) / 2, 3 ) * 100 ) * $tmp_with_af_perc[ $tmp_id ];
                    }



//                    foreach ( $ceptnum as $k => $v ) {
//                        $ceptper [ $k ] = round( $v / $tce, 3 ) * 100;
//                    }


                    if ( $campaign->ao_clicks > 0 && $campaign->ao_threshold > 0 ) {

                        if ( $campaign->ao_clicks % $tce == 0 ) {

                            foreach ( $engagement_per_tmp as $tid => $evinp ) {
                                // for each template as event in percent

                                if ( $campaign->ao_threshold > $evinp ) {
                                    $tempalte_for_auto_optimisation[ $tid ] = true;
                                }
                            }
                        }
                    }
                    if(count($active_templates)==count($tempalte_for_auto_optimisation)){
                        $tempalte_for_auto_optimisation=[];
                    }

//                    \Clockwork::info( $engagement_per_tmp );
//                    \Clockwork::info( $tmp_with_af_perc );

                }

                $total_show = 0;
                foreach ( $template_stats as $tmprow ) {
                    $total_show += $tmprow[ 'total' ];
                }
                $variations_real      = [ ];
                $variations_should_be = [ ];
                // get the stats from mogno for all campaing in the template as they where viewed
                foreach ( $template_stats as $tmprow ) {
                    $variations_real[ $tmprow[ '_id' ] ] = round( $tmprow[ 'total' ] / $total_show, 3 ) * 100;
                }
                // all template for that cmapaing with the perentile of how they should be shown
                foreach ( $campaign->template as $tmp ) {
                    if ( $tmp->affect_percentile > 0 ) {
                        if ( isset( $tempalte_for_auto_optimisation[ $tmp->id ] ) ) {
                            $ustt = UserTemplates::find( $tmp->id );
                            if ( $ustt ) {
                                $ustt->affect_percentile = 0;
                                $ustt->save();
                            }

                        } else {
                            $variations_should_be[ $tmp->id ] = $tmp->affect_percentile;
                        }
                    }
                }
                // auto optmisation end

                // remove from calulations the templates that do not exist any more
                foreach ( $variations_real as $vrid => $vrv ) {
                    if ( !isset( $variations_should_be[ $vrid ] ) ) {
                        unset( $variations_real[ $vrid ] );
                    }
                }

                // fill mising stats for tempalte with empty values
                foreach ( $variations_should_be as $k => $v ) {
                    if ( !isset( $variations_real[ $k ] ) ) {
                        $variations_real[ $k ] = 0;
                    }

                }
                // find the wining template
                foreach ( $variations_should_be as $k => $v ) {
                    if ( isset( $variations_real[ $k ] ) ) {


                        if ( $variations_real[ $k ] < $v && $v > 0 ) {
                            foreach ( $campaign->template as $rtmp ) {
                                if ( $k == $rtmp->id ) {
                                    $winer = $rtmp;
                                }
                            }
                        }
                    }
                }
                if ( !isset( $winer ) ) {
                    $winer = $campaign->template->first();
                }


                if ( isset( $winer ) && $winer->affect_percentile > 0 ) {
                    
                    $data[ 'mailto_link' ] = 'mailto:' . $winer->notification_email . '?subject=' . rawurlencode( $winer->email_subject ) . '&body=' . rawurlencode( $winer->email_message );

                    $allowed_custom_url_params = \Config::get( 'redirect_allowed' );
                    $qstring                   = '';
//                    $rfrsa                     = [ ];

                    foreach ( $request->query() as $qsk => $qsv ) {
                        if ( in_array( $qsk, $allowed_custom_url_params ) !== false ) {
                            if ( stripos( $winer->redirect_after, $qsk ) !== false ) {
//                                $qstring .= trim($qsk) . '=' . trim($qsv);
//                                $rfrsa[ ] = $qsk;
                                $winer->redirect_after = str_replace( '[' . $qsk . ']', trim( $qsv ), $winer->redirect_after );
                            }
                        }
                    }
                    $winer->redirect_after = preg_replace( '#(\[)(.*)(\])#si', '', $winer->redirect_after );


                    if ( !empty( $winer->redirect_after ) ) {
                        $data[ 'redirect_aft' ] = urldecode( $winer->redirect_after . $qstring );
                    }
                    CampaignStats::record_event( $campaign->id, $winer->id, $winer->original_template_id );

                    if ( isset( $winer->body ) && !empty( $winer->body ) ) {
                        
                        $data[ 'therms_of_link' ]  = $winer->terms;
                        $data[ 'privacy_link' ]    = $winer->privacy;
                        $data[ 'contact_us_link' ] = $winer->contact_us;
                        $data[ 'title' ]           = $campaign->title;
                        $data[ 'template_id' ]     = $winer->id;
                        $data[ 'campaign_id' ]     = $campaign->id;

                        $user_profile = UserProfile::where( 'user_id', '=', $campaign->user_id )->first();
                        if ( $user_profile->analytics_retargeting ) {
                            $data[ 'analitics_and_retargeting' ] = $campaign->analitics_and_retargeting;
                        } else {
                            $data[ 'analitics_and_retargeting' ] = '';

                        }


                        $html = new Htmldom( $winer->body );

                        // Find all images
                        foreach ( $html->find( 'span' ) as $element ) {
                            if ( isset( $element->class ) ) {
                                if ( $element->class == 'link' ) {
                                    $text               = $element->innertext;
                                    $element->outertext = '<a href="{{$mailto_link}}" data-onclick="' . $data[ 'redirect_aft' ] . '" target="_blank">' . $text . '</a>';
                                }
                            }

                        }

                        foreach ( $html->find( 'meta' ) as $meta_elem ) {

                            if ( $meta_elem->name == 'template_id' ) {
                                $meta_elem->content = $winer->id;
                            }
                            if ( $meta_elem->name == 'campaign_id' ) {
                                $meta_elem->content = $winer->campaign_id;
                            }

                        }
                        $winer->body = $html->save();

                        return \StringView::make(
                            array(
                                // this actual blade template
                                'template'   => $winer->body . '{!!$analitics_and_retargeting!!}',
                                // this is the cache file key, converted to md5
                                'cache_key'  => 'campaign_campaign_preview_' . md5( $campaign_id . "_" . $winer->id ),
                                // timestamp for when the template was last updated, 0 is always recompile
                                'updated_at' => time()
                            ),
                            $data
                        );


                    }
                }
            } else {
                return '<h5 style="margin: 10px auto 10px">Sorry this campaign has been disabled.</h5>';

            }
        } catch ( Exception $e ) {
            return '<h5 style="margin: 10px auto 10px">Sorry this campaign has been disabled.</h5>';

        }
    }

    public function add_template( $campaign_id )
    {
        try {
            $new_tmp_id = 0;


            $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();


            $tmp_template                    = new UserTemplates();
            $tmp_template->campaign_id       = $campaign_id;
            $tmp_template->user_id           = $user_id;
            $tmp_template->body              = '';
            $tmp_template->name              = '';
            $tmp_template->terms             = '';
            $tmp_template->privacy           = '';
            $tmp_template->contact_us        = '';
            $tmp_template->affect_percentile = '';

            $tmp_template->redirect_after     = '';
            $tmp_template->notification_email = '';
            $tmp_template->email_subject      = '';
            $tmp_template->email_message      = '';
            $tmp_template->name               = '';


            $tmp_template->save();
            $new_tmp_id = $tmp_template->id;


            $any_existinttmp = UserTemplates::where( 'campaign_id', '=', $campaign_id )->first();

            if ( $any_existinttmp ) {

                $tmp_template->terms      = $any_existinttmp->terms;
                $tmp_template->privacy    = $any_existinttmp->privacy;
                $tmp_template->contact_us = $any_existinttmp->contact_us;


                $tmp_template->redirect_after     = $any_existinttmp->redirect_after;
                $tmp_template->notification_email = $any_existinttmp->notification_email;
                $tmp_template->email_subject      = $any_existinttmp->email_subject;
                $tmp_template->email_message      = $any_existinttmp->email_message;
                $tmp_template->save();
            }


            return response()->json( [ 'tmpl_id' => $new_tmp_id, ] );
        } catch ( Exception $e ) {
            return response()->json( [ ] );
        }
    }

    public function get_fresh_stats()
    {
        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        $campaigns = Campaigns::where( 'user_id', '=', $user_id )->with( 'template' )->get();

        $cids = [ ];

        foreach ( $campaigns as $c ) {
            $cids[ ] = $c->id;
        }

        $from      = \Request::input( 'from', '-30days' );
        $to        = \Request::input( 'to', 'now' );
        $spitstats = SplitTestingStats::getMultiBasicInfo( $cids, $from, $to );


        $return = [ ];
        foreach ( $campaigns as $campaign ) {
            $return[ $campaign->id ][ 'html' ]        = '';
            $return[ $campaign->id ][ 'totalamount' ] = 0;
            if ( isset( $spitstats[ $campaign->id ] ) ) {
                $overall       = '';
                $per_variation = '';
                $return[ $campaign->id ][ 'html' ] .= '<a href="' . route( 'extended_testing_results', [ 'campaign_id' => $campaign->id ] ) . '" class="btn btn-extended_stats pull-right">Results</a> ';
                $return[ $campaign->id ][ 'html' ] .= '<br/>';
                $return[ $campaign->id ][ 'html' ] .= '<br/>';


                $total_campaing_mto      = 0;
                $total_campaing_clicks   = 0;
                $total_campaing_visitors = 0;
                foreach ( $spitstats[ $campaign->id ] as $tmp_id => $twe ) {

                    $afp  = 0;
                    $name = '';
                    foreach ( $campaign->template as $tmpinfo ) {

                        if ( $tmpinfo->id == $tmp_id ) {
                            $afp  = $tmpinfo->affect_percentile;
                            $name = $tmpinfo->name;
                        }
                    }
                    $return[ $campaign->id ][ 'totalamount' ] += $twe[ 'total_opened' ];
                    $per_variation .= '<div class="stats_for_t"><h5 class="template_title_qs">';
                    $per_variation .= 'Variation: ( ' . $name . ' )</h5>';
                    $per_variation .= '<div class="row">';
                    $per_variation .= '<div class="col-xs-4">';
                    $per_variation .= '<span class="sttitle">Visitors</span>';
                    $per_variation .= $twe[ 'total_opened' ];
                    $per_variation .= '</div>';
                    $per_variation .= '<div class="col-xs-4">';
                    $per_variation .= '<span class="sttitle">Clicks</span>';
                    $per_variation .= $twe[ 'total_events' ];
                    $per_variation .= '</div>';
                    $per_variation .= '<div class="col-xs-4">';
                    $per_variation .= '<span class="sttitle">Conversion</span>';
                    $per_variation .= $twe[ 'conversion' ];
                    $per_variation .= '</div>';
                    $per_variation .= '</div>';
                    $per_variation .= '<div class="row">';
                    $per_variation .= '<div class="col-xs-12">';
                    $per_variation .= 'Traffic Allocation:';
                    $per_variation .= '<span class="tmp_percentage">' . $afp . '%</span>';
                    $per_variation .= '</div>';
                    $per_variation .= '</div>';
                    $per_variation .= '</div>';

                    $total_campaing_mto += $twe[ 'total_mailto' ];
                    $total_campaing_clicks += $twe[ 'total_events' ];
                    $total_campaing_visitors += $twe[ 'total_opened' ];

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
                if ( $total_campaing_visitors > 0 ) {

                    $overall .= ( round( $total_campaing_mto / $total_campaing_visitors, 3 ) * 100 );
                } else {
                    $overall .= 0;

                }
                $overall .= '%</div>';
                $overall .= '</div>';
                $overall .= '</div>';

                $return[ $campaign->id ][ 'html' ] .= $overall . $per_variation;
            } else {
                $return[ $campaign->id ][ 'html' ] .= '<h4>This campaign currently does not have any stats.</h4>';
            }

        }

        return response()->json( $return );

    }

    public function preview_template( $template_id, $org_tmp_id )
    {
        try {
            $otmptemplate = false;

            $data = [
                'mailto_link'     => '',
                'redirect_aft'    => '',
                'therms_of_link'  => '',
                'privacy_link'    => '',
                'contact_us_link' => '',
                'title'           => '',
                'template_path'   => '',
                'campaign_id'     => '',
                'template_id'     => '',
                'template_name'   => '',
            ];

            $template = UserTemplates::find( $template_id );


            $template_body = $template->body;
            if ( empty( $template_body ) || $template->original_template_id != $org_tmp_id ) {

                $otmptemplate = CampaignsTemplates::find( $org_tmp_id );


                $data[ 'template_id' ]   = $otmptemplate->id;
                $data[ 'template_path' ] = $otmptemplate->path;

                $template_path = 'template.' . $otmptemplate->path . '.index';
                $template_body = view( $template_path, $data )->render();

            }
         
            $template->body = $template_body;
            $template->original_template_id = $org_tmp_id;
            $template->save();
          
            return response()->json(
                [
                    'tmpl_body' => $template_body,
                    'template'  => $template,
                    'data'      => $data
                ]
            );


        } catch ( Exception $e ) {
            return response()->json( [ ] );
        }
    }

    public function save_user_template( \Illuminate\Http\Request $request )
    {
        try {


            $validator = \Validator::make( $request->all(), [
                'template_id'                   => 'required',
                'template_name'                 => 'required',
                'template_return_email_content' => 'required',
                'email_subject'                 => 'required',
                'redirect_after'                => 'required',
                'notification_email'            => 'required',
                'contact_link'                  => 'required',
                'privacy_link'                  => 'required',
                'therms_link'                   => 'required',
                'org_tmp_id'                    => 'required',
                'page_content'                  => 'required',
            ] );

            if ( $validator->fails() ) {


                return response()->json( [ 'errors' => $validator->errors()->all() ] );

            } else {
                $template = UserTemplates::find( \Input::get( 'template_id' ) );


                $html        = new Htmldom( \Input::get( 'page_content' ) );
                $ignoreparse = 'data-ignore';
                // Find all images
                foreach ( $html->find( 'a' ) as $element ) {

                    if ( isset( $element->href ) && !isset( $element->$ignoreparse ) ) {
                        $element->href = '{{$mailto_link}}';
                    }
                    if ( isset( $element->onclick ) ) {
                        $element->onclick = '{!!$redirect_aft!!}';
                    }
                    if ( isset( $element->class ) ) {
                        switch ( $element->class ) {
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
                foreach ( $html->find( 'meta' ) as $meta_elem ) {

                    if ( $meta_elem->name == 'template_id' ) {
                        $meta_elem->content = $template->id;
                    }
                    if ( $meta_elem->name == 'campaign_id' ) {
                        $meta_elem->content = $template->campaign_id;
                    }

                }
                $template_content = $html->save();


                $template->original_template_id = \Input::get( 'org_tmp_id' );
                $template->terms                = \Input::get( 'therms_link' );
                $template->privacy              = \Input::get( 'privacy_link' );
                $template->contact_us           = \Input::get( 'contact_link' );
                $template->notification_email   = \Input::get( 'notification_email' );
                $template->redirect_after       = \Input::get( 'redirect_after' );
                $template->email_subject        = \Input::get( 'email_subject' );
                $template->email_message        = \Input::get( 'template_return_email_content' );
                $template->name                 = \Input::get( 'template_name' );
                $template->body                 = $template_content;


                $template->save();

            }

            return response()->json( [ ] );


        } catch ( Exception $e ) {
            return response()->json( [ ] );
        }
    }

    public function remove_user_template()
    {
        $template_id = \Input::get( 'tempalte_id' );
        $user_id     = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        if ( !empty( $template_id ) && !empty( $user_id ) ) {
            try {
                $template = UserTemplates::where( 'user_id', '=', $user_id )->where( 'id', '=', $template_id )->first();
                if ( $template ) {
                    $template->delete();
                    SplitTestingStats::where( 'template', '=', intval( $template_id ) )->delete();
                    CampaignStats::where( 'template', '=', intval( $template_id ) )->delete();
                    return response()->json( [ 'status' => true ] );

                }
            } catch ( \Exception $e ) {

            }
        }
        return response()->json( [ 'status' => false ] );


    }

    public function campaigns_assinged( $cid )
    {
        $data               = [ ];
        $data[ 'campaign' ] = Campaigns::with( 'template' )->where( 'user_id', '=', Auth::id() )->where( 'id', '=', $cid )->firstOrFail();
        $data[ 'assinged' ] = User::with( 'profile', 'allowed_campaigns', 'role', 'owner' )->whereHas( 'owner', function ( $query ) {
            $query->where( 'owner_id', '=', Auth::id() );
        } )->whereHas( 'allowed_campaigns', function ( $query ) use ( $cid ) {
            $query->where( 'campaign_id', '=', $cid );
        } )->paginate( 10 )->setPath( 'campaigns/assinged /' . $cid );

        $data[ 'not_assinged' ] = User::with( 'profile', 'allowed_campaigns', 'role', 'owner' )->whereHas( 'owner', function ( $query ) {
            $query->where( 'owner_id', '=', Auth::id() );
        } )->whereHas( 'allowed_campaigns', function ( $query ) use ( $cid ) {
            $query->where( 'campaign_id', '=', $cid );
        }, ' != ' )->get()->lists( 'name', 'id' );

        return view( 'campaigns.assinged', $data );
    }

    public function save_campaigns_assinged()
    {
        $cid = \Input::get( 'id' );
        $uid = \Input::get( 'assing_other' );

        $user = User::with( 'profile', 'allowed_campaigns', 'role', 'owner' )->whereHas( 'owner', function ( $query ) {
            $query->where( 'owner_id', '=', Auth::id() );
        } )->whereHas( 'allowed_campaigns', function ( $query ) use ( $cid ) {
            $query->where( 'campaign_id', '=', $cid );
        }, ' != ' )->where( 'id', '=', $uid )->first();

        if ( isset( $user->id ) ) {

            $ua              = new UserAllowedCampaigns();
            $ua->user_id     = $uid;
            $ua->campaign_id = $cid;
            $ua->save();
            return redirect()->route( 'campaigns_assinged', [ 'id' => $cid ] )->withSuccess( 'user saved ' );
        }
        return redirect()->route( 'campaigns_assinged', [ 'id' => $cid ] )->withError( 'user not added ' );
    }

    public function remove_assingment( $cid, $uid )
    {
        $user = User::with( 'profile', 'allowed_campaigns', 'role', 'owner' )->whereHas( 'owner', function ( $query ) {
            $query->where( 'owner_id', '=', Auth::id() );
        } )->whereHas( 'allowed_campaigns', function ( $query ) use ( $cid ) {
            $query->where( 'campaign_id', '=', $cid );
        } )->where( 'id', ' = ', $uid )->first();
        if ( isset( $user->id ) ) {

            UserAllowedCampaigns::where( 'user_id', '=', $uid )->where( 'campaign_id', '=', $cid )->delete();
            return redirect()->route( 'campaigns_assinged', [ 'id' => $cid ] )->withSuccess( 'user removed ' );
        }
        return redirect()->route( 'campaigns_assinged', [ 'id' => $cid ] )->withError( 'user not removed ' );
    }


}
