<?php namespace MobileOptin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;

use Illuminate\Http\Request;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\CampaignStats;
use MobileOptin\Models\SplitTestingStats;

class StatsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    var $user_id = null;

    function __construct()
    {
        parent::__construct();
        if ( !Auth::guest() ) {
            $this->user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();
        }


    }

    public function show( $campaign_id )
    {


        $data[ 'campaign' ] = Campaigns::with( 'template' )->where( 'user_id', $this->user_id )->where( 'id', $campaign_id )->first();

        if ( empty( $data[ 'campaign' ] ) ) {
            abort( 404 );
        }

        return view( 'split_test.view', $data );

    }

    public function get_data( $campaign_id )
    {

        $from   = \Input::get( 'datefrom' );
        $to     = \Input::get( 'dateto' );
        $start  = \Input::get( 'start' );
        $length = \Input::get( 'length' );
        $order  = \Input::get( 'order' );

        $res = SplitTestingStats::detailedCampaignStats( $campaign_id, $from, $to, $start, $length, $order );

        return response()->json( $res );
    }

    public function reset( $campaign_id )
    {
        if ( !empty( $campaign_id ) ) {
            $campaign_data = Campaigns::where( 'user_id', $this->user_id )->where( 'id', $campaign_id )->first();
            if ( isset( $campaign_data->id ) ) {
                SplitTestingStats::where( 'campaign_id', '=', intval( $campaign_data->id ) )->delete();
                CampaignStats::where( 'campaign_id', '=', intval( $campaign_data->id ) )->delete();
              return  redirect()->back()->withSuccess('reset done');
            }
        }
      return  redirect()->back()->withErrors('not reseted');
    }

}
