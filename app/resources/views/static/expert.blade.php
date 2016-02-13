@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tab-content">
                <div class="row">
                	<div class='col-md-4' id="faq-menu-category">
                		@if(!empty($faq_category) && $faq_category->count() > 0) 
	                		<h4 style="margin-top: 0 !important;">TEA  CATEGORY</h4>
	                		<div class="row-md-12 list-group">
	                			@foreach($faq_category as $faq_cat)
	                				@if($faq_cat->id == $faq_category_selected) 
	                					<a href="{{ route('support.with.expert_categories', ['faq_category_id' =>  $faq_cat->id])  }}" class="list-group-item active">{{ $faq_cat->name }}</a>
	                				@else
	                					<a href="{{ route('support.with.expert_categories', ['faq_category_id' =>  $faq_cat->id])  }}" class="list-group-item">{{ $faq_cat->name }}</a>
	                				@endif
	                			@endforeach
	                		</div>
                		@endif
                	</div>
                	<div class="col-md-8">
                		@if(!empty($faq_category_answer) && $faq_category_answer->count() > 0)
	                		<div class="bar-color">
	                			<a href="#" id="faq-answer-expand">Expand</a> / <a href="#" id="faq-answer-collapse">Collapse All</a>
	                		</div>
	                		<div class="panel-group" id="accordionFaq" role="tablist" aria-multiselectable="true">
	                			{{--*/ $first = true /*--}}
		                		@foreach($faq_category_answer as $faq_answer)
		                			{{--*/ $pdf_link = null  /*--}}
			                        @if(!empty($faq_answer->pdf_file))
			                        	{{--*/ $pdf_link = '/public/expert/' . $faq_answer->pdf_file  /*--}}
			                        @endif
		                			<div class="panel panel-default">
									    <div class="panel-heading" role="tab" id="headingOne">
									      <h4 class="panel-title">
									        <a role="button" data-toggle="collapse" data-parent="#accordionFaq" href="#collapseFaq-{{$faq_answer->id}}" aria-expanded="{{ $first == true ? 'true' : 'false' }}" aria-controls="collapseFaq-{{$faq_answer->id}}">
									          {{ $faq_answer->question }}
									        </a>
									      </h4>
									    </div>
									    <div id="collapseFaq-{{$faq_answer->id}}" class="panel-collapse collapse {{ $first == true ? 'in' : '' }}" role="tabpanel" aria-labelledby="collapseFaq-{{$faq_answer->id}}">
									      <div class="panel-body">
									      	{!! $faq_answer->answer !!}
										    <div>
										     		{!! !empty($pdf_link) ? link_to_asset($pdf_link, 'Click here for PDF version of this question', ['class' => 'open-faq-pdf', 'target' => '_blank', 'style' => 'text-align: center'], false) : '' !!}
										    </div>
									      </div>
									    </div>
									</div>
									{{--*/ $first = false /*--}}
		                   	 	@endforeach
		                   	 </div>
		                 @endif
                	</div>
                </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function () {
	$("#faq-answer-collapse").click(function(){
		$('.panel-collapse.in').collapse('hide');
	});
	$("#faq-answer-expand").click(function(){
		$('.panel-collapse:not(".in")').collapse('show');
	});
});
</script>
@endsection