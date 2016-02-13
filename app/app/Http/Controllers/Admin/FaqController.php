<?php

namespace MobileOptin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use MobileOptin\Http\Controllers\Controller;
use MyProject\Proxies\__CG__\stdClass;
use PhpSpec\Exception\Exception;
use MobileOptin\Models\Package;
use MobileOptin\Models\FaqCategory;
use MobileOptin\Models\FaqAnswers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Psy\Exception\ErrorException;

class FaqController extends Controller {


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct() {
        parent::__construct();
    }


    public function index() {
        $data = ['admin_navigation' => true];
        $data['faq_categories'] = FaqCategory::paginate( 10 );
        return \Response::view('admin.faq.categories', $data);
    }
    
    public function add_category() {
        if ( Auth::user()->hasRole( 'admin' ) ) {
            $data = [ 'admin_navigation' => true ];
        } else {
            $data = [ ];
        }
        
        \SEOMeta::setTitle( 'Add - FAQ\'s Category ' );
        \DB::enableQueryLog();


        $data[ 'add' ]        = true;
        $data[ 'faq' ]        = new \stdClass();
        $data[ 'faq' ]->id    = 0;
        $data[ 'faq' ]->name  = \Input::old( 'name' );
        
        return view( 'admin.faq.add_category_edit', $data );
    }
    
    public function add_edit_category(){
    	$validator = \Validator::make( \Input::only( 'id', 'name'), [
    		'id'   => 'required|integer',
    		'name' => 'required'
    	]);
    	
    	if ( $validator->fails() ) {
    		return redirect()->back()->withInput()->withErrors( $validator );
    	} else {
    		$id = (int) Input::get( 'id' );
    		if ( $id > 0 ) {//EDIT
    			$new_faq_category = FaqCategory::where( 'id', '=', \Input::get( 'id' ) )->first();
    			if ( !$new_faq_category ) {
    				return redirect()->back()->withInput()->withError( 'FAQ\'s category not found' );
    			}
    			$new_faq_category->name    = \Input::get( 'name' );
    			$new_faq_category->save();
    		} else {//ADD
    			$package = FaqCategory::create( [ 'name' => \Input::get( 'name' ) ] );
    		}
    		
    		return redirect()->route( 'admin.faq.categories' )->withNotify( 'FAQ\'s category saved' );
    	}
    }
    
    public function edit_category($id){
    	\SEOMeta::setTitle( 'Edit - FAQ\'s category' );
    	try {
    		$data['faq']              = FaqCategory::where( 'id', '=', $id )->firstOrFail();
    		$data[ 'add' ]            = true;
    	} catch ( \Exception $e ) {
    		return redirect()->route( 'admin.faq.categories' )->withError( $e . 'FAQ\'s category not found or you do not have permissions' );
    	}
    	
    	return view( 'admin.faq.add_category_edit', $data + ['admin_navigation' => Auth::user()->hasRole( 'admin' ) ? true : false] );
    }
    
    public function delete_category($id){
    	try {
    		if ( Auth::user()->hasRole( 'admin' ) ) {
    			$faqCategory = FaqCategory::where( 'id', '=', $id )->firstOrFail();
    			FaqAnswers::where( 'faq_category_id', '=', $id )->delete();
    			$faqCategory->forceDelete();
    			return redirect()->route( 'admin.faq.categories' )->withSuccess( 'FAQ\'s category Deleted' );
    		}
    	} catch ( \Exception $e ) { }
    	return redirect()->route( 'admin.faq.categories' )->withError( 'FAQ\'s category not removed ' );
    }
    
    public function category_index($id) {
    	if ( $id > 0 ) {
    		$faq_category = FaqCategory::where( 'id', '=', $id )->first();
    		if ( $faq_category ) {
    			$data                       = ['admin_navigation' => true];
    			$data['faq_category_name']  = $faq_category->name;
    			$data['faq_category_id']    = $id;
    			$data['faq_answers']        = FaqAnswers::where('faq_category_id', $id, false)->paginate( 10 );
    			return \Response::view('admin.faq.category_index', $data);
    		}
    	}
	    return redirect()->route( 'admin.faq.categories' )->withError( 'FAQ\'s category do not exist ' );
    }
    
