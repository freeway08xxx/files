<form method="post" name="form01">
	<div class="clearfix relate legacy">

		<!-- クライアント選択 -->
		<div class="clearfix select-client">
			<legend>クライアント選択（<?= count($client_list) ?>件）</legend>
			<select id="client_id" name="client_id">
				<option value="">--</option>
				<? foreach ($client_list as $client) { ?>
					<?
					if ($client["id"] === $client_id) {
						$selected = "selected";
					} else {
						$selected = "";
					}
					?>
					<option value="<?= $client["id"] ?>" <?= $selected ?>><?= $client["client_name_disp"] ?></option>
				<? } ?>
			</select>
		</div>

		<? if (!empty($account_list)) { ?>
			<div class="select-account">
				<!-- アカウント選択 -->
				<legend>アカウント選択（<?= count($account_list) ?>件）</legend>
				<select id="account_id" name="account_pk">
					<option value="">--</option>
					<? foreach ($account_list as $account) { ?>
						<?
						if ($account["account_pk"] === $account_pk) {
							$selected = "selected";
						} else {
							$selected = "";
						}
						?>
						<option value="<?= $account["account_pk"] ?>" <?= $selected ?>><?= $account["account_name_disp"] ?></option>
					<? } ?>
				</select>
			</div>

			<div class="list-group keywords">
				<div class="list-group-item">
					<!-- キーワード -->
					<div class="relate_reget checkbox">
						<label>
							<input type="checkbox" name="relate_reget" value="checked" <?= $relate_reget ?> />結果KWから再度関連KW取得
						</label>
					</div>
					<textarea class="form-control" name="relate_keyword"
						placeholder="キーワードを改行ごとに入力してください。"><?= $relate_keyword ?></textarea>
				</div>
			</div>

			<button type="button" class="btn btn-sm btn-primary" id="relate_get">関連キーワードを取得する</button>
		<? } ?>

	</div>

	<input type="hidden" name="action_type" />
</form>