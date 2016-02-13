@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                For instructions on adding your domains names to this list please watch the How To Video In Support > FAQ

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Domains</h1>
        </div>
        <div class="col-md-4 text-right pt-3">
          
        </div>
        <div class="col-md-2 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('add_domain')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Domain</a>
            @endif

        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="table-responsive">
                <table id="campaign_table" class="table   table-curved list_options">
                    <thead>
                    <tr>
                        <th>Name</th>
                        
                        <th>Active</th>
                        <th>Status</th>
                        @if(Auth::user()->can('manage_campaign'))
                            <th class="col-sm-2">Actions</th>
                        @endif
                       
                    </tr>
                    </thead>
                    <tbody data-link="row">
                    <?php $i = 0;?>

                    @foreach($domains as $domain)
                        <tr data-campaign_id="{{$domain->id}}">

                            <td><span class="campaign_name">{{ $domain->name }}</span>


                                <div class="row campaign_info">
                                    <div class="col-md-6">
                                        <strong>Created</strong>
                                    </div>
                                    <div class="col-md-6 ">
                                        {{date('F d,Y',strtotime($domain->created_at))}}
                                    </div>
                                </div>
                                <div class="row campaign_info">
                                    <div class="col-md-6">
                                        <strong>Updated</strong>
                                    </div>
                                    <div class="col-md-6 ">
                                        {{date('F d,Y',strtotime($domain->updated_at))}}
                                    </div>
                                </div>
                            </td>

                          
                            <td align="center">
                                <span class="light_gray">{{ $domain->active ? 'Active' : 'Pending' }}</span>
                            </td>
                            <td align="center">
                                    <span class="light_gray">{{ \MobileOptin\Models\Domains::textStatus($domain->status)  }}</span>
                            </td>
                      
                      
                            @if(Auth::user()->can('manage_campaign'))
                                <td class="rowlink-skip">


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
                                                    <a href="{{ route('edit_domain',['id'=> $domain->id])  }}">Edit</a>
                                                </li>
                                               
                                                <li>
                                                    @if(Auth::user()->getOwner() == false)
                                                        <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_domain',['id'=> $domain->id])  }}">Delete</a>
                                                    @endif
                                                </li>
                                            
                                            </ul>
                                        </li>
                                    </ul>

                                </td>
                            @endif
                        </tr>

                    @endforeach
                    </tbody>
                   
                </table>


            </div>
        </div>
    </div>


@endsection