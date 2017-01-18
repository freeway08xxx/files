<div class="row-fluid">
	<fieldset class="six columns" id="industry_base">
		<legend>業種</legend>

		<? if ($admin_flg) { ?>
			<div id="industry_0_edit" class="field register">
				<input type="text" class="xnarrow text input industry-sort" id="industry__sort" value="" placeholder="並び順">
				<input type="text" class="wide text input" id="industry__name" value="" placeholder="業種名">
				<div class="small info btn pull_right js-insert-btn"><a href="#">新規登録</a></div>
			</div>
		<? } ?>

		<?= $industry_list ?>
	</fieldset>

	<? if ($industry_id) { ?>
		<fieldset class="six columns" id="industry_class_base">
			<legend><span class="label default">詳細</span> <?=$current_name?></legend>

			<? if ($admin_flg) { ?>
				<div id="industry_class_0_edit" class="field register">
					<input type="hidden" id="industry_class" value="<?=$industry_id?>">

					<input type="text" class="xnarrow text input" id="industry_class__sort" value="" placeholder="並び順">
					<input type="text" class="wide text input" id="industry_class__name" value="" placeholder="業種詳細名">

					<div class="small info btn pull_right js-insert-btn"><a href="#">新規登録</a></div>
				</div>
			<? } ?>

			<?= $industry_class_list ?>
		</fieldset>
	<? } ?>
</div>