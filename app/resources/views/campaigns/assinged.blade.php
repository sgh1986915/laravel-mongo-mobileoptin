@extends('layouts/main')

@section('content')




    <div class="row">
        <div class="col-md-6">
            <h1 class="category_header">Users assigned to {{ $campaign->name }}</h1>
        </div>
        <div class="col-md-6 text-right pt-26">


            {!! Form::open(['method'=>'post','route'=>'save_campaigns_assinged']) !!}
            {!! Form::hidden('id', $campaign->id) !!}

            <div class="row">
                <div class="col-md-6">

                    {!! Form::select('assing_other', $not_assinged, '' ,['id'=>'assing_other','class'=>'form-control' ,'placeholder'=>'select a user'] ) !!}

                </div>
                <div class="col-md-3">

                    <button type="submit" class="btn btn-default">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Assign user to campaign
                    </button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="table-responsive">
                <table class="table   table-curved list_options">


                    <thead>
                    <tr>
                        <th class="col-sm-1">user id</th>
                        <th>Name</th>
                        <th class="col-md-1">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assinged as $user)
                        <tr data-link="row" class="rowlink usrmngrow">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                <a href="{{ route('unassigne_from_campaign',['id'=> $campaign->id,'user_id'=>$user->id])  }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th class='text-right' colspan="7"><?php echo $assinged->render(); ?></th>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

    @include('campaigns.modal.hosted')
    @include('campaigns.modal.embed')

@endsection