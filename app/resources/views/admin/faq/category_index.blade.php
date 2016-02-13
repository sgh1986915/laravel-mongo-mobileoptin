@extends('layouts/main')

@section('content')
<div class="row">
    <div class="col-md-6 ">
        <h1 class="category_header"><b>{{ $faq_category_name }}</b> / Faq Management</h1>
    </div>
    <div class="col-md-4 text-right pt-3"></div>
    <div class="col-md-2 text-right pt-26">
    	@if(Auth::user()->can('manage_index_user_content'))
        	<a href="{{ route('admin.faq.add', ['id'=> $faq_category_id])  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add a Question </a>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12 ">
        <div class="table-responsive">
            <table id="" class="table table-curved list_options">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>View PDF</th>
                        <th class="col-sm-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faq_answers as $faq_answer)
                    <tr data-faq_id="{{$faq_answer->id}}">
                        <td><span class="" style="text-align: center">{{ $faq_answer->id }}</span></td>
                        <td><span class="" style="text-align: center">{{ $faq_answer->question }}</span></td>
                        <td><span class="" style="text-align: center">{!! $faq_answer->answer !!}</span></td>
                        
                        {{--*/ $pdf_link = null  /*--}}
                        @if(!empty($faq_answer->pdf_file))
                        	{{--*/ $pdf_link = '/public/faq/' . $faq_answer->pdf_file  /*--}}
                        @endif
                        <td>
                        	{!! !empty($pdf_link) ? link_to_asset($pdf_link, 'View PDF', ['class' => 'open-faq-pdf', 'target' => '_blank', 'style' => 'text-align: center'], false) : '' !!}
                        </td>
                        <td>
                        	<div class="dropdown">
	                        	<ul class="nav nav-pills">
	                           		<li role="presentation">
	                                	<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	                                    	<span class="text">Action</span>
											<span class="iconholder">
	                                            <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
	                                        </span>
	                                   </a>
	                                   <ul class="dropdown-menu">	
	                                   		<li>
	                                        	<a href="{{ route('admin.faq.edit', ['category_id' => $faq_answer->faq_category_id, 'id'=> $faq_answer->id])  }}">Edit</a>
	                                       	</li>
	                                       	@if(!empty($pdf_link))
	                                        <li>    
	                                        	<a onclick="return confirm(' you want to delete the pdf?');" href="{{ route('admin.faq.delete.pdf', ['id'=> $faq_answer->id])  }}">Delete PDF</a>
	                                        </li>
	                                        @endif
	                                        <li>    
	                                        	<a onclick="return confirm(' you want to delete?');" href="{{ route('admin.faq.delete', ['id'=> $faq_answer->id])  }}">Delete</a>
	                                        </li>
	                                   </ul>
	                                </li>
	                             </ul>
	                        </div>
                         </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <th class='text-right' colspan="7"><?php echo $faq_answers->render(); ?></th>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection