@extends('layouts/main')

@section('content')



    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Users</h1>
        </div>
        <div class="col-md-3 text-right pt-26">
            <form class="form-inline" role="form"  action="{{ url('/admin/users') }}">
                <input class="form-control input-sm" type="text" name="search_email" placeholder="enter user email">
                <button type="submit" class="btn btn-default btn-sm">Search</button>
            </form>
        </div>
        <div class="col-md-3 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('add_user')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create User</a>
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
                        <th>email</th>
                        <th class="col-sm-2">Registerd on</th>
                        <th class="col-sm-2">group</th>

                        <th class="col-sm-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr data-link="row" class="rowlink usrmngrow">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td> {{ $user->email }}</td>
                            <td>
                                {{ date('m-d-Y',strtotime($user->created_at)) }}
                            </td>
                            <td>{{ $user->role->role_title }}</td>

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
                                        	 @if(Auth::user()->hasRole('admin'))
                                        		<li>
                                                	<a href="{{ route('connect_as_user',['id'=> $user->id])  }}">Connect as User </a>
                                            	</li>
                                            @endif
                                            <li>
                                                <a href="{{ route('edit_user',['id'=> $user->id])  }}">Edit</a>
                                            </li>
                                            <li>
                                                <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_user',['id'=> $user->id])  }}">Delete</a>
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <th class='text-right' colspan="7"><?php echo $users->render(); ?></th>
                    </tfoot>
                </table>
             </div>

        </div>
    </div>



@endsection