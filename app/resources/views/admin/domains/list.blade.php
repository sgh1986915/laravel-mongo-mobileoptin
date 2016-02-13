@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Domains Management</h1>
        </div>
        <div class="col-md-4 text-right pt-3">
          
        </div>
        <div class="col-md-2 text-right pt-26">

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
                                <span class="light_gray">{{ $domain->active ? 'Active' : 'Disabled' }}</span>
                            </td>
                            <td align="center">
                                <span class="orangered">{{ \MobileOptin\Models\Domains::textStatus($domain->status)  }}</span>
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
                                                    <a href="{{ route('admin.edit_domain',['id'=> $domain->id])  }}">Edit</a>
                                                </li>
                                                <li>
                                                    @if($domain->active)
                                                        <a href="{{ route('admin.change_status_domain',['id'=> $domain->id ,'status'=>0])  }}">Disable</a>
                                                    @else
                                                        <a href="{{ route('admin.change_status_domain',['id'=> $domain->id ,'status'=>1])  }}">Enable</a>
                                                    @endif
                                                </li>
                                                <li>
                                                    @if(Auth::user()->getOwner() == false)
                                                        <a onclick="return confirm(' you want to delete?');" href="{{ route('admin.delete_domain',['id'=> $domain->id])  }}">Delete</a>
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