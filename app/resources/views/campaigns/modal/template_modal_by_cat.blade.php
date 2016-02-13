<style>

#templateTabContainerId .wrapper {
    position:relative;
    margin:0 auto;
    overflow:hidden;
	padding:5px;
  	height:50px;
}

#templateTabContainerId .list {
    position:absolute;
    left:0px;
    top:0px;
  	min-width:3000px;
  	margin-left:12px;
    margin-top:0px;
}

#templateTabContainerId .list li{
	display:table-cell;
    position:relative;
    text-align:center;
    cursor:grab;
    cursor:-webkit-grab;
    color:#efefef;
    vertical-align:middle;
}

#templateTabContainerId .scroller {
  text-align:center;
  cursor:pointer;
  display:none;
  padding:7px;
  padding-top:11px;
  white-space:no-wrap;
  vertical-align:middle;
  background-color:#fff;
}

#templateTabContainerId .scroller-right{
  float:right;
}

#templateTabContainerId .scroller-left {
  float:left;
}

#TemplateTabModalContent .dd-option-image{
	width: 100%;
	max-width: 100%;
	height: auto;
}

#TemplateTabModalContent .dd-option.col-xs-12.label-checked {
    border: 4px solid #aaa !important;
}

#templateModal .modal-body{
    -webkit-user-select: none; /* webkit (safari, chrome) browsers */
    -moz-user-select: none; /* mozilla browsers */
    -khtml-user-select: none; /* webkit (konqueror) browsers */
    -ms-user-select: none; /* IE10+ */
}

</style>
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 1000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Choose a template</h4>
            </div>
            <div class="modal-body">
	            <div id="templateTabContainerId">
		  			<div class="scroller scroller-left"><i class="glyphicon glyphicon-chevron-left"></i></div>
		  			<div class="scroller scroller-right"><i class="glyphicon glyphicon-chevron-right"></i></div>
					<div class="wrapper">
						<ul class="nav nav-tabs list" id="templateTabModal">
							
						</ul>
					</div>
				</div>
				<div id="TemplateTabModalContent" class="tab-content">
					
				</div>
            </div>
            <div class="modal-footer">
            	<div class="control-group">
          			<button type="button" class="btn btn-default" id="template_choosen_cancel" data-value="cancel">Cancel</button>
          			<button type="button" class="btn btn-default" id="template_choosen_selected" data-value="save">Save</button>
	          	</div>
            </div>
        </div>
    </div>
</div>