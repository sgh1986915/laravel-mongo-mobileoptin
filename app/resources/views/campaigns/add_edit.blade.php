@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Create Campaigns</h1>
        </div>
        <div class="col-md-6 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">

        <div class="row">
            <div class="col-md-5">   
                {!! Form::open(['method'=>'post','route'=>'upsert_campaigns','id'=>'add_edit_campaign_form']) !!}
                {!! Form::hidden('id', $campaign->id,['id'=>'campaign_id']) !!}
                <div class="row">
                    <div class="col-md-4 pr-0">
                        <h4>Choose Template</h4>
                    </div>

                    <div class="col-md-8 text-right">
                         <span id="Active_template" class="btn btn-sm btn-blue ">
                             Activate More Templates
                        </span>
                        <span id="add_template" class="btn btn-green btn-sm">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Template
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <div id="different_templates">
                            @foreach($campaign->template as $ct)

                                <div class="row

                                @if($ct->affect_percentile==0)
                                      grayed_variation
                                @endif

                                        ">

                                    <div class=" overlay_disabled @if($ct->affect_percentile>0)

                                          hidden
                                    @endif">
                                       Disabled
                                    </div>

                                    <div class="col-md-11">
                                        <div class="template-container" id='exist_div' data-utid="{{$ct->id}}" style='position: absolute;width:400px;height:120px;z-index: 99999;'><div class="loader" id='loader_{{$ct->id}}'></div></div>
                                        <div class="form-group @if ($errors->has('template')) has-error @endif" id='exist_cont_{{$ct->id}}'  data-utid="{{$ct->id}}">


                                            <select name="template_selector" class="template_selector" id="selector_for_{{$ct->id}}" data-utid="{{$ct->id}}">
                                              
                                                    <optgroup >
                                                        
                                                            <option data-description=" Variation : ( {{$ct->name}} )  </br>
                                                             Redirect url : {{$ct->redirect_after}}           </br>
                                                             Notify E-mail : {{$ct->notification_email}}      </br>
                                                             E-mail Subject : {{$ct->email_subject}}         </br>
                                                            " data-enabled_field="true"
                                                                    selected="selected"
                                                           
                                                                    value="{{isset($ct->org_template) ? $ct->org_template->id : $ct->id }}" data-imagesrc="{{isset($ct->org_template) ?URL::to('templates/'.$ct->org_template->path):''}}/preview.png">
                                                                {{isset($ct->org_template) ? $ct->org_template->name : ''}}
                                                            </option>
                                                            
                                                    </optgroup>
                                                
                                              

                                            </select>

                                            @if ($errors->has('name'))
                                                <p class="help-block">{{ $errors->first('template') }}</p>
                                            @endif

                                        </div>
                                        <div class="form-group traffic_allocation">
                                            {!! Form::label('percent_container', 'Traffic Allocation') !!}

                                            {!! Form::input('number','percent_container[]',$ct->affect_percentile ,[ "class"=>"percent_container" , "min"=>"0", "max"=>"100"]) !!}
                                            %

                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="hidden" name="user_template[]" value="{{$ct->id}}"/>
                                        <span data-delete_id="{{$ct->id}}" class="glyphicon glyphicon-trash remove_ut"></span>
                                        <span data-utid="{{$ct->id}}" class="glyphicon glyphicon-edit edit_ut"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group  @if ($errors->has('name')) has-error @endif">
                            {!! Form::label('name', 'Campaign Name') !!}
                            {!! Form::text('name',$campaign->name ,['placeholder'=>'Enter campaigns name','id'=>'name','class'=>'form-control']) !!}
                            @if ($errors->has('name'))
                                <p class="help-block">{{ $errors->first('name') }}</p>
                            @endif
                        </div>
                        <div class="form-group  @if ($errors->has('slug')) has-error @endif">
                            {!! Form::label('slug', 'Campaign Url') !!}
                            {!! Form::text('slug',$campaign->slug ,['placeholder'=>'Enter campaign Url','id'=>'slug','class'=>'form-control']) !!}
                            @if ($errors->has('slug'))
                                <p class="help-block">{{ $errors->first('slug') }}</p>
                            @endif
                        </div>
                        <div class="form-group  @if ($errors->has('domain_id')) has-error @endif">
                            {!! Form::label('domain_id', 'Campaign Domain') !!}
                            {!! Form::select('domain_id', $domains ,$campaign->domain_id,['placeholder'=>'Select custom Domain','id'=>'domain_id','class'=>'form-control'])   !!}
                            @if ($errors->has('domain_id'))
                                <p class="help-block">{{ $errors->first('domain_id') }}</p>
                            @endif
                        </div>

                    </div>
                </div>
                
             <div class="row">
                <div class="col-md-5">
                    {!! Form::label('enable_retargeting', 'Enable Retargeting', ['class'=>'retargeting_labels']) !!} <br/>
                    {!! Form::checkbox('enable_retargeting', 1 , (!empty($campaign->enable_retargeting) && $campaign->enable_retargeting == 1 ? $campaign->enable_retargeting : false) ,[ 'id'=>'enable_retargeting','class' => 'checkbox form-control']) !!}
                </div>
             </div>

                <div class="row">
                    <div  id="retargeting_divs" >
                     <div class="col-xs-12">
                            <p > {!! Form::label('analitics_and_retargeting', 'Analytics and Retargeting code' ) !!}                       
                                    <i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title=" In this box you can place retargeting pixels from Facebook and any other advertising network. This pixel will be set on anyone who visits your MobileOptin page. This allows you to retarget your ads to all of your visitors. You can also set a Google Analytics code on your pages so you are able to get detailed information on the visitors you are receiving."></i>
                            </p>  
                         @if($can_analytics_retargeting === '' || $can_analytics_retargeting === ' ')
                        <textarea {!!$can_analytics_retargeting!!} style="height: 400px;"
                                  id="analitics_and_retargeting" name="analitics_and_retargeting" class="form-control">{{$campaign->analitics_and_retargeting}}</textarea>
                        
                         @else
                         <a href='http://mobileoptin.com/pixelpro' target="_blank">
                             <textarea {!!$can_analytics_retargeting!!} style="height: 400px;"
                                  id="analitics_and_retargeting" name="analitics_and_retargeting" class="form-control">{{$campaign->analitics_and_retargeting}}</textarea>
                        <span id="Active_template_button" class="btn btn-sm btn-blue ">
                             Upgrade To Activate Retargeting
                        </span>
                         </a>
                         @endif
                         
                     </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-xs-12">
                           <div class="row">
                                <div class="col-md-5">
                                    {!! Form::label('enable_optimization', 'Enable Optimization', ['class'=>'optimisation_labels']) !!} <br/>
                                    {!! Form::checkbox('enable_optimization', 1 ,$campaign->enable_optimization ,[ 'id'=>'enable_optimization','class' => 'checkbox form-control']) !!}
                                </div>
                             </div>
                        <br/>
                        <div class="row">
                         <div  id="optimization_divs" >
                            <div class="col-md-4">
                                {!! Form::label('ao_clicks', 'Optimize After', ['class'=>'optimisation_labels']) !!}
                                <p class="help-block preinput">Number of clicks
                                    <i data-toggle="tooltip" data-placement="right" title="Allow mobile optin to optimize your campaign after x amount of clicks automatically. Once your campaign has reached x number of clicks it will disable the variations with the lowest conversion rates and keep the highest converting variation as the winner. To enable this just set a click value.  To disable it, leave the field empty." class="glyphicon glyphicon-question-sign"></i>
                                </p>

                                {!! Form::input('number','ao_clicks',$campaign->ao_clicks ,[ 'placeholder'=>'X number of clicks','id'=>'ao_clicks','class'=>'form-control']) !!}


                            </div>
                            <div class="col-md-5">
                                {!! Form::label('ao_threshold', 'Threshold Percentage',['class'=>'optimisation_labels']) !!}
                                <p class="help-block preinput">Disable all templates that do not reach the threshold
                                    <i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title="Set a threshold percentage for your campaign.  If none of the variations in your campaign reach this conversion percentage then the campaign will continue to run past the number of clicks set in the optimize after box.  For example if you set a threshold percentage of 3% and none of your campaigns reach this % your campaign will continue to run until a variation reaches this percentage."></i>
                                </p>

                                {!! Form::input('number','ao_threshold',$campaign->ao_threshold ,[ 'placeholder'=>'Threshold percentage','id'=>'ao_threshold','class'=>'form-control']) !!}

                            </div>
                        </div>
                         
                        </div>
                    </div>
                </div>
             <br/>
                <div class="row">
                    <div class="col-xs-12">
                           <div class="row">
                                <div class="col-md-7">
                                    {!! Form::label('enable_return_redirect', 'Enable Redirect For Return Visitors', ['class'=>'optimisation_labels']) !!} <br/>
                                    {!! Form::checkbox('enable_return_redirect', 1 ,$campaign->enable_return_redirect ,[ 'id'=>'enable_return_redirect','class' => 'checkbox form-control']) !!}
                                </div>
                             </div>
                        <br/>
                        <div class="row">
                         <div  id="redirect_divs" >
                            <div class="col-md-4">
                                {!! Form::label('redirect_return_after', 'Redirect After', ['class'=>'optimisation_labels']) !!}
                                <p class="help-block preinput">Number of visits
                                    <i data-toggle="tooltip" data-placement="right" title="Redirect return user after X numbers of visits." class="glyphicon glyphicon-question-sign"></i>
                                </p>

                                {!! Form::select('redirect_return_after',['1'=>'1','2'=>'2','3'=>'3'],$campaign->redirect_return_after ,[ 'placeholder'=>'Select','id'=>'redirect_return_after','class'=>'form-control']) !!}


                            </div>
                            <div class="col-md-6">
                                {!! Form::label('redirect_return_url', 'Redirect Return User Url',['class'=>'optimisation_labels']) !!}
                                <p class="help-block preinput">Set the url
                                    <i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title="Set the url where user will be redirected"></i>
                                </p>

                                {!! Form::input('string','redirect_return_url',$campaign->redirect_return_url ,[ 'placeholder'=>'Redirect Url','type'=>'url','id'=>'redirect_return_url','class'=>'form-control']) !!}

                            </div>
                        </div>
                         
                        </div>
                    </div>
                </div>
                   <div class="row">
                       <div class="col-md-3">

                                <button type="submit" class="btn  btn-orange" style='margin-top:10px;'>Submit</button>
                            </div>
                   </div>

                <hr/>
                {!! Form::close() !!}
            </div>
            <div class="col-md-7 blugraybg">
                <div id="edit_template_holder_column">
                    <div class="row">
                        <div class="col-md-12" id="Template_name_string">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="editing_template">
                                <form class="form-horizontal" id="save_User_template_form">
                                    <div class="form-group ">
                                        <label for="terms_link" class="col-sm-3 control-label">Variation (Name)</label>

                                        <div class="col-sm-9">
                                            <input type="text" class="form-control " value="" id="template_name">
                                        </div>
                                    </div>
                                    <div class="form-group  ">
                                        <label for="terms_link" class="col-sm-3 control-label">
                                        	Terms of service URL
                                        	<i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title="While this is not a required field, if you do have a terms of service page you can link to it here. It will show up on the bottom of your newly created optin page."></i>
                                        </label>

                                        <div class="col-sm-9">
                                            <input type="url" class="form-control " value="" id="therms_link">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="privacy_link" class="col-sm-3 control-label">
                                        	Privacy policy URL
                                        	<i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title="While this is not a required field, if you do have a terms of service page you can link to it here. It will show up on the bottom of your newly created optin page."></i>
                                        </label>

                                        <div class="col-sm-9">
                                            <input type="url" class="form-control" value="" id="privacy_link">
                                        </div>
                                    </div>

                                    <div class="form-group  ">
                                        <label for="contact_link" class="col-sm-3 control-label">
                                        	Contact Us URL
                                        	<i data-toggle="tooltip" data-placement="right" class="glyphicon glyphicon-question-sign" title="You may enter mailto:youremailaddress or enter a URL for your contact information here. If you do not wish to provide this you can leave this box blank."></i>
                                        </label>

                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="" name="contact_link" id="contact_link">
                                        </div>
                                    </div>

                                    <div class="form-group @if ($errors->has('contact_type')) has-error @endif">
                                        {!! Form::label('contact_type', 'Choose Integration',['class'=>"col-sm-3 control-label"]) !!}
                                           <div class="col-sm-9">
                                               {{--
                                               {!! Form::select('contact_type', $contact_types,'',['id'=>'contact_type','class'=>'form-control'])   !!}
                                               --}}
                                               <select name="contact_type" id="contact_type" class="form-control">
                                                   <option value="0" data-type-id="0">Manual</option>
                                                   @foreach($user_integrations as $type)
                                                      <option value="{{ $type->id }}" data-type-id="{{ $type->type_id }}">{{ $type->name }}</option>
                                                   @endforeach
                                               </select>
                                           </div>
                                        @if ($errors->has('contact_type'))
                                        <p class="help-block">{{ $errors->first('contact_type') }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group  ">
                                        <label for="contact_link" class="col-sm-3 control-label">Notification Email</label>

                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" value="" id="notification_email">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group  ">
                                        <label for="integration_id" class="col-sm-3 control-label">Choose List</label>

                                        <div class="col-sm-9">
                                            {!! Form::select('integration_id', $integrations ,'0',['id'=>'integration_id','class'=>'form-control'])   !!}
                                        	<div id="aweber-note" class='alert alert-warning hide' style="margin-top: 10px;">
                                        		<strong>IMPORTANT!</strong>  Single optin is not turned on by default for Aweber.  
                                        		In order to enable single optin for your lists you MUST contact Aweber support and ask them to enable single optin for each of your lists.
                                        	</div>
                                        </div>
                                    </div>

                                    <div class="form-group  ">
                                        <label for="contact_link" class="col-sm-3 control-label">Redirect after optin</label>

                                        <div class="col-sm-9">
                                            <input type="url" class="form-control" value=""  {{$can_redirect}} id="redirect_after">
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="contact_link" class="col-sm-3 control-label">Return Email subject</label>

                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="" id="email_subject">
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="contact_link" class="col-sm-3 control-label">Return Email body</label>

                                        <div class="col-sm-9">
                                            <textarea id="template_return_email_content" rows="6" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" value="" id="current_edited_template_id">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="mobile_templateHolder">
                                                <textarea name="custom_template" id="template_content"></textarea>
                                                <span id="save_tmp_changes" class="btn  btn-orange">Save Changes</span>

                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="template_editing_manual">
                    <img src="{{url('img/green_arrow.png')}}" width="60" height="73">
                    </br>


                    Click on "Add Template" to customize it here.<br/> You can add as many templates as you like for each campaign.
                    </br>
                    </br>
                    <span class="gray_manual_text">

                        You can delete '<span class="glyphicon glyphicon-trash "></span>' or edit '<span class="glyphicon glyphicon-edit"></span>' your</br> template anytime you want
                    </span>
                </div>
            </div>
        </div>
    </div>


@include('campaigns.modal.template_modal_by_cat')

<script type="text/javascript" defer="defer">

        var table_row_template = '<div class="row">' +
                '<div class="col-md-11">' +

                '<div class="template-container" data-utid="${template_id}" style="position: absolute; width: 400px; height: 120px; z-index: 99999;">' +
        			'<div class="loader" id="loader_${template_id}"></div>' +
        		'</div>' +
        	
                '<div class="form-group ">' +
		            '<div class="form-group " id="exist_cont_${template_id}" data-utid="${template_id}">' +
						'<div style="width: 100%;" class="dd-container" id="selector_for_${template_id}-dd-placeholder">' +
							'<div style="width: 100%; background: rgb(238, 238, 238) none repeat scroll 0% 0%;" class="dd-select">' +
								'<input value="${template_id}" name="template_selector" id="selector_for_${template_id}" class="dd-selected-value" type="hidden">' + 
								'<a class="dd-selected"></a>'+
								'<span class="dd-pointer dd-pointer-down"></span>'+
							'</div>' +
						'</div>' +
		   		   '</div>' +
                '</div>' +
                
                '<div class="form-group traffic_allocation">' +
                '{!! Form::label("percent_container", "Traffic Allocation") !!}' +
                ' {!! Form::input("number","percent_container[]","" ,[ "class"=>"percent_container"   , "min"=>"0", "max"=>"100"]) !!}' +
                ' %' +
                '</div>' +
                '</div>' +
                '<div class="col-md-1">' +
                '<input type="hidden" name="user_template[]" value="${template_id}"/>' +
                '<span data-delete_id="${template_id}" class="glyphicon glyphicon-trash remove_ut"></span>' +
                '<span data-utid="${template_id}" class="glyphicon glyphicon-edit edit_ut"></span>' +
                '</div>' +
                '</div>';


    </script>
@endsection

@section('javascript')
    <script type="text/javascript" defer="defer">

        $(function () {
            @foreach($campaign->template as $ct)


            //the value for which we are searching
            var searchBy = '{{$ct->original_template_id}}';

            //#aSelectBox is the id of ddSlick selectbox
            $('#selector_for_{{$ct->id}}-dd-placeholder li').each(function (index) {

                //traverse all the options and get the value of current item
                var curValue = $(this).find('.dd-option-value').val();

                //check if the value is matching with the searching value
                if (curValue == searchBy) {
                    //if found then use the current index number to make selected
                    $('#selector_for_{{$ct->id}}-dd-placeholder').ddslick('select', {index: $(this).index()});
                }
            });

            @endforeach

         $('#edit_template_holder_column').hide();
            $("#template_editing_manual").show();


        });

        var submit_clicked = false;
        $('button[type="Submit"]').click(function () {
            submit_clicked = true;
        });


        $('body').on('change keyup keydown', 'input, textarea, select', function (e) {
            $(this).addClass('changed-input');
        });
        

        if($('#enable_optimization').is(':checked') == false){
            $('#optimization_divs').hide();
        }
        
        $("#enable_optimization").bootstrapSwitch();
        
        $('#enable_optimization').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state == true){
            $('#optimization_divs').show('normal');
               }else{
            $('#optimization_divs').slideUp('normal');
           }
       });

        if($('#enable_retargeting').is(':checked') == false){
            $('#retargeting_divs').hide();
        }
        
        $("#enable_retargeting").bootstrapSwitch();
        
        $('#enable_retargeting').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state == true){
            $('#retargeting_divs').show('normal');
               }else{
            $('#retargeting_divs').slideUp('normal');
           }
       });
       
        if($('#enable_return_redirect').is(':checked') == false){
            $('#redirect_divs').hide();
        }
        
        $("#enable_return_redirect").bootstrapSwitch();
        
        $('#enable_return_redirect').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state == true){
            $('#redirect_divs').show('normal');
               }else{
            $('#redirect_divs').slideUp('normal');
           }
        });
       
       
        if($('#contact_type').val() == 0){
            $('#integration_id').parent().parent().hide();
        }
        if($('#contact_type').val() > 0){
            $('#notification_email').parent().parent().show();
        }
        if($('#contact_type option:selected').data('type-id') == 4) {
            // integration with Zapier
            $('#integration_id').html('');
            $('#integration_id').parent().parent().hide();
            $('#notification_email').parent().parent().hide();
        }

        if($('#contact_type option:selected').data('type-id') == 2) {
        	if($('#aweber-note').hasClass('hide')){
				$('#aweber-note').removeClass('hide');
			}
        }else{
        	$('#aweber-note').addClass('hide');
        }

        $('#contact_type').on('change', function() {

         if($('#contact_type option:selected').data('type-id') != 2) {
        	 $('#aweber-note').addClass('hide');
         }
         
         if($('#contact_type').val() == 0) {
             $('#integration_id').html('');
             $('#integration_id').parent().parent().hide();
             $('#notification_email').parent().parent().show();
         } else if($('#contact_type option:selected').data('type-id') == 4) {
             // integration with Zapier
             $('#integration_id').html('');
             $('#integration_id').parent().parent().hide();
             $('#notification_email').parent().parent().hide();
         }else{
                $('#notification_email').parent().parent().hide();
                $('#integration_id').html('');

				var type_id = $('#contact_type option:selected').data('type-id');
                $.ajax({
                    url: base_url + '/campaigns/camplist/' + $('#contact_type').val(),
                    type: 'GET',
                    dataType: "json",
                    cache: false,
                    success: function (content) {
                        $.each(content, function(id, name){
                              $('#integration_id').append('<option value="'+id+'">'+name+'</option>');
                        });

                        var integration_id = $('#integration_id').data('integration-id');
                        if (integration_id != 0) $('#integration_id option[value="' + integration_id + '"]').prop('selected',true);
                        $('#integration_id').parent().parent().show(function(){
							if(type_id == 2){
								if($('#aweber-note').hasClass('hide')){
									$('#aweber-note').removeClass('hide');
								}
							}
                        });
                    }
                });
         }
       });

    
     
        $(window).on('beforeunload', function () {
            if ($('.changed-input').length && submit_clicked === false) {
                return 'You haven\'t saved your changes.';
            }
        });
        
        
        //
        //        $(window).on('beforeunload', function () {
        //
        //
        //            BootstrapDialog.show({
        //                type: BootstrapDialog.TYPE_WARNING,
        //                title: 'You did not saved all changes',
        //                message: 'Please save all changes or the page will unload',
        //                buttons: [{
        //                    label: 'Ok',
        //                    action: function (dialogItself) {
        //
        //
        //                    }
        //                }]
        //            });
        //
        //        });	
        
    </script>
@endsection
