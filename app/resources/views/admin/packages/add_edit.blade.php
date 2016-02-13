@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Add Package</h1>
        </div>
        <div class="col-md-6 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">

        <div class="row">
            <div class="col-md-5">
             @if(isset($package) && $package->id != 0)
            {!! Form::model($package, ['route' => ['admin.upsert_package', $package->id], 'class' => 'form-horizontal']) !!}
             @else
            {!! Form::open(array('route'=>'admin.upsert_package', 'class' => 'form-horizontal')) !!}
            @endif
              
                {!! Form::hidden('id', $package->id) !!}
           
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
                                     <div class="form-group row">
                                        <label for="name" class="col-sm-3 control-label">Jvzoo ID</label>

                                        <div class="col-sm-9">
                                               {!! Form::text('jvzoo_id', Input::old('jvzoo_id'), array('class' => 'form-control',  'maxlength' => '255')) !!}
                                       
                                        </div>
                                    </div>

                                    
                            <div class="form-group">
                                <label class="col-md-3 control-label">Number of campaigns</label>

                                <div class="col-md-9">
                                     {!! Form::text('max_campaigns', Input::old('max_campaigns'), array('class' => 'form-control',  'maxlength' => '200')) !!}
                       
                                </div>
                            </div>
                            <div class="col-md-7 col-md-offset-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="split_testing" name="split_testing" @if($package->split_testing)checked="checked" @endif >
                                        Can do split tests
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="redirect_page" name="redirect_page" @if($package->redirect_page)checked="checked" @endif >
                                        Can Redirect afterwards
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="embed" name="embed" @if($package->embed)checked="checked" @endif >
                                        Can have embed
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="hosted" name="hosted" @if($package->hosted)checked="checked" @endif >
                                        Can have hosted
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="analytics_retargeting" name="analytics_retargeting" @if($package->analytics_retargeting)checked="checked" @endif >
                                        Can have analytics and retargeting
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="traffic_experts_academy" name="traffic_experts_academy" @if($package->traffic_experts_academy) checked="checked" @endif >
                                        Can access traffic experts academy
                                    </label>
                                </div>
                            </div>
                   

                        <div class="row">
                            <div class="col-md-7 col-md-offset-3">
                                <br/>
                                <strong>Templates Groups</strong>
                            </div>
                        </div>


                            <div class="row">
                                <div class="col-md-7 col-md-offset-3">

                                    @foreach($allowed_groups as $g_id=>$group_name)
                                        <label>
                                            
                                            <input type="checkbox" value="{{$g_id}}"
                                                   
                                            @if(isset($user_allowed_groups[$g_id]))
                                                   checked="checked"
                                                   @endif
                                                   name="allowed_groups[]"   >
                                            {{$group_name}}
                                        </label>
                                        <br/>
                                    @endforeach

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

