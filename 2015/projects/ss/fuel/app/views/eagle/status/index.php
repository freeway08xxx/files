<form method="post" name="form01">
	<!-- 説明文 -->
	<div class="clearfix">
		<fieldset class="six columns eagle-description">
			<?= STATUS_DESCRIPTION; ?>
		</fieldset>
	</div>

	<!-- クライアント選択 START -->
	<div class="clearfix">
		<fieldset class="columns">
			<legend>クライアント選択（<?= count($DB_client_list); ?>件）</legend>
			<select id="client_id" name="client_id">
				<option value="">--</option>
			<? foreach ($DB_client_list as $DB_client) { ?>
				<!-- 選択済みクライアントは引継 -->
				<? if (!empty($OUT_data["client_id"])) { ?>
					<option value="<?= $DB_client["id"] ?>" <? if ($DB_client["id"] === $OUT_data["client_id"]) { print "selected"; } ?>><?= $DB_client["company_name"] ?><? if ($DB_client["client_name"]) echo "//" . $DB_client["client_name"]; ?></option>
				<? } else { ?>
					<option value="<?= $DB_client["id"] ?>"><?= $DB_client["company_name"] ?><? if ($DB_client["client_name"]) echo "//" . $DB_client["client_name"]; ?></option>
				<? } ?>
			<? } ?>
			</select>
		</fieldset>
	</div>
	<!-- クライアント選択 END -->

	<!-- クライアント選択済み -->
	<? if (!empty($OUT_data["client_id"])) { ?>
		<!-- アカウント選択 START -->
		<?= $HMVC_accounttable; ?>
		<!-- アカウント選択 END -->

		<!-- キャンペーン検索 START -->
		<?= $HMVC_campaigntable; ?>
		<!-- キャンペーン検索 END -->

		<!-- 対象CPN掲載取得済み START -->
		<?= $HMVC_componenttable; ?>
		<!-- 対象CPN掲載取得済み END -->
	<? } ?>
</form>
