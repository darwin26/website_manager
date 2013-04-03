<?php
$func = rex_request('func', 'string');

if ($func == 'generate_all') {
	$REX['WEBSITE_MANAGER']->generateAll();
	echo rex_info("Cache wurde für alle Websites gelöscht.");
}
?>

<div class="rex-addon-output">
	<h2 class="rex-hl2">Cache löschen für alle Websites</h2>
	<div class="rex-area-content">
		<p>Wenn Sie ein Template oder Modul geändert haben, ist es nötig den Cache für alle Websites zu löschen.</p>
		<form action="index.php" method="post">		
			<p class="button">
				<input type="submit" class="rex-form-submit" name="sendit" value="Globales Cache löschen" />
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
