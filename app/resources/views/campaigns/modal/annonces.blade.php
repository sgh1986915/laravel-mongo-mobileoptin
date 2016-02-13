<div class="modal fade" id="annoncesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= $title ?></h4>
            </div>
            <div class="modal-body" style="padding: 0;">
            	<?= $content ?>
            </div>
            <div class="modal-footer">
            	<div class="control-group">
	          		<div clas="controls controls-announcement-modal">
	          			<div class="col-xs-12 col-md-9" style="padding-left: 0; text-align: left;">
	          				<div class="checkbox">
						     	<label>
						      		<input name="never_display_announce" type="checkbox">
						      		Never display this message again?
						      	</label>
						    </div>
	          			</div>
	          			<div class="col-xs-12 col-md-3" style="padding-left: 0">
	          				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	          			</div>
	          		</div>
	          	</div>
            </div>
        </div>
    </div>
</div>