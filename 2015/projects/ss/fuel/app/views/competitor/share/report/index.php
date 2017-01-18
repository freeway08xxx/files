<!-- ContentsArea Start -->
<div class="content legacy report">
	<form name="form01" method="post" action="#">
		<?= $search_form; ?>
		<input type="hidden" name="action_type">
	</form>

	<form name="form02" method="post" action="#">
		<?= $search_hidden; ?>
		<input type="hidden" name="sort_key">
		<input type="hidden" name="sort_type">
		<input type="hidden" name="action_type">
	</form>

	<?= $table; ?>
</div>
<!-- ContentsArea End -->
