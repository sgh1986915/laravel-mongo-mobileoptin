@extends('layouts/main')

@section('content')


    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Templates Groups</h1>
        </div>
        <div class="col-md-6 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('admin.templates.groups.add')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Termplate Groups</a>
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
                        <th class="col-sm-2">Number of templates</th>
                        <th class="col-sm-2">Created on</th>

                        <th class="col-sm-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($TemplatesGroups as $template)
                        <tr  data-link="row" class="rowlink usrmngrow">
                            <td>{{ $template->id }}</td>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->templates->count() }} Times</td>

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
                                                <a href="{{ route('admin.templates.groups.edit',['id'=> $template->id])  }}">Edit</a>

                                            </li>
                                            <li>
                                                <a onclick="return confirm(' you want to delete?');" href="{{ route('admin.templates.groups.delete',['id'=> $template->id])  }}">Delete</a>
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th class='text-right' colspan="7"><?php echo $TemplatesGroups->render(); ?></th>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>


@endsection