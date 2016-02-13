<?php

/**
 *
 * User: Damir djikic
 * damir@cod3.me , ddjikic@gmail.com
 * website www.cod3.me
 * Date: 5/11/15
 * Time: 9:53 PM
 */

namespace MobileOptin\Models;

use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Model as Eloquent;
use Carbon\Carbon;

class SplitTestingStats extends Eloquent {

    protected $collection = 'split_testing_stats';
    protected $connection = 'mongodb';

    public static function record_event($campaign_id, $template_id, $event, $label, $name, $value, $ip=null)
    {
        if (isset($ip)) {
            $request_id = $ip;
        } else {
            $request_id = \Request::ip();
        }

        $today = new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->timestamp);

        $campaign_clicks = SplitTestingStats::where('campaign_id', intval($campaign_id))
                        ->whereBetween('created_at', array(new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->subDay()->timestamp), new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->timestamp)))
                        ->where('id_address', $request_id)->get();

        if($event == 'navigate' || $event == 'optin' || $event == 'pixel'){
                        $stat = new static;
            $stat->id_address = $request_id;
            $stat->event = $event;
            $stat->label = $label;
            $stat->name = $name;
            $stat->value = $value;
            $stat->campaign_id = intval($campaign_id);
            $stat->is_ajax = \Request::ajax();
            $stat->referer = \Request::server('HTTP_REFERER');
            $stat->template = intval($template_id);
            $stat->created_at = $today;
            $stat->updated_at = $today;
            $stat->save();
            return $stat;
        }else{
        if (count($campaign_clicks) == 0) {
            $stat = new static;
            $stat->id_address = $request_id;
            $stat->event = $event;
            $stat->label = $label;
            $stat->name = $name;
            $stat->value = $value;
            $stat->campaign_id = intval($campaign_id);
            $stat->is_ajax = \Request::ajax();
            $stat->referer = \Request::server('HTTP_REFERER');
            $stat->template = intval($template_id);
            $stat->created_at = $today;
            $stat->updated_at = $today;
            $stat->save();
            return $stat;
        } else {
            return false;
        }}
    }

    /**
     * sort stats table data ( the one in list campaigns)
     * @param $campaignIdArray
     * @return array
     */
    public static function getMultiBasicInfo($campaignIdArray, $from = '', $to = '', $addrow = false) {
        $time_start = microtime(true);
        $begin = false;
        $end = false;
        // format those dates so they inlude some final time stamp and if equa add +-1 day so the graph will look normal
//        $return = Cache::store( 'memcached' )->get( 'agmbci_' . join( '_', $campaignIdArray ).'_'.$from.'_'.$to );
//        $return=false;
//        if ( !$return ) {
        if (!empty($from) && !empty($to)) {
            $begin = new \DateTime(date('Y-m-d', strtotime($from)) . ' 00:00:00', new \DateTimeZone('America/New_York'));
            $end = new \DateTime(date('Y-m-d', strtotime($to)) . ' 23:59:59', new \DateTimeZone('America/New_York'));
        }


//        if ( $from == $to ) {
//            $begin = new \DateTime( date( 'Y-m-d', strtotime( $from . ' -1 day' ) ) . ' 00:00:00' );
//           $end   = new \DateTime( date( 'Y-m-d', strtotime( $to . ' +1 day' ) ) . ' 23:59:59' );
//        } else {
        //     $begin = new \DateTime( date( 'Y-m-d', strtotime( $from ) ) . ' 00:00:00', new \DateTimeZone( 'America/New_York' ) );
        //    $end   = new \DateTime( date( 'Y-m-d', strtotime( $to ) ) . ' 23:59:59', new \DateTimeZone( 'America/New_York' ) );
//        }


        $splt = static::select('event', 'campaign_id', 'template', 'label', 'created_at')->whereIn('campaign_id', $campaignIdArray);


        if ($begin !== false && $end !== false) {
            $splt->whereBetween('created_at', [
                new \MongoDate(strtotime($begin->format('d-m-Y H:i:s'))),
                new \MongoDate(strtotime($end->format('d-m-Y H:i:s')))
                    ]
            );
        }
        $splt = $splt->get();


        $return = [];
        $diff_templates = [];


//            foreach ( $splt as $spl ) {
//                // set some defaults
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'total_opened' ] = 0;
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'total_mailto' ] = 0;
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'total_out' ] = 0;
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'total_events' ] = 0;
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'read_page' ] = 0;
//
//                $return[ $spl->campaign_id ][ $spl->template ][ 'conversion' ] = 0;
//            }

        $time_end = microtime(true);
        $time = $time_end - $time_start;


        foreach ($splt as $spl) {

            //events
            if ($spl->event != 'page_open' && $spl->event != 'read') {
                if (isset($return[$spl->campaign_id][$spl->template]['total_events'])) {
                    $return[$spl->campaign_id][$spl->template]['total_events'] += 1;
                } else {
                    $return[$spl->campaign_id][$spl->template]['total_events'] = 1;
                }

                //      $return[ $spl->campaign_id ][ $spl->template ][ 'total_events' ] += 1;
            }

            if ($spl->event == 'page_open') {
                if (isset($return[$spl->campaign_id][$spl->template]['total_opened'])) {
                    $return[$spl->campaign_id][$spl->template]['total_opened'] += 1;
                } else {
                    $return[$spl->campaign_id][$spl->template]['total_opened'] = 1;
                }
            }
            if ($spl->event == 'navigate') {
                if ($spl->label == 'mailto') {
                    if (isset($return[$spl->campaign_id][$spl->template]['total_mailto'])) {
                        $return[$spl->campaign_id][$spl->template]['total_mailto'] += 1;
                    } else {
                        $return[$spl->campaign_id][$spl->template]['total_mailto'] = 1;
                    }

                    // $return[ $spl->campaign_id ][ $spl->template ][ 'total_mailto' ] += 1;
                } elseif ($spl->label == 'out') {
                    if (isset($return[$spl->campaign_id][$spl->template]['total_out'])) {
                        $return[$spl->campaign_id][$spl->template]['total_out'] += 1;
                    } else {
                        $return[$spl->campaign_id][$spl->template]['total_out'] = 1;
                    }

                    //   $return[ $spl->campaign_id ][ $spl->template ][ 'total_out' ] += 1;
                }
            }
            if ($spl->event == 'read') {
                if (isset($return[$spl->campaign_id][$spl->template]['read_page'])) {
                    $return[$spl->campaign_id][$spl->template]['read_page'] += 1;
                } else {
                    $return[$spl->campaign_id][$spl->template]['read_page'] = 1;
                }
                //      $return[ $spl->campaign_id ][ $spl->template ][ 'read_page' ] += 1;
            }

            
            $diff_templates[$spl->template] = $spl->template;
            if ($addrow) {
                $return[$spl->campaign_id]['raw'][] = $spl;
            }
        }

        $usedTemplates = UserTemplates::whereIn('id', $diff_templates)->get();
        foreach ($usedTemplates as $ut) {
            foreach ($return as $c_Id => $tmpldata) {
                foreach ($tmpldata as $tid => $tinfo) {
                    if ($ut->id == $tid) {
                        $return[$c_Id] [$tid]['name'] = $ut->name;
                        if (empty($ut->name)) {
                            static::whereIn('campaign_id', [intval($tid)])->delete();
                        }
                    }
                }
            }
        }


