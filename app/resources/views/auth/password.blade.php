@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Forgotten password</div>
                <div class="panel-body relativ_pos">

                    <div class="form-holder" style="margin-top: 3rem">
                        <form method="POST" action="{{ url('/password/email') }}">
                            <fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" placeholder="Enter Email Address" name="email" value="{{ old('email') }}">
                                </div>


                                <div class=" text-right">
                                    <button type="submit" class="btn btn-green  ">Send Password Reset Link</button>
                                </div>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

