@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Profile</div>
                <div class="panel-body">


                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/profile') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">



                        <div class="form-group">
                            <label class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" disabled="disabled" value="{{ $user->name }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" disabled="disabled" value="{{ $user->email }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" placeholder="password" name="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" placeholder="password confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        @if(!$hide_campaign_data)
                        <div class="form-group">
                                <label class="col-md-4 control-label" style="color: #ac2925">Package</label>

                                <div class="col-md-6">
                                    {!! Form::select('package_id', $packages, (isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package_id : 0) ,['id'=>'package_id','class'=>'form-control','disabled'=>'disabled'] ) !!}
                                </div>
                            </div>
           				
                        <div class="form-group">
                            <label class="col-md-4 control-label">Number of campaigns</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package->max_campaigns : $user->profile->max_campaigns }}" disabled="disabled" name="max_campaigns">
                            </div>
                        </div>
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="split_testing" disabled="disabled" name="split_testing" <?php echo isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package->split_testing ? 'checked="checked"' : '' : $user->profile->split_testing ? 'checked="checked"' : '' ?> >
                                    Can do split tests
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="redirect_page" disabled="disabled" name="redirect_page" <?php echo isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package->redirect_page ? 'checked="checked"' : '' : $user->profile->redirect_page ? 'checked="checked"' : '' ?>  >
                                    Can Redirect afterwards
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="embed" name="embed" disabled="disabled" <?php echo isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package->embed ? 'checked="checked"' : '' : $user->profile->embed ? 'checked="checked"' : '' ?>  >
                                    Can have embed
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="hosted" name="hosted" disabled="disabled" <?php echo isset($user->profile) && isset($user->profile->package_id) ? $user->profile->package->hosted ? 'checked="checked"' : '' : $user->profile->hosted ? 'checked="checked"' : '' ?>  >
                                    Can have hosted
                                </label>
                            </div>
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
