@extends('layouts/main')

@section('content')


    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Integrations</h1>
        </div>
        <div class="col-md-4 text-right pt-3">
          
        </div>
        <div class="col-md-2 text-right pt-26">
            @if(Auth::user()->can('manage_campaign')) {{-- && count($integrations) == 0) --}}
                <a href="{{ route('add_integration')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Integration</a>
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
                        <th>API Key</th>
                        <th>Type</th>
                        @if(Auth::user()->can('manage_campaign'))
                            <th class="col-sm-2">Actions</th>
                        @endif                       
                    </tr>
                    </thead>
                    <tbody data-link="row">
                    <?php $i = 0;?>

                    @foreach($integrations as $integration)
                        <tr data-campaign_id="{{$integration->id}}">
                            <td><span class="campaign_name">{{ $integration->name }}</span>

                                <div class="row campaign_info">
                                    <div class="col-md-6">
                                        <strong>Created</strong>
                                    </div>
                                    <div class="col-md-6 ">
                                        {{date('F d,Y',strtotime($integration->created_at))}}
                                    </div>
                                </div>
                        @if(substr($integration->created_at,0,10) != substr($integration->updated_at,0,10))
                                <div class="row campaign_info">
                                    <div class="col-md-6">
                                        <strong>Updated</strong>
                                    </div>
                                    <div class="col-md-6 ">
                                        {{date('F d,Y',strtotime($integration->updated_at))}}
                                    </div>
                                </div>
                        @endif                       
                            </td>
                          
                            <td align="center">
                                <span class="light_gray">{{ $integration->type_id == 4 ? $integration->local_api_key : $integration->api_key }}</span>
                            </td>
                            <td align="center">
                                <span class="light_gray">{{ $integration->type_id ? $types[$integration->type_id] : '' }}</span>
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
                                                    <a href="{{ route('edit_integration',['id'=> $integration->id])  }}">Edit</a>
                                                </li>
                                               
                                                <li>
                                                    @if(Auth::user()->getOwner() == false)
                                                        <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_integration',['id'=> $integration->id])  }}">Delete</a>
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