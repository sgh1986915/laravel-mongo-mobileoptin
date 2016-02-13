<div class="modal fade" id="pixelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Conversion Tracking Pixel</h4>
            </div>
            <div class="modal-body">
                <h4>Setup</h4>

                <p>
                    Add this HTML code to your page to track conversions: <br/>
                    <button class="btn btn-default btn-sm" data-clipboard-text="the_url" data-clipboard-target="pixel_tag" id="copy-url-to-cb">Copy to clipboard</button>
                    <pre class="pre-scrollable" id="pixel_tag">&lt;img src="<span id="pixel_url_container">{|url_to_pixel|}</span>"&gt;</pre>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>