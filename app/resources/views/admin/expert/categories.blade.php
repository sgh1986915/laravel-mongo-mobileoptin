@extends('layouts/main')

@section('content')
<div class="row">
    <div class="col-md-8 ">
        <h1 class="category_header">TEA Category Management</h1>
    </div>
    <div class="col-md-2 text-right pt-26">
    </div>
    <div class="col-md-2 text-right pt-26">
    	@if(Auth::user()->can('manage_index_user_content'))
        	<a href="{{ route('admin.expert_traffic.add.category')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add A TEA Category </a>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12 ">
        <div class="table-responsive">
            <table id="campaign_table" class="table table-curved list_options">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th class="col-sm-2">Actions</th>
                    </tr>
                </thead>
                <tbody data-link="row">
                    <?php $i = 0; ?>
                    @foreach($faq_categories as $faq_category)
                    <tr data-faq_category_id="{{$faq_category->id}}">
                        <td><span class="campaign_name" style="text-align: center">{{ $faq_category->id }}</span> </td>
                        <td><span class="campaign_name" style="text-align: center">{{ $faq_category->name }}</span> </td>
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
                                        	<a href="{{ route('admin.expert_traffic.category.answers', ['id'=> $faq_category->id])  }}">Questions and Answers</a>
                                       	</li>	
                                   		<li>
                                        	<a href="{{ route('admin.expert_traffic.category.edit', ['id'=> $faq_category->id])  }}">Edit</a>
                                       	</li>
                                        <li>    
                                        	<a onclick="return confirm(' you want to delete?');" href="{{ route('admin.expert_traffic.category.delete', ['id'=> $faq_category->id])  }}">Delete</a>
                                        </li>
                                   </ul>
                                </li>
                             </ul>
                         </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <th class='text-right' colspan="7"><?php echo $faq_categories->render(); ?></th>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection