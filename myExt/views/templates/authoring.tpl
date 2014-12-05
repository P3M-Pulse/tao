<div class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select delivery test')?>222
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<?=get_data('formContent')?>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-<?=get_data('formId')?>" type="button" value="<?=__('Save')?>" />
	</div>	
</div>
<script type="text/javascript">
require(['jquery', 'i18n', 'helpers', 'generis.tree.select'], function($, __, helpers) {
    
    });
</script>