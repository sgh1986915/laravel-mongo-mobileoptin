@extends('layouts/main')

@section('content')


<div class="row">
    <div class="col-md-6 ">
        <h1 class="category_header">Packages Management</h1>
    </div>
            <div class="col-md-4 text-right pt-3">
          
        </div>
        <div class="col-md-2 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('admin.add_package')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Package</a>
            @endif

        </div>

</div>
<div class="row">
    <div class="col-md-12 ">
        <div class="table-responsive">
            <table id="campaign_table" class="table   table-curved list_options">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jvzoo ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th class="col-sm-2">Actions</th>

                    </tr>
                </thead>
                <tbody data-link="row">
                    <?php $i = 0; ?>

                    @foreach($packages as $module)
                    <tr data-campaign_id="{{$module->id}}">
                        <td><span class="campaign_name" style="text-align: center">{{ $module->id }}</span> </td>
                        <td><span class="campaign_name" style="text-align: center">{{ $module->jvzoo_id }}</span> </td>
                        <td><span class="campaign_name" style="text-align: center">{{ $module->name }}</span> </td>
                        <td class="rowlink-skip">
                            <div style="text-align: center">
                                {!! Form::checkbox('status', Input::old('status'), $module->status == 1 ? true : false,array('id'=>'status', 'data-id' => $module->id,'class' => 'checkbox form-control','style'=>'position:relative;')) !!}
                            </div>
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
                                                <a href="{{ route('admin.edit_package',['id'=> $module->id])  }}">Edit</a>
                                            </li>
                                            <li>    
                                                <a onclick="return confirm(' you want to delete?');" href="{{ route('admin.delete_package',['id'=> $module->id])  }}">Delete</a>
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </td>
                    </tr>
                    

                    @endforeach
                </tbody>

            </table>


        </div>
    </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript" defer="defer">
    $(document).ready(function () {
        $("[name='status']").bootstrapSwitch();
        $("[name='status']").on('switchChange.bootstrapSwitch', function (event, state) {
            var module_id = $(this).data('id');
            $.ajax({
                url: base_url + '/admin/package/activatepackage/' + module_id+'/'+state,
                type: 'GET',
                dataType: "json",
                success: function (content) {
                    
                },
                error: function (errordata) {
                    console.log(errordata);
                }
            });
        })

    });

</script>
@endsection