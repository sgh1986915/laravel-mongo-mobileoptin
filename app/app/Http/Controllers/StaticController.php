<?php namespace MobileOptin\Http\Controllers;

use Clockwork\Clockwork;
use Htmldom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use MobileOptin\Models\FaqCategory;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MobileOptin\Models\FaqAnswers;
use Illuminate\Database\Query\Builder;
use MobileOptin\Models\ExpertCategory;
use MobileOptin\Models\ExpertAnswers;
use MobileOptin\Models\Package;
use MobileOptin\Models\UserProfile;

class StaticController extends Controller
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
        if(str_replace(' ', '', strtolower(Auth::user()->role->role_title)) == 'normaluser'){
        	return redirect()->route('campaigns');
        }
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function support($faq_category_id = null)
    {


        if ( \Request::isMethod( 'post' ) ) {

            $validator = \Validator::make(
                \Input::all(),
                [
                    'name'    => 'required',
                    'email'   => 'required|email',
                    'message' => 'required',
                ]
            );

            if ( $validator->fails() ) {

                return redirect()->back()->withInput( \Input::all() )->withErrors( $validator->errors() );
            } else {
                $sender_name    = \Input::get( 'name' );
                $sender_email   = \Input::get( 'email' );
                $sender_message = \Input::get( 'message' );

                Mail::send( 'emails.support_form', [ 'name' => $sender_name, 'email' => $sender_email, 'msg' => $sender_message ], function ( $m ) use ( $sender_email, $sender_name ) {
                    $m->to( 'support@mobileoptin.com' )->subject( 'Support Form!' );
                     $m->from( $sender_email, $sender_name );
                } );
                return redirect()->back()->withSuccess( 'Message sent' );
            }
        } else {
            return view( 'static/support');
        }


    }
    
    public function faq($faq_category_id = null){
    	$data = [];
    	$data['faq_category']           = FaqCategory::all();
    	if($data['faq_category']->count() > 0){
    		$data['faq_category_selected']  = $faq_category_id !== null && (int)$faq_category_id > 0 ? $faq_category_id : $data['faq_category']->first()->id;
    		$data['faq_category_answer']    = FaqAnswers::where('faq_category_id', $data['faq_category_selected'], false)->get(array('*'));
    	}
    	return view( 'static/faq', $data );
    }
    
    public function expert($faq_category_id = null){
    	
    	$userProfile = UserProfile::where('user_id', Auth::id())->with('package')->first();
    	
    	if(!empty($userProfile) && !empty($userProfile->package) && $userProfile->package->traffic_experts_academy == true){
    		$data = [];
    		$data['faq_category']           = ExpertCategory::all();
    		 
    		if(!empty($data['faq_category']) && $data['faq_category']->count() > 0){
    			$data['faq_category_selected']  = $faq_category_id !== null && (int)$faq_category_id > 0 ? $faq_category_id : $data['faq_category']->first()->id;
    			$data['faq_category_answer']    = ExpertAnswers::where('faq_category_id', $data['faq_category_selected'], false)->get(array('*'));
    		}
    		return view( 'static/expert', $data );
    	}else{
    		return Redirect::to('http://www.mobileoptinlive.com/trafficexpertacademy/');
    	}
    }


}
