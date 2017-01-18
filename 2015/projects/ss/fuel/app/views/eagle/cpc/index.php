<form method="post" name="form01">

	<!-- クライアント選択 -->
	<div class="clearfix">
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

	<? if ($client_id) { ?>

		<!-- アカウント選択 -->
		<div class="clearfix">
			<fieldset class="columns">
				<legend>アカウント選択（<span id="select_account_count"><?= count($account_id_list) ?></span>/<?= count($account_list) ?>件）</legend>
				<textarea class="structure_filter_text" name="account_search_text" cols="15" placeholder="アカウント絞込み"><?= $account_search_text ?></textarea>
				<select name="account_search_type">
					<? foreach ($GLOBALS["structure_filter_type_list"] as $structure_filter_type) { ?>
						<?
						if ($structure_filter_type === $account_search_type) {
							$selected = "selected";
						} else {
							$selected = "";
						}
						?>
						<option value="<?= $structure_filter_type ?>" <?= $selected ?>><?= $structure_filter_type ?></option>
					<? } ?>
				</select>
				<input type="checkbox" name="account_search_id_only" value="checked" <?= $account_search_id_only ?> />ID検索
				&nbsp;&nbsp;
				<div class="primary btn" id="account_search"><a href="#" class="js-href-canceled">検索</a></div>
				</br>
				</br>
				<select id="account_id_list" name="account_id_list[]" multiple="multiple">
					<?
					$account_count = 0;

					foreach ($account_list as $account) {

						if ($account_id_list && in_array($account["account_pk"], $account_id_list)) {
							$selected = "selected";
						} else {
							$selected = "";
						}
					?>
						<option value="<?= $account["account_pk"] ?>"
								class="account_id_list<?= $account_count ?>" <?= $selected ?>><?= $account["account_name_disp"] ?></option>
					<?
						$account_count++;
					}
					?>
				</select>
				</br>
				<div class="primary btn" id="all_account_select"><a href="#" class="js-href-canceled">全選択</a></div>
				<div class="primary btn" id="all_account_cancel"><a href="#" class="js-href-canceled">全解除</a></div>
			</fieldset>
		</div>
		<div class="medium primary btn" id="new_campaign"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= ALERT_MSG_005 ?>">最新CPN取得</a></div>
		<input type="checkbox" name="new_campaign_not_disp" value="checked" <?= $new_campaign_not_disp ?> />UI非表示
		</br>
		</br>

		<? if (isset($new_campaign_not_disp) || !empty($account_id_list)) { ?>

			<!-- キャンペーン選択 -->
			<div class="clearfix">
				<fieldset class="columns">
					<legend>キャンペーン選択（<span id="select_campaign_count"><?= count($campaign_id_list) ?></span>/<?= count($campaign_list) ?>件）</legend>
					<textarea class="structure_filter_text" name="campaign_search_text" cols="15" placeholder="キャンペーン絞込み"><?= $campaign_search_text ?></textarea>
					<select name="campaign_search_type">
						<? foreach ($GLOBALS["structure_filter_type_list"] as $structure_filter_type) { ?>
							<?
							if ($structure_filter_type === $campaign_search_type) {
								$selected = "selected";
							} else {
								$selected = "";
							}
							?>
							<option value="<?= $structure_filter_type ?>" <?= $selected ?>><?= $structure_filter_type ?></option>
						<? } ?>
					</select>
					<input type="checkbox" name="campaign_search_id_only" value="checked" <?= $campaign_search_id_only ?> />ID検索
					&nbsp;&nbsp;
					<? if (isset($new_campaign_not_disp)) { ?>
						<div class="primary btn" id="campaign_dl"><a href="#" class="js-href-canceled">対象CPN DL</a></div>
					<? } else { ?>
						<div class="primary btn" id="campaign_search"><a href="#" class="js-href-canceled">検索</a></div>
					<? } ?>
					</br>
					</br>
					<select id="campaign_id_list" name="campaign_id_list[]" multiple="multiple">
						<?
						$campaign_count = 0;

						foreach ($campaign_list as $campaign) {

							if ($campaign_id_list && in_array($campaign["campaign_pk"], $campaign_id_list)) {
								$selected = "selected";
							} else {
								$selected = "";
							}
						?>
							<option value="<?= $campaign["campaign_pk"] ?>"
									class="campaign_id_list<?= $campaign_count ?>" <?= $selected ?>><?= $campaign["campaign_name_disp"] ?></option>
						<?
							$campaign_count++;
						}
						?>
					</select>
					</br>
					<div class="primary btn" id="all_campaign_select"><a href="#" class="js-href-canceled">全選択</a></div>
					<div class="primary btn" id="all_campaign_cancel"><a href="#" class="js-href-canceled">全解除</a></div>
				</fieldset>
			</div>
			<div class="medium primary btn" id="get_structure"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= ALERT_MSG_006 ?>">掲載取得</a></div>
			<input type="checkbox" name="get_structure_not_disp" value="checked" <?= $get_structure_not_disp ?> />UI非表示
			</br>
			</br>

			<!-- CPC変更 -->
			<div class="clearfix">
				<fieldset class="columns">
					<legend>CPC変更</legend>
					<div class="medium primary btn" id="reget_structure"><a href="#" class="js-href-canceled">掲載再取得</a></div>
					</br>
					</br>
					<div class="sub-content">
						<div class="info label budget-title">掲載一覧</div>
						<table class="report-table table striped" id="structure_list">
							<tr class="table-label">
								<td />
								<td>
									<textarea class="structure_filter_text" name="account_filter_text" cols="15"
											  placeholder="アカウント名絞込み"><?= $account_filter_text ?></textarea>
									<select name="account_filter_type">
										<? foreach ($GLOBALS["structure_filter_type_list"] as $structure_filter_type) { ?>
											<?
											if ($structure_filter_type === $account_filter_type) {
												$selected = "selected";
											} else {
												$selected = "";
											}
											?>
											<option value="<?= $structure_filter_type ?>" <?= $selected ?>><?= $structure_filter_type ?></option>
										<? } ?>
									</select>
									<input type="checkbox" name="account_filter_id_only" value="checked" <?= $account_filter_id_only ?> />ID検索
								</td>
								<td>
									<textarea class="structure_filter_text" name="campaign_filter_text" cols="15"
											  placeholder="キャンペーン名絞込み"><?= $campaign_filter_text ?></textarea>
									<select name="campaign_filter_type">
										<? foreach ($GLOBALS["structure_filter_type_list"] as $structure_filter_type) { ?>
											<?
											if ($structure_filter_type === $campaign_filter_type) {
												$selected = "selected";
											} else {
												$selected = "";
											}
											?>
											<option value="<?= $structure_filter_type ?>" <?= $selected ?>><?= $structure_filter_type ?></option>
										<? } ?>
									</select>
									<input type="checkbox" name="campaign_filter_id_only" value="checked" <?= $campaign_filter_id_only ?> />ID検索
								</td>
								<td>
									<textarea class="structure_filter_text" name="adgroup_filter_text" cols="15"
											  placeholder="広告グループ名絞込み"><?= $adgroup_filter_text ?></textarea>
									<select name="adgroup_filter_type">
										<? foreach ($GLOBALS["structure_filter_type_list"] as $structure_filter_type) { ?>
											<?
											if ($structure_filter_type === $adgroup_filter_type) {
												$selected = "selected";
											} else {
												$selected = "";
											}
											?>
											<option value="<?= $structure_filter_type ?>" <?= $selected ?>><?= $structure_filter_type ?></option>
										<? } ?>
									</select>
									<input type="checkbox" name="adgroup_filter_id_only" value="checked" <?= $adgroup_filter_id_only ?> />ID検索
								</td>
								<td style="display:none">
									<select class="structure_filter_text" name="matchtype_filter">
										<option value="">--</option>
										<? foreach ($GLOBALS["structure_matchtype_filter_list"] as $structure_matchtype_filter) { ?>
											<?
											if ($structure_matchtype_filter === $matchtype_filter) {
												$selected = "selected";
											} else {
												$selected = "";
											}
											?>
											<option value="<?= $structure_matchtype_filter ?>" <?= $selected ?>><?= $structure_matchtype_filter ?></option>
										<? } ?>
									</select>
								</td>
								<td>
									<? if (isset($get_structure_not_disp)) { ?>
										<div class="primary btn" id="structure_dl"><a href="#" class="js-href-canceled">対象掲載DL</a></div>
									<? } else { ?>
										<div class="primary btn" id="structure_filter"><a href="#" class="js-href-canceled">絞込み</a></div>
									<? } ?>
								</td>
							</tr>
							<tr class="table-label">
								<td>No</td>
								<td style="display:none">メディアID</td>
								<td style="display:none">アカウントID</td>
								<td style="display:none">アカウント名</td>
								<td>アカウント</td>
								<td style="display:none">キャンペーンID</td>
								<td style="display:none">キャンペーン名</td>
								<td>キャンペーン</td>
								<td style="display:none">広告グループID</td>
								<td style="display:none">広告グループ名</td>
								<td>広告グループ</td>
								<td style="display:none">マッチタイプ</td>
								<td style="display:none">【PC】</br>現在の設定CPC</td>
								<td>【PC】</br>設定CPC</td>
								<td>【PC】</br>変更CPC</td>
								<td style="display:none">【SP】</br>現在の設定CPC</td>
								<td>【SP】</br>設定CPC</td>
								<td>【SP】</br>変更CPC</td>
								<td style="display:none"></br>現在の設定MBA</td>
								<td></br>設定MBA</td>
								<td></br>変更MBA</td>
							</tr>
							<?
							foreach ($structure_list as $index => $structure) {

								$style = "";
								$style_ydn = "";

								if ($index >= STRUCTURE_LIST_MAX_DISP_NUM) {

									$style = "style='display:none'";
									$style_ydn = "style='display:none'";
								}

								if (!$style_ydn && intval($structure["media_id"]) === MEDIA_ID_IM) {

									$style_ydn = "style='display:none'";
								}
							?>
								<tr class="table-label">
									<td class="table-default" <?= $style ?>><?= $index + 1 ?></td>
									<td class="table-default" style="display:none"><?= $structure["media_id"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["account_id"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["account_name"] ?></td>
									<td class="table-default" <?= $style ?>><?= $structure["account_name_disp"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["campaign_id"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["campaign_name"] ?></td>
									<td class="table-default" <?= $style ?>><?= $structure["campaign_name_disp"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_id"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_name"] ?></td>
									<td class="table-default" <?= $style ?>><?= $structure["adgroup_name_disp"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_match_type_disp"] ?></td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_cpc_max"] ?></td>
									<td class="table-type-money" <?= $style ?> title=<?= "現在の設定：" . $structure["adgroup_cpc_max"] ?>>
										<?= $structure["adgroup_cpc_max"] ?>
									</td>
									<td class="table-type-money" <?= $style ?>>
										<input type="text" class="normal text input"
											   onChange="changeCpcMaxPc(this.value, this.parentNode.parentNode.rowIndex, true)" />
									</td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_cpc_max_sp"] ?></td>
									<td class="table-type-money" <?= $style_ydn ?> title=<?= "現在の設定：" . $structure["adgroup_cpc_max_sp"] ?>>
										<?= $structure["adgroup_cpc_max_sp"] ?>
									</td>
									<td class="table-type-money" <?= $style_ydn ?>>
										<input type="text" class="normal text input"
											   onChange="changeCpcMaxSp(this.value, this.parentNode.parentNode.rowIndex, true)" />
									</td>
									<td class="table-default" style="display:none"><?= $structure["adgroup_bid_modifier"] ?></td>
									<td class="table-default" <?= $style_ydn ?> title=<?= "現在の設定：" . $structure["adgroup_bid_modifier_disp"] . "%" ?>>
										<?= $structure["adgroup_bid_modifier_disp"] ?>%
									</td>
									<td class="table-default" <?= $style_ydn ?>>
										<input type="text" class="normal text input"
											   onChange="changeBidModifier(this.value, this.parentNode.parentNode.rowIndex, true)" />%
									</td>
								</tr>
							<? } ?>
						</table>
					</div>
					<? if (isset($get_structure_not_disp) || $structure_list) { ?>
						<? if (!isset($get_structure_not_disp)) { ?>
							<div class="medium primary btn" id="edit"><a href="#" class="js-href-canceled">入稿</a></div>
						<? } ?>
						<div class="medium primary btn" id="cpc_bulk_change"><a href="#" class="js-href-canceled">CPC一括変更</a></div>
						<div class="medium primary btn" id="cpc_evenness_change"><a href="#" class="js-href-canceled">CPC一律変更</a></div>
						<? if (!isset($get_structure_not_disp)) { ?>
							<div class="medium primary btn" id="cpc_change_dl">
								<a href="#" class="js-href-canceled ttip" data-tooltip="<?= ALERT_MSG_002 ?>">CPC変更内容DL</a>
							</div>
							<div class="medium primary btn" id="cpc_change_cancel"><a href="#" class="js-href-canceled">変更内容クリア</a></div>
						<? } ?>
					<? } ?>
				</fieldset>
			</div>
		<? } ?>
	<? } ?>
	<input type="hidden" name="action_type" />
	<input type="hidden" name="scroll_x" />
	<input type="hidden" name="scroll_y" />
	<input type="hidden" name="set_scroll_x" value="<?= $scroll_x ?>" />
	<input type="hidden" name="set_scroll_y" value="<?= $scroll_y ?>" />
</form>