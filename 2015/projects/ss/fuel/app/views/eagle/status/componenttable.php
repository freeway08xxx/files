<!-- キャンペーン検索 START -->
<input type="hidden" name="campaign_search" value="<?= $OUT_data["campaign_search"] ?>">
<input type="hidden" name="campaign_search_like" value="<?= $OUT_data["campaign_search_like"] ?>">
<input type="hidden" name="campaign_search_type" value="<?= $OUT_data["campaign_search_type"] ?>">
<input type="hidden" name="campaign_search_list" value="<?= $OUT_data["campaign_search_list"] ?>">
<input type="hidden" name="campaign_except" value="<?= $OUT_data["campaign_except"] ?>">
<input type="hidden" name="campaign_except_like" value="<?= $OUT_data["campaign_except_like"] ?>">
<input type="hidden" name="campaign_except_type" value="<?= $OUT_data["campaign_except_type"] ?>">
<input type="hidden" name="campaign_except_list" value="<?= $OUT_data["campaign_except_list"] ?>">
<!-- キャンペーン検索 END -->

<div class="clearfix">
	<fieldset class="columns">
		<legend>処理対象コンポーネント選択</legend>
		<div class="field">
			<select class="picker" id="component" name="component">
				<option value="campaign" <? $OUT_data["component"] === "campaign" ? print "selected" : print ""; ?>>キャンペーン</option>
				<option value="adgroup" <? $OUT_data["component"] === "adgroup" ? print "selected" : print ""; ?>>広告グループ</option>
				<option value="keyword" <? $OUT_data["component"] === "keyword" ? print "selected" : print ""; ?>>キーワード</option>
				<option value="ad" <? $OUT_data["component"] === "ad" ? print "selected" : print ""; ?>>広告</option>
			</select>
		</div>
		<input type="checkbox" name="search_onlyid" value="1" <? if (!empty($OUT_data["search_onlyid"])) { echo "checked"; } ?>>ID検索のみ<br>
		<div class="clearfix" id="ad_search_pattern">
		※検索対象<br>
		<? $checked_0 = ""; $checked_1 = ""; $checked_2 = ""; $checked_3 = ""; $checked_4 = ""; ?>
		<? if (empty($OUT_data["ad_search_pattern"])) { ?>
			<? $checked_0 = "checked"; ?>
		<? } elseif ($OUT_data["ad_search_pattern"] === "1") { ?>
			<? $checked_1 = "checked"; ?>
		<? } elseif ($OUT_data["ad_search_pattern"] === "2") { ?>
			<? $checked_2 = "checked"; ?>
		<? } elseif ($OUT_data["ad_search_pattern"] === "3") { ?>
			<? $checked_3 = "checked"; ?>
		<? } elseif ($OUT_data["ad_search_pattern"] === "4") { ?>
			<? $checked_4 = "checked"; ?>
		<? } ?>
			<input type="radio" name="ad_search_pattern" value="0" <?= $checked_0 ?>>広告ID＋広告名＋タイトル＋説明文１＋説明文２<br>
			<input type="radio" name="ad_search_pattern" value="1" <?= $checked_1 ?>>広告ID＋タイトル＋説明文１＋説明文２<br>
			<input type="radio" name="ad_search_pattern" value="2" <?= $checked_2 ?>>広告ID＋広告名<br>
			<input type="radio" name="ad_search_pattern" value="3" <?= $checked_3 ?>>広告ID＋タイトル<br>
			<input type="radio" name="ad_search_pattern" value="4" <?= $checked_4 ?>>広告ID＋説明文１＋説明文２<br>
		</div>
	</fieldset>
</div>

