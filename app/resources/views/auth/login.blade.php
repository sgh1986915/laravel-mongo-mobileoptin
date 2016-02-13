@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body relativ_pos">

                    <div class="form-holder">
                        <form method="POST" action="{{ url('/auth/login') }}">
                            <fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" placeholder="Enter Email Address" name="email" value="{{ old('email') }}">
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Enter Password" name="password">
                                </div>

                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="remember">
                                            <input type="checkbox" name="remember" id="remember"> Remember Me
                                        </label>
                                    </div>

                                </div>

                                <div class=" text-right">
                                    <button type="submit" class="btn btn-green  ">Login</button>
                                    <br class="clearfix"/>
                                    <a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>
                                </div>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
