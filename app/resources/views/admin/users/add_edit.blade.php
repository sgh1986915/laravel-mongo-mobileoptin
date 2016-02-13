@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Manage user</div>
                <div class="panel-body">


                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/users/upsert') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        @if(Auth::user()->can('change_role'))
                            <div class="form-group">
                                <label class="col-md-4 control-label">Role</label>

                                <div class="col-md-6">
                                    {!! Form::select('role_id', $roles, ($user->role_id ? $user->role_id : 2) ,['id'=>'role_id','class'=>'form-control'] ) !!}
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="role_id" value="2">
                        @endif

                        <div class="form-group">
                            <label class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ $user->email }}">
                            </div>
                        </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" value="" name="password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" value="" name="password_confirmation">
                                </div>
                            </div>
                        
                         @if(!$hide_campaign_data || Auth::user()->hasRole('admin'))
                         <div class="form-group">
                            <label class="col-md-4 control-label" style="color: #ac2925">Select package</label>
                            <div class="col-md-6">
                                {!! Form::select('package_id', $packages, (isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package_id : 0) ,['id'=>'package_id','class'=>'form-control'] ) !!}
                            </div>
                        </div>
                        @endif
                        
                        <div id="user_hide">

                         @if(!$hide_campaign_data || Auth::user()->hasRole('admin'))
                        <div class="row">
                            <div class="col-md-6 col-md-offset-4">
                                <br/>
                                <strong>Templates Groups</strong>
                            </div>
                        </div>

                        @if(Auth::user()->hasRole( 'advertiser' ))
                            <div class="row">
                                <div class="col-md-6 col-md-offset-4">
                                     @foreach($this_user_allowed_groups as $grow)
                                          @foreach($grow->tplgroups()->get() as $groupdata)

                                        <label>
                                            <input type="checkbox" value="{{$groupdata->id}}"

                                            @if(isset($user_allowed_groups[$groupdata->id]))
                                                   checked="checked"
                                                   @endif
                                                   name="allowed_groups[]"   >
                                            {{$groupdata->name}}
                                        </label>
                                        <br/>
                                        @endforeach
                                    @endforeach

                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-6 col-md-offset-4">

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
                        @endif
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-md-offset-4">
                                <br/>
                                <strong>Modules</strong>
                            </div>
                        </div>

                            <div class="row">
                                <div class="col-md-6 col-md-offset-4">
                                     @foreach($modules as $module)
                                      <?php  $flag = false; ?>
                                        @if(isset($modules_user))
                                          @foreach($modules_user as $puser)
                                            @if($module->id == $puser->module_id && $puser->status == 1)
                                              <?php  $flag = true; ?>
                                            @endif
                                          @endforeach
                                          @endif

                                        <label style="font-weight: normal;">
                                            <input type="checkbox" value="{{$module->id}}"

                                            @if($flag == true)
                                                   checked="checked"
                                                   @endif
                                                   name="module[]"   >
                                            {{$module->name}}
                                        </label>
                                        <br/>
                                     
                                    @endforeach

                                </div>
                            </div>
                        @else
                        	</div>
                        @endif


                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script type="text/javascript" defer="defer">
    $(function () {
        if($('#package_id').val() != 0){
            $('#user_hide').hide();
        }
                
        $('#package_id').on('change', function(event, state) {
            if($('#package_id').val() == 0){
            $('#user_hide').show('normal');
               }else{
            $('#user_hide').hide('normal');
           }
       });
   });
       
</script>
@endsection
