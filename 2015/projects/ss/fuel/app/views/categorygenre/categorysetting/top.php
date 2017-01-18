<form enctype="multipart/form-data" id="form01" name="form01" method="POST" onsubmit="">
<h3 id="page_title">カテゴリ設定一覧の表示・ダウンロード</h3>
<div class="clearfix">
	<fieldset class="columns back-col">
		<legend>1．カテゴリジャンルを選択</legend>
		<div>
			<table class="form-table">
				<tr>
					<td>カテゴリジャンル名<font color="red">（必須）</font></td>
					<td>
						<div>
							<select class="select_genre_id" name="select_genre_id">
								<option value="--">--</option>
								<? foreach($genre_list as $genre) { ?>
									<option value="<?=$genre["id"]?>"><?=$genre["category_genre_name"]?>　（<?=$genre["user_name"]?>：<?=$genre["datetime"]?>）</option>
								<?}?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>カテゴリ設定単位<font color="red">（必須）</font></td>
					<td>
						<select class="select_element_type_id" name="select_element_type_id">
							<option value="0">--</option>
							<option class="element_account" value="1">アカウント</option>
							<option class="element_campaign" value="2">キャンペーン</option>
							<option class="element_ad_group" value="3">広告グループ</option>
							<option class="element_keyword" value="4">キーワード</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>絞り込み</td>
					<td>
						<label class="no_setting_flg" for="no_setting_flg"><input type="checkbox" id="no_setting_flg" name="no_setting_flg" value="1">カテゴリ未設定のみ</label>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>
<div class="clearfix">
	<fieldset class="columns back-col">
		<legend>2．アカウントを選択（<span id="select_account_count"><?= count($account_list); ?></span>/<?= count($account_list); ?>件）</legend>
		<div>
			<p>選択可能アカウントからアカウントを選択してください<font color="red">（必須）</font></p>
			<select id="account_id_list" name="account_id_list[]" multiple="multiple">
				<? $cnt = 0; ?>
				<? foreach($account_list as $account) { ?>
					<option id="account_id_list_<?= $cnt; ?>" value="<?= $account["media_id"] . "//" . $account["account_id"] ?>"><?= "[" . $account["account_id"] . "] " . $account["account_name"] ?></option>
					<? $cnt++; ?>
				<? } ?>
			</select>
			<br>
			<div class="medium secondary btn" id="account_all_select_btn"><a href="#" class="js-href-canceled">全選択</a></div>
			<div class="medium secondary btn" id="account_all_cancel_btn"><a href="#" class="js-href-canceled">全解除</a></div>
			<br><br>
			<div class="info label category-title">アカウント検索(部分一致)</div>
			<br><br>
			<input type="checkbox" name="account_search" value="1" <? if (!empty($account["account_search"])) { echo "checked"; } ?>>除外
			<br>
			<? if (empty($account["account_search_type"])) { ?>
				<? $and_checked = ""; ?>
				<? $or_checked  = "checked"; ?>
			<? } else { ?>
				<? $and_checked = "checked"; ?>
				<? $or_checked  = ""; ?>
			<? } ?>
			<input type="radio" name="account_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="account_search_type" value="0" <?= $or_checked ?>>OR<br>
			<div class="field"><textarea class="input textarea" name="account_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["account_search_list"])) { echo $OUT_data["account_search_list"]; } ?></textarea></div>
			<div class="medium secondary btn" id="account_search_btn"><a href="#" class="js-href-canceled">検索</a></div>
			<div class="medium secondary btn" id="account_search_clear_btn"><a href="#" class="js-href-canceled">クリア</a></div>
		</div>
	</fieldset>
