@extends('layouts/main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h5>Manage Template Groups</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            {!! Form::open(array('route'=>'admin.templates.groups.upsert','method'=>'POST', 'files'=>true,'class'=>'form','role'=>'form')) !!}
            <input type="hidden" name="id" value="{{ $group->id }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Name</label>


                        <input type="text" class="form-control" name="name" value="{{ $group->name }}">

                    </div>
                </div>

            </div>

            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>

            </form>

        </div>
    </div>
@endsection
