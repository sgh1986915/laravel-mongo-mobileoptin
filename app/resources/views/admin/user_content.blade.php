@extends('layouts/main')
@section('content')
    <div class="row"> 
	    <div class="col-md-6 "> 
	   	 	<h1 class="category_header">Manage Announcement Content</h1> 
	    </div> 
    	<div class="col-md-2 text-right pt-3"> </div> 
    	<div class="col-md-4 text-right pt-26">  
    		<a href="{{ URL::to('/admin/reset_announcement_settings') }}" class="btn btn-green">
   				Reset display announcement setting
   			</a>  
   		 </div> 
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(array('route'=>'admin.save.user_content','method'=>'POST', 'class'=>'form','role'=>'form')) !!}
            <input type="hidden" name="id" value="{{ $user_content->id }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <input class="form-control col-md-12" name="title" value="{{ $user_content->title }}"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Content</label>
                        <textarea style="height: 257px;" class="form-control col-md-12" name="content">{{ $user_content->content }}</textarea>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary pull-right"> Save </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection