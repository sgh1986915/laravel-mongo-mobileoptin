@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Add Domain</h1>
        </div>
        <div class="col-md-6 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">

        <div class="row">
            <div class="col-md-5">
                     @if(isset($domain) && $domain->id != 0)
            {!! Form::model($domain, ['route' => ['upsert_domain', $domain->id], 'class' => 'form-horizontal']) !!}
             @else
            {!! Form::open(array('route'=>'upsert_domain', 'class' => 'form-horizontal')) !!}
            @endif
                {!! Form::hidden('id', $domain->id,['id'=>'domain_id']) !!}
           
                    <div class="row">
                        <div class="col-md-12">
                            <div id="editing_template">
                                <form class="form-horizontal" id="save_User_template_form">
                                    <div class="form-group row">
                                        <label for="terms_link" class="col-sm-3 control-label">Name</label>

                                        <div class="col-sm-9">
                                               {!! Form::text('name', Input::old('name'), array('class' => 'form-control',  'maxlength' => '200')) !!}
                                       
                                        </div>
                                    </div>
                                              
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <button type="submit" id="save_tmp_changes2" class="btn  btn-orange">Save</button>

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

