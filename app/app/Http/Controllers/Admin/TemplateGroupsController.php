<?php namespace MobileOptin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;

use Illuminate\Http\Request;
use MobileOptin\Models\CampaignsTemplates;
use MobileOptin\Models\TemplatesGroups;

class TemplateGroupsController extends Controller
{


    public function __construct()
    {


            parent::__construct();
         $this->middleware( 'auth' );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        \SEOMeta::setTitle( 'Templates Groups mManagement' );

        \SEOMeta::setDescription( 'Templates groups Management' );
        \SEOMeta::addKeyword( [ 'templates' ] );

        $data = [ 'admin_navigation' => Auth::user()->hasRole( 'admin' ) ? 1 : 0 ];
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => 1 ];
        } else {
            $data = [ ];
        }
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data[ 'TemplatesGroups' ] = TemplatesGroups::with( 'templates' )->paginate( 10 )->setPath( 'groups' );
        }
        return \Response::view( 'admin.templates.groups.list', $data );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        $data = [ 'admin_navigation' => 1 ];

        \SEOMeta::setTitle( 'Add - Template Groups' );


        $groups          = new \stdClass();
        $groups->id      = 0;
        $groups->name    = \Input::old( 'name' );

        $data[ 'group' ] = $groups;

        return view( 'admin.templates.groups.add_edit', $data );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function upsert()
    {
        // getting all of the post data
        $input = [

            'name'    => \Input::get( 'name' ),

        ];
        // setting up rules
        $rules = [

            'name'    => 'required',
         ];
        // doing the validation, passing post data, rules and the messages
        $validator = Validator::make( $input, $rules );
        if ( $validator->fails() ) {
            // send back to the page with the input data and errors
            return \Redirect::back()->withInput()->withErrors( $validator );
        } else {
            $template = TemplatesGroups::findOrCreate( \Input::get( 'id' ) );

            $template->name   = \Input::get( 'name' );
            $template->save();
            return \Redirect::route( 'admin.templates.groups.list' )->withSuccess( 'Template Group Saved' );
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit( $id )
    {
        $data = [ 'admin_navigation' => 1 ];

        \SEOMeta::setTitle( 'Edit - Template  Group' );


        $data[ 'group' ] = TemplatesGroups::find( $id );


        return view( 'admin.templates.groups.add_edit', $data );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy( $id )
    {
        $template = TemplatesGroups::find( $id );
        if ( $template ) {
            try {




                $template->delete();
                return redirect()->back()->withSuccess( 'Template group deleted' );
            } catch ( Exception $e ) {

            }
        }
        return redirect()->back()->withError( 'Template goup not deleted' );
    }

}
