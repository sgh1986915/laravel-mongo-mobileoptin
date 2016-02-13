@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h5>Manage template</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            {!! Form::open(array('url'=>'/admin/templates/upsert','method'=>'POST', 'files'=>true,'class'=>'form','role'=>'form')) !!}
            <input type="hidden" name="id" value="{{ $template->id }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Name</label>


                        <input type="text" class="form-control" name="name" value="{{ $template->name }}">


                    </div>
                    <div class="form-group">
                        <label class="control-label">Group</label>


                        {!! Form::select('group_id', $groupes, ($template->group_id ? $template->group_id : 1) ,['id'=>'group_id','class'=>'form-control'] ) !!}

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Css</label>
                        {!! Form::file('css') !!}
                    </div>
                    <div class="form-group">
                        <label class="control-label">Preview image ( 50px X 100px ) png format</label>
                        {!! Form::file('preview') !!}
                    </div>
                    <div class="form-group">
                        <label class="control-label">Images </label>
                        {!! Form::file('images') !!}
                        <p class="help-block">All images that the template needs in a ZIP file</p>

                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label">Contet</label>
                        <textarea rows="30" class="form-control" name="content">{{ $template->content }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>
                    <h4>Info</h4>

                    Every template must start and contain
                    <kbd><?= "@extends('layouts/mobileoptin')" ?></kbd>
                    </p>
                    <hr/>
                    <h4><span style="color:#ff0000!important">LINK Place holders !!!</span></h4>

                    <p>
                        to write a link for mailto use <br/>
                        <kbd><?= e( '<span class="link">link text</span>' )  ?></kbd>
                        <br/>
                        <br/>
                        to write link for "therms of services " in href param use this
                        <kbd><?= e( '{{$therms_of_link}}' )  ?></kbd> <br/> <br/>
                        to write link for "Privacy Policy" in href param use this
                        <kbd><?= e( '{{$privacy_link}}' )  ?></kbd> <br/><br/>
                        to write link for "Contact us" in href param use this
                        <kbd><?= e( '{{$contact_us_link}}' )  ?></kbd> <br/>
                    </p>
                    <hr/>
                    <p>
                    <h4>Header</h4>
                    header section of the template start with
                    <kbd><?= "@section('header')" ?></kbd><br/>
                    and ends with <kbd><?= "@endsection" ?></kbd><br/>
                    header section also must contain<br/>
                    <kbd><?= e( '<meta content="{{$template_id}}" name="template_id"/>' ) . '<br/>'
                        . e( ' <meta content="{{$campaign_id}}" name="campaign_id"/>' ) ?></kbd> <br/>
                    <br/>
                    add a path for you template css file <br/>


                    <kbd><?= e( ' <link rel="stylesheet" type="text/css" href="{{URL::to(\'/\')}}/templates/{{$template_path}}/css/style.css">' )  ?></kbd>
                    </p>
                    <hr/>
                    <h4>Body</h4>

                    <p>
                        body section starts with
                        <kbd><?= e( "@section('content')" )  ?></kbd> <br/>
                        and ends with
                        <kbd><?= e( "@endsection" )  ?></kbd> <br/>
                    </p>


                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
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
