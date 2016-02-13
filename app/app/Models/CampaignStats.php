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

use Jenssegers\Mongodb\Model as Eloquent;
use Carbon\Carbon;

class CampaignStats extends Eloquent
{
    protected $collection = 'campaign_stats';
    protected $connection = 'mongodb';

    public static function record_event( $campaign_id, $template_id, $org_template_id, $type = 'visit' )
    {
        $request_id = \Request::ip();
       
   
      //  $dt    = new \DateTime( date( 'Y-m-d H:i:s' ), new \DateTimeZone( 'America/New_York' ) );
     //   $ts    = $dt->getTimestamp();
        $today = new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->timestamp);
        
         $campaign_clicks = CampaignStats::where('campaign_id', intval( $campaign_id ))
        ->whereBetween('created_at', array(new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->subDay()->timestamp), new \MongoDate(Carbon::now(new \DateTimeZone('America/New_York'))->timestamp)))
        ->where( 'id_address',$request_id)->get();
     
        if(count($campaign_clicks) == 0){
            $stat                  = new static;
            $stat->id_address      = $request_id;
            $stat->type            = 'visit';
            $stat->campaign_id     = $campaign_id;
            $stat->is_ajax         = \Request::ajax();
            $stat->referer         = \Request::server( 'HTTP_REFERER' );
            $stat->template        = $template_id;
            $stat->org_template_id = $org_template_id;
            $stat->created_at  = $today;
            $stat->updated_at  = $today;
            $a = $stat->save();
        }      


    }

//    public function calculationsCount( $tmp_id )
//    {
//        return $this->raw( function ( $collection ) use ( $tmp_id ) {
//            return $collection->aggregate( [
//                [
//                    '$group' => [
//                        '_id'   => '$template',
//                        'count' => [
//                            '$sum' => 1
//                        ],
//                        'where' => [
//                            '$campaign_id' => $tmp_id
//                        ]
//                    ]
//                ],
//            ] );
//        } );
//    }
}






