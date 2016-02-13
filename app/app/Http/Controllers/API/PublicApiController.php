<?php

namespace MobileOptin\Http\Controllers\API;

use Jenssegers\Agent\Facades\Agent;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MobileOptin\Models\SplitTestingStats;

class PublicApiController extends Controller {

    /**
     * save click on the web page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function __construct() {
        parent::__construct();
    }

    public function postTrackEvent() {
        $campaign_id = \Input::get('campaign_id');
        $template_id = \Input::get('template_id');
        $event = \Input::get('event');
        $label = \Input::get('label');
        $name = \Input::get('name');
        $value = \Input::get('value');

        if (( Agent::isMobile() || Agent::isTablet() ) && !Agent::isRobot()) {
            $save_response = SplitTestingStats::record_event($campaign_id, $template_id, $event, $label, $name, $value);
            return response()->json(
                            [
                                'status' => true,
                            ]
            );
        } else {
            return response()->json(
                            [
                                'status' => false,
                            ]
            );
        }
    }

    public function getStats($showonly = '') {
        $from = \Input::get('from');
        $to = \Input::get('to');

        $data = SplitTestingStats::getCombinedStats($from, $to, $showonly);

        return response()->json(
                        [
                            'data' => $data,
                            'status' => true,
                        ]
        );
    }
    
    

}