//            $expiresAt = Carbon::now()->addMinutes( 5 );

//            Cache::store( 'memcached' )->put( 'agmbci_' . join( '_', $campaignIdArray ).'_'.$from.'_'.$to, $return, $expiresAt );
     //  }

        return $return;
    }

    public static function detailedCampaignStats($campaign_id, $from = '', $to = '', $start = 0, $length = 10, $order = [ 0 => [ 'column' => 4, 'dir' => 'desc']]) {

//        $return = Cache::store( 'memcached' )->get( 'singlecampaigndata_' . join( '_', $campaign_id ) );
//        if ( !$return ) {
        // make some predefined date if they are empty
        if (empty($from)) {
            $from = '-30 Days';
        }
        if (empty($to)) {
            $to = '';
        }
        // format those dates so they inlude some final time stamp and if equa add +-1 day so the graph will look normal

        if ($from == $to) {
            $begin = new \DateTime(date('Y-m-d', strtotime($from . ' -1 day')) . ' 00:00:00', new \DateTimeZone('America/New_York'));
            $end = new \DateTime(date('Y-m-d', strtotime($to . ' +1 day')) . ' 23:59:59', new \DateTimeZone('America/New_York'));
        } else {
            $begin = new \DateTime(date('Y-m-d', strtotime($from)) . ' 00:00:00', new \DateTimeZone('America/New_York'));
            $end = new \DateTime(date('Y-m-d', strtotime($to)) . ' 23:59:59', new \DateTimeZone('America/New_York'));
        }
        // make the datatables order readable
        $order_by = 'created_at';
        $order_directon = 'desc';
        switch ($order[0]['column']) {
            case 0:
                $order_by = 'created_at';
                break;
            case 1:
                $order_by = 'label';
                break;
            case 2:
                $order_by = 'name';
                break;
            case 3:
                $order_by = 'referer';
                break;
            case 4:
            default:
                $order_by = 'created_at';
                break;
        }
        if ($order[0]['dir'] == 'asc') {
            $order_directon = 'asc';
        }

        // pull from mongo all the needed data
        $all_data = static::where('campaign_id', intval($campaign_id))->
                        whereBetween('created_at', [
                            new \MongoDate(strtotime($begin->format('d-m-Y H:i:s'))),
                            new \MongoDate(strtotime($end->format('d-m-Y H:i:s')))
                                ]
                        )->orderBy($order_by, $order_directon)->get();


        // predefine some standard format stuff
        $return = [
            'data' => [],
            'graph' => [ 'labels' => [], 'datasets' => []],
            'summary' => [],
            'draw' => \Input::get('draw'),
            'recordsTotal' => $all_data->count(),
            'recordsFiltered' => $all_data->count()
        ];
        // predefine some needed arrays
        $graph_raw = [];
        $templates = [];
        $diff_templates = [];
        $template_name_array = [];

        // fill the array of labels for the graph ( X coordinate )
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        $graph_data_pd = [];
        foreach ($period as $dt) {
            $return['graph']['labels'][] = date('m-d-Y', strtotime($dt->format("Y-m-d")));
            $graph_data_pd[strtotime($dt->format("Y-m-d"))] = 0;
        }

        foreach ($all_data as $record) {
//            if ( $record->event == 'navigate' ) {
            $graph_raw[$record->template] = $graph_data_pd;
//            }
            $diff_templates[] = $record->template;

//            $graph_raw[ $record->template ][ strtotime( date( 'Y-m-d', strtotime( $record->created_at ) ) ) ] = 0;
        }
        //get all diferent templates used because joining mongo and mysql can not work

        $usedTemplates = UserTemplates::whereIn('id', $diff_templates)->get();
        foreach ($usedTemplates as $ut) {
            $template_name_array[$ut->id] = $ut->name;
        }
        // frst pass trough saved events
        $sc = 0;
        $lc = 0;

        $unique = [];

        foreach ($all_data as $record) {
            $row = [];
            // make the event look nicer
            switch ($record->event) {
                case 'page_open':
                    $row['event'] = 'Landed';
                    break;
                case 'navigate':
                    if ($record->label == 'out') {
                        $row['event'] = 'Exit';
                    } elseif ($record->label == 'mailto') {
                        $row['event'] = 'Open Email';
                    } else {
                        $row['event'] = '';
                    }
                    break;
                case 'read':
                    $row['event'] = 'read the page';
                    break;
                case 'optin':
                    $row['event'] = 'Optin';
                    break;
                case 'pixel':
                    $row['event'] = 'Conversion';
                    break;
            }

            $row['link_text'] = ( $record->event == 'page_open' ) ? ( '' ) : ( $record->name );
            $row['time'] = date('Y-m-d H:i:s', strtotime($record->created_at));
            $row['template_id'] = $record->template;
            $row['from'] = $record->referer;
            $row['name'] = ( isset($template_name_array[$record->template]) ) ? ( $template_name_array[$record->template] ) : ( '' );

            //$row['debug'] = $record; // TODO: delete this line

            if ($sc >= $start && $lc <= $length) {
                $lc = $lc + 1;
                $return['data'][] = $row;
            }

            // add all Optin events to the graph data
            if($row['event'] == 'Optin') {
                $graph_raw[$record->template][strtotime(date('Y-m-d', strtotime($record->created_at)))] += 1;
            }

            // collect summary data for the template: impressions, clicks, opt-ins, and conversions
            if(!isset($return['summary'][$record->template]))
            {
                $pixel_url = route('get_pixel', ['user_template_id'=>$record->template]);
                //$pixel_img = htmlspecialchars("<img src='".$pixel_url."'>");
                $return['summary'][$record->template] = [
                    'name' => $row['name'],
                    'id' => $record->template,
                    'pixel_url' => $pixel_url,
                    'total_events' => 0, // ?
                    'total_out' => 0, // ?
                    'read_page' => 0, // ?
                    'total_unique' => 0,
                    'total_opened' => 0, // "Landed", impressions
                    'total_mailto' => 0, // "Open Email", clicks
                    'click_percent' => 0, // (total_mailto / total_opened) NOTE: this is NOT a count of pixel hits
                    'total_optin' => 0,
                    'optin_percent' => 0, // (total_optin / total_opened)
                    'total_pixel' => 0,
                    'pixel_percent' => 0, // (total_pixel / total_opened)
                ];
                $unique[$record->template] = [];
            }

            if (!isset($unique[$record->template][md5($record->id_address . date('y-m-d', strtotime($record->created_at)))])) {
                $unique[$record->template][md5($record->id_address . date('y-m-d', strtotime($record->created_at)))] = 1;
            }

            if($record->event != 'page_open' && $record->event != 'read')
            {
                $return['summary'][$record->template]['total_events'] += 1;
            }


            if($record->event == 'page_open')
            {
                $return['summary'][$record->template]['total_opened'] += 1;
            }

            if($record->event == 'navigate' && $record->label == 'mailto')
            {
                $return['summary'][$record->template]['total_mailto'] += 1;
            }

            if($record->event == 'navigate' && $record->label == 'out')
            {
                $return['summary'][$record->template]['total_out'] += 1;
            }

            if($record->event == 'read')
            {
                $return['summary'][$record->template]['read_page'] += 1;
            }

            if($record->event == 'optin')
            {
                $return['summary'][$record->template]['total_optin'] += 1;
            }

            if($record->event == 'pixel')
            {
                $return['summary'][$record->template]['total_pixel'] += 1;
            }

            // calculate percentages of visitors
            if ($return['summary'][$record->template]['total_opened'] > 0)
            {
                $return['summary'][$record->template]['click_percent'] = ( round($return['summary'][$record->template]['total_mailto'] / $return['summary'][$record->template]['total_opened'], 3) * 100 ) . '%';

                $return['summary'][$record->template]['optin_percent'] = ( round($return['summary'][$record->template]['total_optin'] / $return['summary'][$record->template]['total_opened'], 3) * 100 ) . '%';

                $return['summary'][$record->template]['pixel_percent'] = ( round($return['summary'][$record->template]['total_pixel'] / $return['summary'][$record->template]['total_opened'], 3) * 100 ) . '%';
            }

            /*
            if (isset($return['summary'][$record->template]['total_events'])) {
                $return['summary'][$record->template]['total_events'] += 1;
            } else {
                $return['summary'][$record->template]['total_events'] = 1;
            }
            */

            $sc = $sc + 1;
        }

        foreach($unique as $template => $items)
        {
            $return['summary'][$template]['total_unique'] = count($items);
        }

        $return['summary'] = array_values($return['summary']); // re-index array from 0
        //$return['recordsTotal'] = count($return['summary']->count());
        //$return['recordsFiltered'] = count($return['summary']);

//        sort( $graph_raw[ $record->template ] );
//         get the other part of graph data
        // \Clockwork::info( $graph_raw );
        foreach ($graph_raw as $tid => $tv) {
            $color = rand(0, 360) . ',60%,70%';
            $finaldata = [
                'label' => 'Removed template',
                'fillColor' => 'hsla(' . $color . ',0.2)',
                'strokeColor' => 'hsla(' . $color . ',0.5)',
                'pointColor' => 'hsla(' . $color . ',1)',
                'pointStrokeColor' => 'hsla(' . $color . ',1)',
                'pointHighlightFill' => 'hsla(' . $color . ',1)',
                'pointHighlightStroke' => 'hsla(' . $color . ',1)',
                'data' => array_values($tv),
            ];
            foreach ($usedTemplates as $usedTemplate) {
                if ($usedTemplate->id == $tid) {
                    $finaldata['label'] = $usedTemplate->name;
                }
            }
            $return['graph']['datasets'][] = $finaldata;
        }
        // Generate statistic summary
        //$data[ 'summary' ] = static::getMultiBasicInfo( $campaign_id );

        $expiresAt = Carbon::now()->addMinutes(5);

//            Cache::store( 'memcached' )->put( 'singlecampaigndata_' . join( '_', $campaign_id ), $return, $expiresAt );
//        }
        return $return;
    }

    public static function getCombinedStats($from, $to, $showonly = '') {

        // make some predefined date if they are empty
        if (empty($from)) {
            $from = '-30 Days';
        }
        if (empty($to)) {
            $to = 'today';
        }
        // format those dates so they inlude some final time stamp and if equa add +-1 day so the graph will look normal

        if ($from == $to) {
            $begin = new \DateTime(date('Y-m-d', strtotime($from . ' -1 day')) . ' 00:00:00', new \DateTimeZone('America/New_York'));
            $end = new \DateTime(date('Y-m-d', strtotime($to . ' +1 day')) . ' 23:59:59', new \DateTimeZone('America/New_York'));
        } else {
            $begin = new \DateTime(date('Y-m-d', strtotime($from)) . ' 00:00:00', new \DateTimeZone('America/New_York'));
            $end = new \DateTime(date('Y-m-d', strtotime($to)) . ' 23:59:59', new \DateTimeZone('America/New_York'));
        }


        // pull from mongo all the needed data
        $all_data = static::whereBetween('created_at', [
                    new \MongoDate(strtotime($begin->format('d-m-Y H:i:s'))),
                    new \MongoDate(strtotime($end->format('d-m-Y H:i:s')))
                        ]
                )->get();


        $return = [
            'total_opened' => 0,
            'total_unique' => 0,
            'total_mailto' => 0,
            'total_out' => 0,
            'total_events' => 0,
            'read_page' => 0,
            'conversion' => 0,
        ];
        $unique = [];
        foreach ($all_data as $record) {

            //events
            if ($record->event != 'page_open' && $record->event != 'read') {
                $return ['total_events'] += 1;
                if (!isset($unique[md5($record->id_address . date('y-m-d', strtotime($record->created_at)))])) {
                    $unique[md5($record->id_address . date('y-m-d', strtotime($record->created_at)))] = 1;
                }
            }

            if ($record->event == 'page_open') {
                $return ['total_opened'] += 1;
            }
            if ($record->event == 'navigate') {
                if ($record->label == 'mailto') {
                    $return ['total_mailto'] += 1;
                } elseif ($record->label == 'out') {
                    $return ['total_out'] += 1;
                }
            }
            if ($record->event == 'read') {
                $return['read_page'] += 1;
            }
            if ($return ['total_opened'] > 0) {
                $return ['conversion'] = ( round($return ['total_mailto'] / $return ['total_opened'], 3) * 100 ) . '%';
            }
        }
        $return ['total_unique'] = count($unique);
        if (!empty($showonly)) {
            switch ($showonly) {
                case 'total_opened':
                    $return = array_intersect_key($return, [ 'total_opened' => true]);
                    break;
                case 'total_unique':
                    $return = array_intersect_key($return, [ 'total_unique' => true]);
                    break;
                case 'total_mailto':
                    $return = array_intersect_key($return, [ 'total_mailto' => true]);
                    break;
                case 'total_mailto':
                    $return = array_intersect_key($return, [ 'total_mailto' => true]);
                    break;
                case 'total_out':
                    $return = array_intersect_key($return, [ 'total_out' => true]);
                    break;
                case 'total_events':
                    $return = array_intersect_key($return, [ 'total_events' => true]);
                    break;
                case 'read_page':
                    $return = array_intersect_key($return, [ 'read_page' => true]);
                    break;
                case 'conversion':
                    $return = array_intersect_key($return, [ 'conversion' => true]);
                    break;
            }
        }
        return $return;
    }

}
