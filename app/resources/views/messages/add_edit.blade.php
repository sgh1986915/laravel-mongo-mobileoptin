@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Add Message</h1>
        </div>
        <div class="col-md-6 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">

        <div class="row">
            <div class="col-md-5">
                     @if(isset($message) && $message->id != 0)
            {!! Form::model($message, ['route' => ['upsert_message', $message->id], 'class' => 'form-horizontal']) !!}
             @else
            {!! Form::open(array('route'=>'upsert_message', 'class' => 'form-horizontal')) !!}
            @endif
                {!! Form::hidden('id', $message->id,['id'=>'domain_id']) !!}
           
                    <div class="row">
                        <div class="col-md-12">
                            <div id="editing_template">
                                <form class="form-horizontal" id="save_User_template_form">
                                    <div class="row">
                                        <label for="topic" class="col-sm-3 control-label">Topic</label>

                                        <div class="col-sm-9">
                                               {!! Form::text('topic', Input::old('topic'), array('class' => 'form-control',  'maxlength' => '200')) !!}
                                       
                                     
                                    </div>
                                    </div>  
                                    <br/>
                                    <div class="row">
                                        <label for="topic" class="col-sm-3 control-label">Content</label>

                                              <div class="col-sm-9">
                                               {!! Form::textArea('content', Input::old('content'), array('class' => 'form-control')) !!}
                                       
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <button type="submit" id="save_tmp_changes3" class="btn  btn-orange">Send</button>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
             
            </div>
        </div>


    </div>


  
@endsection