    public function add($category_id) {
    	if ( $category_id > 0 ) {
    		$faq_category = FaqCategory::where( 'id', '=', $category_id )->first();
    		if ( $faq_category ) {
    			
    			if ( Auth::user()->hasRole( 'admin' ) ) {
    				$data = [ 'admin_navigation' => true ];
    			} else {
    				$data = [ ];
    			}
    			
    			\SEOMeta::setTitle( 'Add - FAQ\'s Answer' );
    			\DB::enableQueryLog();
    			
    			$data[ 'add' ]                          = true;
    			$data['faq_category_name']              = $faq_category->name;
    			$data[ 'faq_answer' ]                   = new \stdClass();
    			$data[ 'faq_answer' ]->id               = 0;
    			$data[ 'faq_answer' ]->question         = \Input::old( 'question' );
    			$data[ 'faq_answer' ]->answer           = \Input::old( 'answer' );
    			$data[ 'faq_answer' ]->faq_category_id  = $category_id;
    			
    			return view( 'admin.faq.add_edit', $data );
    		}
    	}
    }
    
    public function edit($category_id, $id){
    	\SEOMeta::setTitle( 'Edit - FAQ\'s answer' );
    	try {
    		$data['faq_answer']         = FaqAnswers::where( 'id', '=', $id )->firstOrFail();
    		$faq_category               = FaqCategory::where( 'id', '=', $category_id )->first();
    		$data['faq_category_name']  = $faq_category->name;
    		$data[ 'add' ]              = true;
    	} catch ( \Exception $e ) {
    		return redirect()->route( 'admin.faq.category.answers', $category_id )->withError( $e . 'FAQ\'s answer not found or you do not have permissions' );
    	}
    	 
    	return view( 'admin.faq.add_edit', $data + ['admin_navigation' => Auth::user()->hasRole( 'admin' ) ? true : false] );
    }
    
    public function add_edit(){
    	$validator = \Validator::make( \Input::only( 'id', 'question', 'answer', 'faq_category_id', 'pdf_file'), [
    		'id'              => 'required|integer',
    		'question'        => 'required',
    		'answer'          => 'required',
    		'faq_category_id' => 'required|integer',
    		'pdf_file'        => 'mimes:pdf'
    	]);
    	 
    	if ( $validator->fails() ) {
    		return redirect()->back()->withInput()->withErrors( $validator );
    	} else {
    		
    		$id = (int) Input::get( 'id' );
    		
    		if ( $id > 0 ) {//EDIT
    			$answer = FaqAnswers::where( 'id', '=', $id )->first();
    			if ( !$answer ) {
    				return redirect()->back()->withInput()->withError( 'FAQ\'s answer not found' );
    			}
    			$answer->question           = \Input::get( 'question' );
    			$answer->answer             = \Input::get( 'answer' );
    			$answer->faq_category_id    = \Input::get( 'faq_category_id' );
    			$answer->save();
    		} else {//ADD
    			$answer = FaqAnswers::create([ 
    						'question'        => \Input::get( 'question' ),
    						'answer'          => \Input::get( 'answer' ),
    						'faq_category_id' => \Input::get( 'faq_category_id' )
						  ]);
    		}
    		
    		$pdf = Input::file('pdf_file');
    		if(!empty($pdf)){
    			$pdfFileName       = Str::lower(Str::words(str_slug(\Input::get( 'question' )), 200, '')) . '-' . $answer->id . '.' . Input::file('pdf_file')->getClientOriginalExtension();
    			Input::file('pdf_file')->move( base_path() . '/public/faq/', $pdfFileName );
    			$answer->pdf_file  = $pdfFileName;
    			$answer->save();
    		}
    		
    		return redirect()->route( 'admin.faq.category.answers', $answer->faq_category_id )->withNotify( 'FAQ\'s answer saved' );
    	}
    }
    
    public function delete($id){
    	try {
    		if ( Auth::user()->hasRole( 'admin' ) ) {
    			$faqAnswer    = FaqAnswers::where( 'id', '=', $id )->firstOrFail();
    			$category_id  = $faqAnswer->faq_category_id;
    			$faqAnswer->forceDelete();
    			return redirect()->route( 'admin.faq.category.answers', $category_id )->withSuccess( 'FAQ\'s category Deleted' );
    		}
    	} catch ( \Exception $e ) { }
    	 
    	return redirect()->route( 'admin.faq.categories' )->withError( 'FAQ\'s category not removed ' );
    }
    
    public function delete_pdf($id){
    	try {
    		if ( Auth::user()->hasRole( 'admin' ) ) {
    			$faqAnswer           = FaqAnswers::where( 'id', '=', $id )->firstOrFail();
    			try{
    				if (unlink(public_path('faq/' . $faqAnswer->pdf_file))){
    					$faqAnswer->pdf_file = null;
    					$faqAnswer->save();
    					return redirect()->route( 'admin.faq.category.answers', $faqAnswer->faq_category_id )->withSuccess( 'PDF file Deleted' );
    				}
    			}catch (\ErrorException $e){}
    		}
    	} catch ( \Exception $e ) { }
    	return redirect()->route( 'admin.faq.categories' )->withError( 'PDF file not deleted ' );
    }
    
}
