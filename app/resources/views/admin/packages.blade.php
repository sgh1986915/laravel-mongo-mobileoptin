@extends('layouts/main')

@section('content')


<div class="row">
    <div class="col-md-6 ">
        <h1 class="category_header">Package Management</h1>
    </div>

</div>
<div class="row">
    <div class="col-md-12 ">
        <div class="table-responsive">
            <table id="campaign_table" class="table   table-curved list_options">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody data-link="row">
                    <?php $i = 0; ?>

                    @foreach($packages as $package)
                    <tr data-campaign_id="{{$package->id}}">
                        <td><span class="campaign_name" style="text-align: center">{{ $package->name }}</span> </td>
                        <td><span class="campaign_name" style="text-align: center">{{ $package->version }}</span> </td>
                        <td class="rowlink-skip">
                            <div style="text-align: center">
                                {!! Form::checkbox('status', Input::old('status'), $package->status == 1 ? true : false,array('id'=>'status', 'data-id' => $package->id,'class' => 'checkbox form-control','style'=>'position:relative;')) !!}
                            </div>
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
            var package_id = $(this).data('id');
            $.ajax({
                url: base_url + '/admin/activatepackage/' + package_id+'/'+state,
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