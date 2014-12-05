<?php if (get_data('groupcount') > 0) : ?>
 <div class="grid-row">
        <div class="col-12">
    	   <div id="form-title-history" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
            	Request Review from Peers
            </div>
            <div id="form-history" class="ui-widget-content ui-corner-bottom">
<div class="data-container col-12">
	<div class="" style="padding:5px;">
		<button id="email-btn" class="btn-info small" type="button">Publish</button>
		<button id="move-btn" class="btn-info small" type="button">Compile Results</button>
		<!--  data-modal="#excluded-testtaker" -->
	</div>
</div>
   </div>
        </div>
    </div>

<script type="text/javascript">
$('#email-btn').click(function() {
	jQuery('#testtaker-form').load("/myExt/Authoring/peerDeliveryPublish", {'uri' : '<?= get_data('assemblyUri')?>'}, function() {
		$('body').prepend($('#modal-container'));
		$('#testtaker-form').modal();
	});
});
$('#move-btn').click(function() {
	jQuery('#testtaker-form').load("/myExt/DeliveryAction/moveResults", {'uri' : '<?= get_data('assemblyUri')?>'}, function() {
		$('body').prepend($('#modal-container'));
		$('#testtaker-form').modal();
	});
})
</script>
<?php endif; ?>