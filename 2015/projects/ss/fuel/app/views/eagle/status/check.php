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

		<!-- 絞り込み検索 START -->
		<div class="clearfix">
			<fieldset class="columns">
				<legend>絞り込み検索（<? if ($search_flg === "1") { print $search_contents_count . " / "; } ?><?= $DL_contents_count; ?>件）</legend>
				<div class="float">媒体</div>
				<div class="float field">
					<select class="picker" id="search_media" name="search_media">
						<option value="">--</option>
						<option value="1" <? if ($OUT_data["search_media"] === "1") print "selected"; ?>>Yahoo</option>
						<option value="2" <? if ($OUT_data["search_media"] === "2") print "selected"; ?>>Google</option>
						<option value="3" <? if ($OUT_data["search_media"] === "3") print "selected"; ?>>YDN</option>
					</select>
				</div>
				<div class="float">検索対象コンポーネント</div>
				<div class="float field">
					<select class="picker" id="search_component" name="search_component">
						<option value="">--</option>
						<option value="account" <? if ($OUT_data["search_component"] === "account") print "selected"; ?>>アカウント</option>
						<option value="campaign" <? if ($OUT_data["search_component"] === "campaign") print "selected"; ?>>キャンペーン</option>
						<option value="adgroup" <? if ($OUT_data["search_component"] === "adgroup") print "selected"; ?>>広告グループ</option>
						<option value="keyword" <? if ($OUT_data["search_component"] === "keyword") print "selected"; ?>>キーワード</option>
						<option value="ad" <? if ($OUT_data["search_component"] === "ad") print "selected"; ?>>広告</option>
					</select>
				</div>
				<div class="float">ID検索</div>
				<div class="field float"><input class="input text-size" type="text" name="search_id" value="<?= $OUT_data["search_id"]; ?>"placeholder="前方一致検索です。"></div>
				<div class="float">名称検索</div>
				<div class="field float"><input class="input text-size" type="text" name="search_name" value="<?= $OUT_data["search_name"]; ?>"placeholder="前方一致検索です。"></div>
				<div class="float">ステータス</div>
				<div class="float field">
					<select class="picker" id="search_status" name="search_status">
						<option value="">--</option>
						<option value="1" <? if ($OUT_data["search_status"] === "1") print "selected"; ?>>ON</option>
						<option value="0" <? if ($OUT_data["search_status"] === "0") print "selected"; ?>>OFF</option>
					</select>
				</div>
				<div class="medium primary btn float" id="search_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= SEARCH_BTN; ?>">検索</a></div>
			</fieldset>
		</div>
		<!-- 絞り込み検索 END -->

		<!-- 掲載内容 START -->
		<div class="clearfix">
			<div class="float danger label eagle-title">掲載内容一覧</div>
			<div class="float">
				表示件数
				<select class="picker" id="per_page" name="per_page">
					<option value="15" <? if ($OUT_data["per_page"] === "15") print "selected"; ?>>15件</option>
					<option value="50" <? if ($OUT_data["per_page"] === "50") print "selected"; ?>>50件</option>
					<option value="100" <? if ($OUT_data["per_page"] === "100") print "selected"; ?>>100件</option>
					<option value="500" <? if ($OUT_data["per_page"] === "500") print "selected"; ?>>500件</option>
				</select>
			</div>
		</div>
		<table class="report-table table striped">
			<tr class="table-label">
				<td>処理対象</td>
				<td class="table-default">媒体</td>
				<td class="table-default">アカウントID</td>
				<td class="table-default">アカウント名</td>
				<td class="table-default">キャンペーンID</td>
				<td class="table-default">キャンペーン名</td>
				<td class="table-default">ステータス</td>
			<? if ($OUT_data["component"] === "adgroup" || $OUT_data["component"] === "keyword" || $OUT_data["component"] === "ad") { ?>
				<td class="table-default">広告グループID</td>
				<td class="table-default">広告グループ名</td>
				<td class="table-default">ステータス</td>
				<? if ($OUT_data["component"] === "keyword") { ?>
					<td class="table-default">キーワードID</td>
					<td class="table-default">キーワード</td>
					<td class="table-default">ステータス</td>
				<? } elseif ($OUT_data["component"] === "ad") { ?>
					<td class="table-default">広告ID</td>
					<td class="table-default">広告名</td>
					<td class="table-default">タイトル</td>
					<td class="table-default">説明文１</td>
					<td class="table-default">説明文２</td>
					<td class="table-default">ステータス</td>
				<? } ?>
			<? } ?>
			</tr>
			<? if ($search_flg === "1") { ?>
				<? foreach ($search_contents as $search_content) { ?>
					<tr>
						<? foreach ($search_content as $search_column) { ?>
							<td <? if ($search_column === "★") { ?>style="color: red"<? } ?>><?= $search_column; ?></td>
						<? } ?>
					</tr>
				<? } ?>
			<? } else { ?>
				<? foreach ($DL_contents as $DL_content) { ?>
					<tr>
						<? foreach ($DL_content as $DL_column) { ?>
							<td <? if ($DL_column === "★") { ?>style="color: red"<? } ?>><?= $DL_column; ?></td>
						<? } ?>
					</tr>
				<? } ?>
			<? } ?>
		</table>
		<?= Pagination::instance("eagle"); ?>
		<!-- 掲載内容 END -->
		<input type="hidden" name="page">
		<input type="hidden" name="search_flg" value="<?= $OUT_data["search_flg"]; ?>">
		<input type="hidden" name="update_status_flg">
		<input type="hidden" name="component" value="<?= $OUT_data["component"]; ?>">
		<input type="hidden" name="search_onlyid" value="<?= $OUT_data["search_onlyid"]; ?>">
		<input type="hidden" name="ad_search_pattern" value="<?= $OUT_data["ad_search_pattern"]; ?>">
		<input type="hidden" name="campaign_search" value="<?= $OUT_data["campaign_search"]; ?>">
		<input type="hidden" name="campaign_search_like" value="<?= $OUT_data["campaign_search_like"]; ?>">
		<input type="hidden" name="campaign_search_type" value="<?= $OUT_data["campaign_search_type"]; ?>">
		<input type="hidden" name="campaign_search_list" value="<?= $OUT_data["campaign_search_list"]; ?>">
		<input type="hidden" name="campaign_except" value="<?= $OUT_data["campaign_except"]; ?>">
		<input type="hidden" name="campaign_except_like" value="<?= $OUT_data["campaign_except_like"]; ?>">
		<input type="hidden" name="campaign_except_type" value="<?= $OUT_data["campaign_except_type"]; ?>">
		<input type="hidden" name="campaign_except_list" value="<?= $OUT_data["campaign_except_list"]; ?>">
		<input type="hidden" name="adgroup_search" value="<?= $OUT_data["adgroup_search"]; ?>">
		<input type="hidden" name="adgroup_search_like" value="<?= $OUT_data["adgroup_search_like"]; ?>">
		<input type="hidden" name="adgroup_search_type" value="<?= $OUT_data["adgroup_search_type"]; ?>">
		<input type="hidden" name="adgroup_search_list" value="<?= $OUT_data["adgroup_search_list"]; ?>">
		<input type="hidden" name="adgroup_except" value="<?= $OUT_data["adgroup_except"]; ?>">
		<input type="hidden" name="adgroup_except_like" value="<?= $OUT_data["adgroup_except_like"]; ?>">
		<input type="hidden" name="adgroup_except_type" value="<?= $OUT_data["adgroup_except_type"]; ?>">
		<input type="hidden" name="adgroup_except_list" value="<?= $OUT_data["adgroup_except_list"]; ?>">
		<input type="hidden" name="keyword_search" value="<?= $OUT_data["keyword_search"]; ?>">
		<input type="hidden" name="keyword_search_like" value="<?= $OUT_data["keyword_search_like"]; ?>">
		<input type="hidden" name="keyword_search_type" value="<?= $OUT_data["keyword_search_type"]; ?>">
		<input type="hidden" name="keyword_search_list" value="<?= $OUT_data["keyword_search_list"]; ?>">
		<input type="hidden" name="keyword_except" value="<?= $OUT_data["keyword_except"]; ?>">
		<input type="hidden" name="keyword_except_like" value="<?= $OUT_data["keyword_except_like"]; ?>">
		<input type="hidden" name="keyword_except_type" value="<?= $OUT_data["keyword_except_type"]; ?>">
		<input type="hidden" name="keyword_except_list" value="<?= $OUT_data["keyword_except_list"]; ?>">
		<input type="hidden" name="ad_search" value="<?= $OUT_data["ad_search"]; ?>">
		<input type="hidden" name="ad_search_like" value="<?= $OUT_data["ad_search_like"]; ?>">
		<input type="hidden" name="ad_search_type" value="<?= $OUT_data["ad_search_type"]; ?>">
		<input type="hidden" name="ad_search_list" value="<?= $OUT_data["ad_search_list"]; ?>">
		<input type="hidden" name="ad_except" value="<?= $OUT_data["ad_except"]; ?>">
		<input type="hidden" name="ad_except_like" value="<?= $OUT_data["ad_except_like"]; ?>">
		<input type="hidden" name="ad_except_type" value="<?= $OUT_data["ad_except_type"]; ?>">
		<input type="hidden" name="ad_except_list" value="<?= $OUT_data["ad_except_list"]; ?>">

		<br>
		<div class="medium primary btn" id="update_status_on_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= UPDATE_STATUS_ON_BTN; ?>">ON</a></div>
		<div class="medium primary btn" id="update_status_off_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= UPDATE_STATUS_OFF_BTN; ?>">OFF</a></div>
		<div class="medium primary btn" id="return_unget_campaign_structure_btn"><a href="#" class="js-href-canceled">戻る</a></div>
	<? } ?>
</form>

<?= Asset::js('eagle/status/check.js') ?>