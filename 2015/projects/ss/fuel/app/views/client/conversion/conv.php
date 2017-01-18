<div class="conversion clientmenu">
	<h4 class="heading">コンバージョン設定</h4>

	<form method="post" name="form01" id="cv_setting">
		<select class="" id="cv_list" name="cv_list[]" multiple>
			<? foreach ($DB_conv_list as $DB_conv) { ?>
				<? $selected = ""; ?>
				<? foreach ($DB_setting_list as $DB_setting) { ?>
					<? if ($DB_conv["tool_id"] === $DB_setting["tool_id"] && $DB_conv["cv_name"] === $DB_setting["cv_name"]) { ?>
							<? $selected = "selected"; ?>
							<? break; ?>
					<? } ?>
				<? } ?>
				<? if (isset($DB_conv["tool_name"])) { ?>
					<? $value = "[".$DB_conv["tool_id"].":".$DB_conv["tool_name"]."]".$DB_conv["cv_name"]; ?>
					<? $display = "[".$DB_conv["tool_name"]."]".$DB_conv["cv_name"]; ?>
				<? } else { ?>
					<? $value = "[".$DB_conv["tool_id"]."]".$DB_conv["cv_name"]; ?>
					<? $display = $DB_conv["cv_name"]; ?>
				<? } ?>
				<option value="<?= $value; ?>" <?= $selected; ?>><?= $display; ?></option>
			<? } ?>
		</select>

		<div class="btn-area">
			<button type="button" class="btn btn-primary btn-sm" id="setting_btn"
			 ng-click="cv.methods.submit()">
				設定
			</button>
		</div>
	</form>
</div>

<!--  apply jQuery Dom Manipulate after Angular Route Resolve -->
<script>
	$('#cv_list').select2();
</script>
