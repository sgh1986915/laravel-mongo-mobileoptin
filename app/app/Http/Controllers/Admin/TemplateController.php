<?php namespace MobileOptin\Http\Controllers\Admin;

use Chumper\Zipper\Facades\Zipper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use MobileOptin\Http\Requests;
use MobileOptin\Http\Controllers\Controller;

use Illuminate\Http\Request;
use MobileOptin\Models\CampaignsTemplates;
use MobileOptin\Models\TemplatesGroups;
use PhpSpec\Exception\Exception;

class TemplateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware( 'auth' );

             parent::__construct();

    }

    public function index()
    {
        \SEOMeta::setTitle( 'Templates management' );

        \SEOMeta::setDescription( 'Templates management' );
        \SEOMeta::addKeyword( [ 'templates' ] );

        $data = [ 'admin_navigation' => Auth::user()->hasRole( 'admin' ) ? 1 : 0 ];
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => 1 ];
        } else {
            $data = [ ];
        }
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data[ 'templates' ] = CampaignsTemplates::with( 'user_templates' )->paginate( 10 )->setPath( 'templates' );
        }
        return \Response::view( 'admin.templates.list', $data );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {


        $data = [ 'admin_navigation' => 1 ];

        \SEOMeta::setTitle( 'Add - Template ' );
        $views_path = \Config::get( 'view.paths' );

        $content_blank = \File::get( $views_path[ 0 ] . '/admin/templates/blank_template.txt' );

        $template          = new \stdClass();
        $template->id      = 0;
        $template->name    = \Input::old( 'name' );
        $template->content = \Input::old( 'content', $content_blank );
        $template->group_id = \Input::old( 'group_id' );

        $data[ 'template' ] = $template;
        $data[ 'groupes' ] = TemplatesGroups::get()->lists( 'name', 'id' );;

        return view( 'admin.templates.add_edit', $data );


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

        \SEOMeta::setTitle( 'Edit - Template ' );
        $views_path = \Config::get( 'view.paths' );


        $template = CampaignsTemplates::find( $id );


        $template->content = \File::get( $views_path[ 0 ] . '/template/' . $template->path . '/index.blade.php' );

        $data[ 'template' ] = $template;
        $data[ 'groupes' ] = TemplatesGroups::get()->lists( 'name', 'id' );;

        return view( 'admin.templates.add_edit', $data );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function upsert()
    {
        // getting all of the post data
        $file = [
            'css'      => Input::file( 'css' ),
            'preview'  => Input::file( 'preview' ),
            'images'   => Input::file( 'images' ),
            'name'     => Input::get( 'name' ),
            'content'  => Input::get( 'content' ),
         ];
        // setting up rules
        $rules = [

            'name'     => 'required',

            'content'  => 'required'
        ]; //mimes:jpeg,bmp,png and for max size max:10000
        // doing the validation, passing post data, rules and the messages
        $validator = Validator::make( $file, $rules );
        if ( $validator->fails() ) {
            // send back to the page with the input data and errors
            return \Redirect::back()->withInput()->withErrors( $validator );
        } else {


            $views_path = \Config::get( 'view.paths' );


            // checking file is valid.
            $template_dir_path = public_path() . '/templates/';

            $template_name = Input::get( 'name' );


            $template = CampaignsTemplates::findOrCreate( \Input::get( 'id' ) );
            if ( isset( $template->path ) && !empty( $template->path ) ) {
                $template_slug = $template_slug_org = $template->path;
                $template_path = $template_dir_path . '/' . $template_slug;
            } else {
                $template_slug = $template_slug_org = str_slug( Input::get( 'name' ), '_' );
                while ( file_exists( $template_dir_path . '/' . $template_slug ) ) {
                    $template_slug = $template_slug_org . rand();
                }
                $template_path = $template_dir_path . '/' . $template_slug;

            }

            if ( !\File::isWritable( $views_path[ 0 ] . '/template/' ) || !\File::isWritable( public_path() . '/templates' ) ) {
                // there is no right to write in the dirs that we need
                return \Redirect::back()->withInput()->withErrors( 'Can not upload files please check your permission' );
            } else {
                /**
                 * part to make template index file  directory and access
                 */
                if ( !\File::exists( $views_path[ 0 ] . '/template/' . $template_slug ) ) {
                    // if there is no directory fot the template index.blade.php file make it
                    \File::makeDirectory( $views_path[ 0 ] . '/template/' . $template_slug, 0777, true, true );
                } else {
                    // there is a diectory for index.blade.php
                    if ( !\File::isWritable( $views_path[ 0 ] . '/template/' . $template_slug ) ) {
                        // file is not writable so we send the guy back
                        return \Redirect::back()->withInput()->withErrors( 'Can not upload  template index file' );
                    }
                }
                /**
                 * part to make template assets directories and folders
                 */
                if ( !\File::exists( $template_path ) ) {
                    // there is no template directory so we will create one
                    \File::makeDirectory( $template_path, 0777, true, true );
                } elseif ( !\File::isWritable( $template_path ) ) {
                    // well we do not have access so give up
                    return \Redirect::back()->withInput()->withErrors( 'Can not upload template assets' );
                }

                if ( !\File::exists( $template_path . '/css/' ) ) {
                    // there is no template directory so we will create one
                    \File::makeDirectory( $template_path . '/css/', 0777, true, true );
                } elseif ( !\File::isWritable( $template_path . '/css/' ) ) {
                    // well we do not have access so give up
                    return \Redirect::back()->withInput()->withErrors( 'Can not upload template assets' );
                }
                if ( !\File::exists( $template_path . '/images/' ) ) {
                    // there is no template directory so we will create one
                    \File::makeDirectory( $template_path . '/images/', 0777, true, true );
                } elseif ( !\File::isWritable( $template_path . '/images/' ) ) {
                    // well we do not have access so give up
                    return \Redirect::back()->withInput()->withErrors( 'Can not upload template assets' );
                }

            }

            if ( \Request::hasFile( 'css' ) ) {
                if ( Input::file( 'css' )->isValid() ) {
                    \File::cleanDirectory( $template_path . '/css/' );
                    // move css file to his directory
                    $extension = Input::file( 'css' )->getClientOriginalExtension(); // getting image extension
                    $fileName  = str_replace( $extension, '', Input::file( 'css' )->getClientOriginalName() ) . $extension; // renameing image
                    Input::file( 'css' )->move( $template_path . '/css/', $fileName ); // uploading file to given path
                }
            }
            if ( \Request::hasFile( 'preview' ) ) {
                if ( Input::file( 'preview' )->isValid() ) {
                    //halabuda@gmail.com
                    //preview type hardcoded as PNG for now
                    if(Input::file( 'preview' )->getMimeType() != 'image/png'){
                      return \Redirect::back()->withInput()->withErrors( "Preview upload must be a PNG image. Upload detected as: " . Input::file( 'preview' )->getMimeType() );
                    }
                    \File::delete( $template_path . '/preview.png' );
                    // move template thumbnail
                    $extension = Input::file( 'preview' )->getClientOriginalExtension(); // getting image extension
                    $fileName  = 'preview.png'; // renameing image
                    Input::file( 'preview' )->move( $template_path . '/', $fileName ); // uploading file to given path
                }
            }
            if ( \Request::hasFile( 'images' ) ) {
                if ( Input::file( 'images' )->isValid() ) {
                    \File::cleanDirectory( $template_path . '/images/' );
                    // move the ziped file for images , then extract him in images directory
                    $extension = Input::file( 'images' )->getClientOriginalExtension(); // getting image extension
                    $fileName  = 'imgages.' . $extension; // renameing image
                    Input::file( 'images' )->move( $template_path . '/', $fileName ); // uploading file to given path

                    \Zipper::make( $template_path . '/imgages.zip' )->extractTo( $template_path );
                    \File::delete( $template_path . '/imgages.zip' );
                }
            }


            $tpl_index_path = $views_path[ 0 ] . '/template/' . $template_slug . '/index.blade.php';

            $bytes_written = \File::put( $tpl_index_path, \Input::get( 'content' ) );
            if ( $bytes_written === false ) {
                return \Redirect::back()->withInput()->withErrors( 'Could not write to file' );
            }

            $template->name   = \Input::get( 'name' );
            $template->thumb  = 'preview.png';
            $template->active = 1;
            $template->path   = $template_slug;
            $template->group_id   = \Input::get('group_id');
            $template->save();
            return \Redirect::to( 'admin/templates' )->withSuccess( 'Template Saved' );
        }


    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy( $id )
    {

        $template = CampaignsTemplates::find( $id );
        if ( $template ) {
            try {

                $views_path = \Config::get( 'view.paths' );


                \File::deleteDirectory( $views_path[ 0 ] . '/template/' . $template->path );
                \File::deleteDirectory( public_path() . '/templates/' . $template->path );

                $template->delete();

                return redirect()->back()->withSuccess( 'Template deleted' );
            } catch ( Exception $e ) {

            }
        }
        return redirect()->back()->withError( 'Template not deleted' );


    }

    public function chstats( $tid, $sid )
    {


        if ( !empty( $tid ) ) {
            try {
                $tpl = CampaignsTemplates::find( $tid );

                if ( !empty( $tpl ) ) {
                    $tpl->active = $sid;
                    $tpl->save();

                    return redirect()->back()->withSuccess( 'Template Status Changed' );
                }
            } catch ( \Exception $e ) {
            }
        }
        return redirect()->back()->withError( 'Template status not changed' );
    }

}
