<div class="clearfix" id="select_account">
	<fieldset class="columns">
		<legend>アカウント選択（<span id="select_account_count"><?= count($OUT_data["account_id_list"]); ?></span>/<?= count($DB_account_list); ?>件）</legend>
		<select id="account_id_list" name="account_id_list[]" multiple="multiple">
			<? $cnt = 0; ?>
			<? foreach ($DB_account_list as $DB_account) { ?>
				<!-- 選択済みアカウントは引継 -->
				<? if (!empty($OUT_data["account_id_list"])) { ?>
					<option value="<?= $DB_account["media_id"] . "//" . $DB_account["account_id"] ?>" <? if (in_array($DB_account["media_id"] . "//" . $DB_account["account_id"], $OUT_data["account_id_list"])) { print "selected"; } ?>><?= "[" . $DB_account["account_id"] . "] " . $DB_account["account_name"] ?></option>
				<? } else { ?>
					<option id="account_id_list_<?= $cnt; ?>" value="<?= $DB_account["media_id"] . "//" . $DB_account["account_id"] ?>"><?= "[" . $DB_account["account_id"] . "] " . $DB_account["account_name"] ?></option>
				<? } ?>
				<? $cnt++; ?>
			<? } ?>
		</select>
		<br>
		<div class="medium primary btn" id="account_all_select_btn"><a href="#" class="js-href-canceled">全選択</a></div>
		<div class="medium primary btn" id="account_all_cancel_btn"><a href="#" class="js-href-canceled">全解除</a></div>
		<br><br>
		<div class="info label eagle-title">アカウント検索(部分一致)</div>
		<br><br>
		<input type="checkbox" name="account_search" value="1" <? if (!empty($OUT_data["account_search"])) { echo "checked"; } ?>>除外
		<br>
		<? if (empty($OUT_data["account_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="account_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="account_search_type" value="0" <?= $or_checked ?>>OR<br>
        <div class="field"><textarea class="input textarea" name="account_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["account_search_list"])) { echo $OUT_data["account_search_list"]; } ?></textarea></div>
		<div class="medium primary btn" id="account_search_btn"><a href="#" class="js-href-canceled">検索</a></div>
		<div class="medium primary btn" id="account_search_clear_btn"><a href="#" class="js-href-canceled">クリア</a></div>
	</fieldset>
</div>
