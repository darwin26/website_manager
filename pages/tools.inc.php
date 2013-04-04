<?php
$func = rex_request('func', 'string');

if ($func == 'generate_all') {
	$REX['WEBSITE_MANAGER']->generateAll();
	echo rex_info($I18N->msg('website_manager_tools_cache_deleted'));
}
?>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('website_manager_tools_cache_tool'); ?></h2>
	<div class="rex-area-content">
		<p><?php echo $I18N->msg('website_manager_tools_cache_tool_desc'); ?></p>
		<form action="index.php" method="post">		
			<p class="button">
				<input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('website_manager_tools_cache_tool_button'); ?>" />
			</p>
			<input type="hidden" name="page" value="website_manager" />
			<input type="hidden" name="subpage" value="tools" />
			<input type="hidden" name="func" value="generate_all" />
		</form>
	</div>
</div>

<style type="text/css">
.rex-addon-output p {
	margin-bottom: 5px;
}
</style>
