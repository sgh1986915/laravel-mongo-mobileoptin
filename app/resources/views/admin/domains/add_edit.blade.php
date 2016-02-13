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
            {!! Form::model($domain, ['route' => ['admin.upsert_domain', $domain->id], 'class' => 'form-horizontal']) !!}
             @else
            {!! Form::open(array('route'=>'admin.upsert_domain', 'class' => 'form-horizontal')) !!}
            @endif
              
                {!! Form::hidden('id', $domain->id,['id'=>'campaign_id']) !!}
           
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
                                           <div class="form-group  row">    
                                                 <label for="status" class="col-sm-3 control-label">Status</label>
                                                    <div class="col-sm-9">
                                                      
<div class="btn-group" data-toggle="buttons">
  <label style='color:white !important;' class="btn btn-primary <?php echo (isset($domain->status) && $domain->status == 1) ? 'active' : '' ?>">
    <input type="radio" name="status" id="option1" value='1' autocomplete="off" <?php echo (isset($domain->status) && $domain->status == 1) ? 'checked' : '' ?> > Uncofirmed
  </label>
  <label style='color:white !important;' class="btn btn-primary <?php echo (isset($domain->status) && $domain->status == 2) ? 'active' : '' ?>">
    <input type="radio" name="status" id="option2" value='2' autocomplete="off"<?php echo (isset($domain->status) && $domain->status == 2) ? 'checked' : '' ?>> Confirmed
  </label>
  <label style='color:white !important;' class="btn btn-primary <?php echo (isset($domain->status) && $domain->status == 3) ? 'active' : '' ?>">
    <input type="radio" name="status" id="option3" value='3' autocomplete="off" <?php echo (isset($domain->status) && $domain->status == 3) ? 'checked' : '' ?>> Declined
  </label>
</div>
                                                    </div>
                                           </div>
                                    
                             
                                    
                                         <div class="form-group  row">
                                        <label for="terms_link" class="col-sm-3 control-label">Active</label>

                                        <div class="col-sm-1">
                                             {!! Form::checkbox('active', Input::old('active'), array(),array('class' => 'checkbox form-control','style'=>'position:relative;margin-left:0px;')) !!}
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