</div>
<div class="clearfix">
	<fieldset class="columns back-col">
		<legend>3．絞り込み</legend>
		<p>選択したカテゴリ設定単位と同じ階層までのコンポーネント名、<br>またはコンポーネントIDにて対象を絞り込むことが出来ます<br>
		（コンポーネントIDでの検索対象：キャンペーン、広告グループ、キーワード）</p>
		<fieldset class="columns back-col-fff campaign_search">
			<div class="info label category-title">キャンペーン</div>
			<div class="campaign_search">
				<input type="checkbox" name="campaign_search_like" value="1" checked>部分一致　
				<input type="checkbox" name="except_campaign_search" value="1">除外<br>
				<input type="radio" name="campaign_search_type" value="1">AND<input type="radio" name="campaign_search_type" value="0" checked>OR<br>
				<div class="field"><textarea class="input textarea campaign_search_text" name="campaign_search_text" rows="3" cols="35" placeholder="キャンペーン名またはIDを入力してください。"></textarea></div>
			</div>
		</fieldset>
		<fieldset class="columns back-col-fff ad_group_search">
			<div class="info label category-title">広告グループ</div>
			<div class="ad_group_search">
				<input type="checkbox" name="ad_group_search_like" value="1" checked>部分一致　
				<input type="checkbox" name="except_ad_group_search" value="1">除外<br>
				<input type="radio" name="ad_group_search_type" value="1">AND<input type="radio" name="ad_group_search_type" value="0" checked>OR<br>
				<div class="field"><textarea class="input textarea ad_group_search_text" name="ad_group_search_text" rows="3" cols="35" placeholder="広告グループ名またはIDを入力してください。"></textarea></div>
			</div>
		</fieldset>
		<fieldset class="columns back-col-fff keyword_search">
			<div class="info label category-title">キーワード</div>
				<div class="keyword_search">
					<input type="checkbox" name="keyword_search_like" value="1" checked>部分一致　
					<input type="checkbox" name="except_keyword_search" value="1">除外<br>
					<input type="radio" name="keyword_search_type" value="1">AND<input type="radio" name="keyword_search_type" value="0" checked>OR<br>
					<div class="field"><textarea class="input textarea keyword_search_text" name="keyword_search_text" rows="3" cols="35" placeholder="キーワードまたはIDを入力してください。"></textarea></div>
				</div>
		</fieldset>
		<fieldset class="columns back-col-fff url_search">
			<div class="info label category-title">リンク先</div>
			<div class="url_search">
				<input type="checkbox" name="url_search_like" value="1" checked>部分一致　
				<input type="checkbox" name="except_url_search" value="1">除外<br>
				<input type="radio" name="url_search_type" value="1">AND<input type="radio" name="url_search_type" value="0" checked>OR<br>
				<div class="field"><textarea class="input textarea url_search_text" name="url_search_text" rows="3" cols="35" placeholder="リンク先URLを入力してください。"></textarea></div>
			</div>
		</fieldset>
		<fieldset class="columns back-col-fff category_search">
			<div class="info label category-title">カテゴリ</div>
			<p>カテゴリ設定されているコンポーネントの階層が出力コンポーネントより深い、<br>且つ、複数カテゴリが設定されている場合、カテゴリ名での検索は無効です</p>
			<div class="category_search">
				<input type="checkbox" name="category_search_like" value="1" checked>部分一致　
				<input type="checkbox" name="except_category_search" value="1">除外<br>
				<input type="radio" name="category_search_type" value="1">AND<input type="radio" name="category_search_type" value="0" checked>OR<br>
				<div class="field"><textarea class="input textarea category_search_text" name="category_search_text" rows="3" cols="35" placeholder="カテゴリ名を入力してください。"></textarea></div>
			</div>
		</fieldset>
	</fieldset>
</div>
<p>
	<div class="medium primary btn js-export-structure ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_DOWNLOAD_BUTTON?>" id="download_category_setting_btn"><a>設定シートをダウンロード</a></div>　
	<div class="medium primary btn js-update-setting ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_VIEW_BUTTON?>"><a href="javascript:viewCategorySettingList('view');">一覧を表示</a></div>　
	<div class="medium primary btn js-clear" id="js-clear"><a>クリア</a></div>
</p>
<br>
<h3 id="page_title">カテゴリ設定をアップロード</h3>
<div class="clearfix">
	<fieldset class="columns back-col">
		<legend>カテゴリジャンルを選択</legend>
		<div>
			<table class="form-table">
				<tr>
					<td>カテゴリジャンル名<font color="red">（必須）</font></td>
					<td>
						<div>
							<select class="update_genre_id" name="update_genre_id">
								<option value="--">--</option>
								<? foreach($genre_list as $genre) { ?>
									<option value="<?=$genre["id"]?>"><?=$genre["category_genre_name"]?>　（<?=$genre["user_name"]?>：<?=$genre["datetime"]?>）</option>
								<?}?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>ファイルを選択<font color="red">（必須）</font></td>
					<td>
						<input type="file" id="upload_file" name="upload_file">
					</td>
				</tr>
			</table>
			<br>
			<input type="hidden" name="upload_history_id" />
			<input type="hidden" name="upload_file_name" />
		</div>
	</fieldset>
</div>
<p>
	<div class="medium primary btn js-upload-structure ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_UPLOAD_BUTTON?>" id="upload_category_setting_btn"><a>設定シートをアップロード</a></div>
</p>
<input type="hidden" name="client_id" value="<?=$client_id?>"/>
<input type="hidden" name="action_type" />
</form>