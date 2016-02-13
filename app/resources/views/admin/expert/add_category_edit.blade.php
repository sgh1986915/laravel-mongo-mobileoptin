@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-8 ">
            <h1 class="category_header">Add A TEA's Category</h1>
        </div>
        <div class="col-md-4 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">
        <div class="row">
            <div class="col-md-5">
             @if(isset($faq) && $faq->id != 0)
            {!! Form::model($faq, ['route' => ['admin.expert_traffic.upsert_category', $faq->id], 'class' => 'form-horizontal']) !!}
             @else
            {!! Form::open(array('route'=>'admin.expert_traffic.upsert_category', 'class' => 'form-horizontal')) !!}
            @endif
            {!! Form::hidden('id', $faq->id) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div id="editing_template">
                                <form class="form-horizontal" id="save_User_template_form">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-3 control-label">Name</label>
                                        <div class="col-sm-9">
                                               {!! Form::text('name', Input::old('name'), array('class' => 'form-control',  'maxlength' => '200')) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-md-offset-3">
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

