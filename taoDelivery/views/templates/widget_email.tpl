<?php if (get_data('groupcount') > 0) : ?>
<div class="data-container">
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<button id="email-btn" class="btn-info small" type="button">Email TestTakers</button>
		<!--  data-modal="#excluded-testtaker" -->
	</div>
</div>
<script type="text/javascript">
$('#email-btn').click(function() {
	jQuery('#testtaker-form').load("/tao/myExt/DeliveryAction/printHello", {'uri' : '<?= get_data('assemblyUri')?>'}, function() {
		$('body').prepend($('#modal-container'));
		$('#testtaker-form').modal();
	});
})
</script>
<?php endif; ?>