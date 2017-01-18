<div class="clearfix select-account" id="select_account">
	<fieldset class="columns">
		<legend>アカウント選択（<span id="select_account_count"><?= count($OUT_data["account_id_list"]); ?></span>/<?= count($DB_account_list); ?>件）</legend>

		<div class="account-select-box">
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
			<button type="button" class="btn btn-xs btn-default" id="account_all_select_btn">全選択</button>
			<button type="button" class="btn btn-xs btn-default" id="account_all_cancel_btn">全解除</button>
		</div>

		<button type="button" class="btn btn-sm btn-primary" id="userlist_get_btn">データ取得</button>
	</fieldset>
</div>