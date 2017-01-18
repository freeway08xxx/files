<form method="post" name="form01">
	<div class="legacy">

		<!-- クライアント選択 -->
		<div class="clearfix select-client">
			<fieldset class="columns">
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
			</fieldset>
		</div>

		<? if ($account_list) { ?>

			<!-- アカウント選択 -->
			<div class="clearfix select-account with-search">
				<fieldset class="columns">
					<legend>アカウント選択（<span id="select_account_count"><?= count($account_id_list) ?></span>/<?= count($account_list) ?>件）</legend>

					<div class="account-select-box">
						<select class="account-id-list" id="account_id_list" name="account_id_list[]" multiple="multiple">
							<? foreach ($account_list as $index => $account) { ?>
								<?
								if ($account_id_list && in_array($account["account_pk"], $account_id_list)) {
									$selected = "selected";
								} else {
									$selected = "";
								}
								?>
								<option value="<?= $account["account_pk"] ?>"
										class="account_id_list<?= $index ?>" <?= $selected ?>><?= $account["account_name_disp"] ?></option>
							<? } ?>
						</select>

						<button type="button" class="btn btn-xs btn-default" id="all_account_select">全選択</button>
						<button type="button" class="btn btn-xs btn-default" id="all_account_cancel">全解除</button>
					</div>

					<div class="account-search clearfix well well-sm">
						<div class="area-title">
							<span class="label label-info">アカウント検索</span>
						</div>
						<div class="search-option-area">
								<span class="option-chkbox">
									<label><input type="checkbox" name="account_search_except" value="checked" <?= $account_search_except ?> /> 除外</label>
									<label><input type="checkbox" name="account_search_broad" value="checked" <?= $account_search_broad ?> /> 部分一致</label>
									<label><input type="checkbox" name="account_search_id_only" value="checked" <?= $account_search_id_only ?> /> ID検索</label>
								</span>
								<br>
								<span class="option-radio">
									<?
									if ($account_search_type === "and") {
										$account_search_and = "checked";
										$account_search_or = "";
									} else {
										$account_search_and = "";
										$account_search_or = "checked";
									}
									?>
									<label><input type="radio" name="account_search_type" value="and" <?= $account_search_and ?> /> AND</label>
									<label><input type="radio" name="account_search_type" value="or" <?= $account_search_or ?> /> OR</label>
								</span>
						</div>

						<div class="search-input-area">
							<textarea name="account_search_text" class="form-control form-inline"
								placeholder="検索文字列を入力してください。"><?= $account_search_text ?></textarea>

							<button type="button" class="btn btn-sm btn-default" id="account_search">
								検索
							</button>
						</div>
					</div>

				</fieldset>
			</div>

			<!-- アカウント同期実行 -->
			<div class="account-sync-option">
				<h4>アカウント同期実行</h4>

				<div class="clearfix list-group">
					<!-- 実行種別 -->
					<div class="list-group-item">
						<h6 class="item-title">実行種別</h6>
						<?
						if (is_null($account_sync_type) || $account_sync_type === ACCOUNT_SYNC_TYPE_EXECUTE) {
							$account_sync_execute = "checked";
							$account_sync_reserve = "";
						} else {
							$account_sync_execute = "";
							$account_sync_reserve = "checked";
						}
						?>
						<input type="radio" name="account_sync_type" value="<?= ACCOUNT_SYNC_TYPE_EXECUTE ?>" <?= $account_sync_execute ?> /> 即時実行 &nbsp;
						<input type="radio" name="account_sync_type" value="<?= ACCOUNT_SYNC_TYPE_RESERVE ?>" <?= $account_sync_reserve ?> /> 予約
					</div>

					<!-- 予約日時 -->
					<div class="account_sync_datetime list-group-item">
						<h6 class="item-title">予約日時</h6>
						<input type="text" class="text input" id="account_sync_date_from" name="account_sync_date_from" value="<?= $account_sync_date_from ?>" size="10" /> ～
						<input type="text" class="text input" id="account_sync_date_to" name="account_sync_date_to" value="<?= $account_sync_date_to ?>" size="10" />&nbsp;
						<select name="account_sync_time">
							<? for ($i = 0; $i < 24; $i++) { ?>
								<?
								if (strval($i) === $account_sync_time) {
									$selected = "selected";
								} else {
									$selected = "";
								}
								?>
								<option value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
							<? } ?>
						</select>:
						<select name="account_sync_minutes">
							<? foreach ($GLOBALS["account_sync_minutes"] as $key => $value) { ?>
								<?
								if (strval($key) === $account_sync_minutes) {
									$selected = "selected";
								} else {
									$selected = "";
								}
								?>
								<option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
							<? } ?>
						</select>
					</div>

					<!-- 実行内容 -->
					<div class="list-group-item">
						<h6 class="item-title">実行内容</h6>
						<?
						if (is_null($account_sync_content) || $account_sync_content === ACCOUNT_SYNC_CONTENT_SYNC) {
							$account_sync_sync = "checked";
							$account_sync_review = "";
						} else {
							$account_sync_sync = "";
							$account_sync_review = "checked";
						}
						?>
						<input type="radio" name="account_sync_content" value="<?= ACCOUNT_SYNC_CONTENT_SYNC ?>" <?= $account_sync_sync ?> /> アカウント同期 &nbsp;
						<input type="radio" name="account_sync_content" value="<?= ACCOUNT_SYNC_CONTENT_REVIEW ?>" <?= $account_sync_review ?> /> 審査状況取得
					</div>

					<!-- 出力フォーマット -->
					<div class="list-group-item">
						<span class="account_sync_out_format">
							<h6 class="item-title">出力フォーマット</h6>
							<select name="account_sync_out_format" class="form-control input-sm sync-out-format">
								<? foreach ($GLOBALS["account_sync_out_format_list"] as $key => $value) { ?>
									<?
									if (strval($key) === $account_sync_out_format) {
										$selected = "selected";
									} else {
										$selected = "";
									}
									?>
									<option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
								<? } ?>
							</select>
						</span>
					</div>

					<!-- EAGLE実行 -->
					<div class="list-group-item">
						<h6 class="item-title">EAGLE実行</h6>
						<?
						if ($account_sync_eagle === ACCOUNT_SYNC_EAGLE_ON) {
							$account_sync_eagle_on = "checked";
							$account_sync_eagle_off = "";
						} else {
							$account_sync_eagle_on = "";
							$account_sync_eagle_off = "checked";
						}
						?>
						<input type="radio" name="account_sync_eagle" value="<?= ACCOUNT_SYNC_EAGLE_ON ?>" <?= $account_sync_eagle_on ?> /> ON &nbsp;
						<input type="radio" name="account_sync_eagle" value="<?= ACCOUNT_SYNC_EAGLE_OFF ?>" <?= $account_sync_eagle_off ?> /> OFF
					</div>

					<!-- 完了メール宛先 -->
					<div class="list-group-item">
						<h6 class="item-title">完了メール宛先</h6>
						<textarea class="account_sync_mail_address form-control input-sm" name="account_sync_mail_address" cols="60"
								  placeholder="宛先アドレスをカンマ区切りで入力してください。"><?= $account_sync_mail_address ?></textarea>
					</div>
				</div>

				<button type="button" class="btn btn-sm btn-primary" id="sync_execute">アカウント同期を実行する</button>
			</div>


			<!-- アカウント同期一覧 -->
			<div class="clearfix sync-list">
				<fieldset class="columns">
					<h4 class="area-title">アカウント同期結果</h4>

					<button type="button" class="btn btn-xs btn-default reload-btn" id="reload">
						再読み込み
					</button>

					<div class="sub-content">
						<table class="report-table table striped display" id="account_sync_list">
							<thead>
								<tr class="table-label">
									<td />
									<td>予約ID</td>
									<td>アカウント</td>
									<td>実行日時</td>
									<td>実行内容</td>
									<td>出力フォーマット</td>
									<td>EAGLE実行</td>
									<td>登録者</td>
									<td>登録日時</td>
									<td>ステータス</td>
									<td>結果DL</td>
								</tr>
							</thead>
							<tbody>
								<? foreach ($account_sync_list as $account_sync) { ?>
									<tr class="table-label">
										<td class="table-default">
											<input type="checkbox" class="account_sync_row" name="account_sync_row"
												   value="<?= $account_sync["id"] ?>" <?= $account_sync_row ?> />
										</td>
										<td class="table-default"><?= $account_sync["reserve_id"] ?></td>
										<td class="table-default"><?= $account_sync["account_name_disp"] ?></td>
										<td class="table-default"><?= $account_sync["action_date_time"] ?></td>
										<td class="table-default"><?= $account_sync["account_sync_content"] ?></td>
										<td class="table-default"><?= $account_sync["output_format_disp"] ?></td>
										<td class="table-default"><?= $account_sync["account_sync_eagle"] ?></td>
										<td class="table-default"><?= $account_sync["user_name"] ?></td>
										<td class="table-default"><?= $account_sync["created_at"] ?></td>
										<td class="table-default"><?= $account_sync["action_status"] ?></td>
										<td class="table-default">
											<?
											if (isset($account_sync["out_file_path"])
													&& is_file($account_sync["out_file_path"])
													&& file_exists($account_sync["out_file_path"])) {

												$param = explode("/", $account_sync["out_file_path"]);
											?>
												<!-- <a href="/sem/new/accountsync/execute/sync_dl/<?= $param[4] ?>/<?= $param[5] ?>/<?= $account_sync["account_id"] ?>">DL</a> -->
												<a href="/sem/new/accountsync/execute/sync_dl/<?= $param[5] ?>/<?= $param[6] ?>/<?= $account_sync["account_id"] ?>">DL</a>
											<? } ?>
										</td>
									</tr>
								<? } ?>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn btn-sm btn-default" id="all_account_sync_select">全選択</button>
					<button type="button" class="btn btn-sm btn-default right_10px" id="all_account_sync_cancel">全解除</button>

					<button type="button" class="btn btn-sm btn-info" id="all_account_sync_bulk_dl">一括DL</button>
					<button type="button" class="btn btn-sm btn-danger" id="all_account_sync_delete">予約削除</button>
				</fieldset>
			</div>
		<? } ?>
		<input type="hidden" name="action_type" />
		<input type="hidden" name="account_sync_select_row" />
		<input type="hidden" name="scroll_x" />
		<input type="hidden" name="scroll_y" />
		<input type="hidden" name="set_scroll_x" value="<?= $scroll_x ?>" />
		<input type="hidden" name="set_scroll_y" value="<?= $scroll_y ?>" />

	</div>
</form>