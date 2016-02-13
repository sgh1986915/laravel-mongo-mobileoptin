@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Templates</h1>
        </div>
        <div class="col-md-6 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('add_template')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Termplate</a>
            @endif

        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="table-responsive">
                <table class="table   table-curved list_options">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Name</th>
                        <th>path</th>
                        <th class="col-sm-2">Used</th>
                        <th class="col-sm-2">Status</th>
                        <th class="col-sm-2">Registerd on</th>

                        <th class="col-sm-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($templates as $template)
                        <tr data-link="row" class="rowlink usrmngrow">
                            <td>{{ $template->id }}</td>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->path }}</td>
                            <td>{{ $template->user_templates->count() }} Times</td>
                            <td>
                                @if($template->active)
                                    Active
                                @else
                                    Unpublish
                                @endif
                            </td>
                            <td>
                                {{ date('m-d-Y',strtotime($template->created_at)) }}
                            </td>
                            <td>


                                <ul class="nav nav-pills">
                                    <li role="presentation" class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                            <span class="text">Action</span>

                                            <div class="iconholder">
                                                <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                                            </div>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('edit_template',['id'=> $template->id])  }}">Edit</a>

                                            </li>
                                            <li>
                                                @if($template->active)
                                                    <a href="{{ route('act_deact_template',['tid'=> $template->id ,'sid'=>0])  }}">Deactivate</a>
                                                @else
                                                    <a href="{{ route('act_deact_template',['tid'=> $template->id,'sid'=>1])  }}">Activate</a>
                                                @endif

                                            </li>
                                            <li>
                                                <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_template',['id'=> $template->id])  }}">Delete</a>

                                            </li>
                                        </ul>
                                    </li>
                                </ul>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th class='text-right' colspan="7"><?php echo $templates->render(); ?></th>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>


@endsection