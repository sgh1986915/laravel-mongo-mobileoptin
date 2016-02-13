<?php namespace MobileOptin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use MobileOptin\Models\Campaigns;
use MobileOptin\Models\UserContent;
use MobileOptin\Models\SplitTestingStats;
use MobileOptin\Models\UserTemplates;

class HomeController extends Controller
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
    public function __construct()
    {
        $this->middleware( 'auth' );
        parent::__construct();
    }

    
    private function loadLastDateOfAnnoncement($data){
    	return UserContent::select( 'id, content, created_at, updated_at' )->where( 'id', '=', 1 )->get();
    }
    
    private function checkCurrentAnnouncementCookiesParameter($data){
    	$announcement_created_at = $request->cookie('announcement_created_at');
    	if(!empty($announcement_created_at)){
    		return false;
    	}
    	return true;
    }
    
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        $time_start = microtime(true);
        \SEOMeta::setTitle( 'Dashboard' );

        \SEOMeta::setDescription( 'Dash board' );
        \SEOMeta::addKeyword( [ 'stats' ] );

        $user_id = Auth::user()->getOwner() ? Auth::user()->getOwner() : Auth::id();

        $data[ 'campaigns' ] = Campaigns::select( 'id' )->where( 'user_id', '=', $user_id )->get();

        $cids = [ ];

        foreach ( $data[ 'campaigns' ] as $c ) {
            $cids[ ] = $c->id;
        }


        $data[ 'splitTestStats' ] = SplitTestingStats::getMultiBasicInfo( $cids, '-7days', 'now', true );


        $data[ 'has_embed' ]  = Auth::user()->getProfileOption( 'embed' );
        $data[ 'has_hosted' ] = Auth::user()->getProfileOption( 'hosted' );
        
        //load last date of announced
        $data['announcement_params']   = loadLastDateOfAnnoncement();
        Log::info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        Log::info(json_encode($data['announcement_params']));
        Log::info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        
        $data['openAnnouncementModal'] = false;
        if(!empty($data['announcement_params'])){
        	//check with current cookie create if exists
        	$data['openAnnouncementModal'] = checkCurrentAnnouncementCookiesParameter($data['announcement_params'] );
        }
        
        Log::info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        Log::info("[Open Modal ] " . ( $data['openAnnouncementModal'] ? 'true' : 'false' ) );
        Log::info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        
        $cidsforpagi = [ ];

        foreach ( $data[ 'splitTestStats' ] as $k => $v ) {
            $cidsforpagi[ ] = $k;
        }

        if ( !empty( $cidsforpagi ) ) {

            $data[ 'campaigns' ] = Campaigns::whereIn( 'id', $cidsforpagi )->where( 'user_id', '=', $user_id )->with( 'template' )->paginate( 10 )->setPath( 'campaigns' );
        }

        $data[ 'stats_for_doughnut' ] = [ 'Visitors' => 0, 'bounce' => 0, 'Clicked' => 0, ];
        $data[ 'stats_for_line' ]     = [ ];
        $data[ 'graph_labels' ]       = [ ];

        $interval = \DateInterval::createFromDateString( '1 day' );
        $period   = new \DatePeriod( new \DateTime( date( 'Y-m-d', strtotime( ' -7 day' ) ) . ' 00:00:00' ),
            $interval,
            new \DateTime( date( 'Y-m-d', strtotime( ' +1day' ) ) . ' 23:59:59' )
        );

        foreach ( $period as $dt ) {
            $data[ 'graph_labels' ][ ] = date( 'm-d-Y', strtotime( $dt->format( "Y-m-d" ) ) );

            $data[ 'stats_for_line' ][ 'visits' ][ strtotime( $dt->format( "Y-m-d" ) ) ] = 0;
            $data[ 'stats_for_line' ][ 'clicks' ][ strtotime( $dt->format( "Y-m-d" ) ) ] = 0;
        }
         foreach ( $data[ 'splitTestStats' ] as $mck => $stsfct ) {

            foreach ( $stsfct as $key => $tstast ) {
                if ( $key != 'raw' ) {
                    
                      if(isset($tstast[ 'total_opened' ])){
                      $data[ 'stats_for_doughnut' ][ 'Visitors' ] += $tstast[ 'total_opened' ];}else{
                          $data[ 'stats_for_doughnut' ][ 'Visitors' ] += 0;
                      }

                        if(isset($tstast[ 'total_opened' ]) && isset($tstast[ 'read_page' ])){
                        $data[ 'stats_for_doughnut' ][ 'bounce' ] += $tstast[ 'total_opened' ] - $tstast[ 'read_page' ];}else{
                            if(isset($tstast[ 'total_opened' ])){
                                  $data[ 'stats_for_doughnut' ][ 'bounce' ] += $tstast[ 'total_opened' ];
                            }else{
                                $data[ 'stats_for_doughnut' ][ 'bounce' ] += 0;
                            }
                        }


                    if(isset($tstast[ 'total_events' ])){
                    $data[ 'stats_for_doughnut' ][ 'Clicked' ] += $tstast[ 'total_events' ];}else{
                       $data[ 'stats_for_doughnut' ][ 'Clicked' ] += 0; 
                    }

                } elseif($key=='raw') {
                    foreach ( $tstast as $rwtstast ) {
                        if ( $rwtstast->event != 'page_open' && $rwtstast->event != 'read' ) {
                            @$data[ 'stats_for_line' ][ 'clicks' ][ strtotime( date( 'Y-m-d', strtotime( $rwtstast->created_at ) ) ) ] += 1;
                        }
                        if ( $rwtstast->event == 'page_open' ) {
                            @$data[ 'stats_for_line' ][ 'visits' ][ strtotime( date( 'Y-m-d', strtotime( $rwtstast->created_at ) ) ) ] += 1;
                        }
                    }

                }

            }
            unset( $data[ 'splitTestStats' ] [ $mck ][ 'raw' ] );

        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        //lotery to delete old templates belonging to this user

        UserTemplates::cleanOld();


        return view( 'home', $data );
    }

}