<div class="clearfix" id="campaign_search">
	<fieldset class="columns">
		<legend>キャンペーン検索</legend>
		<input type="checkbox" name="campaign_search_like" value="1" <? if (!empty($OUT_data["campaign_search_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["campaign_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="campaign_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="campaign_search_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="campaign_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["campaign_search_list"])) { echo $OUT_data["campaign_search_list"]; } ?></textarea>
	</fieldset>
	<fieldset class="columns">
		<legend>キャンペーン除外検索</legend>
		<input type="checkbox" name="campaign_except_like" value="1" <? if (!empty($OUT_data["campaign_except_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["campaign_except_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="campaign_except_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="campaign_except_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="campaign_except_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["campaign_except_list"])) { echo $OUT_data["campaign_except_list"]; } ?></textarea>
	</fieldset>
</div>
<div class="clearfix" id="adgroup_search">
	<fieldset class="columns">
		<legend>広告グループ検索</legend>
		<input type="hidden" name="adgroup_search" value="0">
		<input type="checkbox" name="adgroup_search_like" value="1" <? if (!empty($OUT_data["adgroup_search_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["adgroup_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="adgroup_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="adgroup_search_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="adgroup_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["adgroup_search_list"])) { echo $OUT_data["adgroup_search_list"]; } ?></textarea>
	</fieldset>
	<fieldset class="columns">
		<legend>広告グループ除外検索</legend>
		<input type="hidden" name="adgroup_except" value="1">
		<input type="checkbox" name="adgroup_except_like" value="1" <? if (!empty($OUT_data["adgroup_except_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["adgroup_except_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="adgroup_except_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="adgroup_except_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="adgroup_except_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["adgroup_except_list"])) { echo $OUT_data["adgroup_except_list"]; } ?></textarea>
	</fieldset>
</div>
<div class="clearfix" id="keyword_search">
	<fieldset class="columns">
		<legend>キーワード検索</legend>
		<input type="hidden" name="keyword_search" value="0">
		<input type="checkbox" name="keyword_search_like" value="1" <? if (!empty($OUT_data["keyword_search_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["keyword_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="keyword_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="keyword_search_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="keyword_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["keyword_search_list"])) { echo $OUT_data["keyword_search_list"]; } ?></textarea>
	</fieldset>
	<fieldset class="columns">
		<legend>キーワード除外検索</legend>
		<input type="hidden" name="keyword_except" value="1">
		<input type="checkbox" name="keyword_except_like" value="1" <? if (!empty($OUT_data["keyword_except_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["keyword_except_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="keyword_except_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="keyword_except_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="keyword_except_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["keyword_except_list"])) { echo $OUT_data["keyword_except_list"]; } ?></textarea>
	</fieldset>
</div>
<div class="clearfix" id="ad_search">
	<fieldset class="columns">
		<legend>広告検索</legend>
		<input type="hidden" name="ad_search" value="0">
		<input type="checkbox" name="ad_search_like" value="1" <? if (!empty($OUT_data["ad_search_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["ad_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="ad_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="ad_search_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="ad_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["ad_search_list"])) { echo $OUT_data["ad_search_list"]; } ?></textarea>
	</fieldset>
	<fieldset class="columns">
		<legend>広告除外検索</legend>
		<input type="hidden" name="ad_except" value="1">
		<input type="checkbox" name="ad_except_like" value="1" <? if (!empty($OUT_data["ad_except_like"])) { echo "checked"; } ?>>部分一致<br>
		<? if (empty($OUT_data["ad_except_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="ad_except_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="ad_except_type" value="0" <?= $or_checked ?>>OR<br>
		<textarea name="ad_except_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["ad_except_list"])) { echo $OUT_data["ad_except_list"]; } ?></textarea>
	</fieldset>
</div>
<div class="medium primary btn" id="download_update_status_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= DOWNLOAD_UPDATE_STATUS_BTN; ?>">処理対象一覧DL</a></div>
<div class="medium primary btn" id="check_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= CHECK_BTN; ?>">確認</a></div>
<div class="medium primary btn" id="return_btn"><a href="#" class="js-href-canceled">戻る</a></div>
