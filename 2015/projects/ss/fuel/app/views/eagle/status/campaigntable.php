<div class="clearfix">
	<fieldset class="columns">
		<legend>キャンペーン検索</legend>
		<input type="hidden" name="campaign_search" value="0">
		<input type="checkbox" name="campaign_search_like" value="1" <? if (!empty($OUT_data["campaign_search_like"])) { echo "checked"; } ?>>部分一致<br>
        <? if (empty($OUT_data["campaign_search_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="campaign_search_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="campaign_search_type" value="0" <?= $or_checked ?>>OR<br>
		<div class="field"><textarea class="input textarea" name="campaign_search_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["campaign_search_list"])) { echo $OUT_data["campaign_search_list"]; } ?></textarea></div>
	</fieldset>
	<fieldset class="columns">
		<legend>キャンペーン除外検索</legend>
		<input type="hidden" name="campaign_except" value="1">
		<input type="checkbox" name="campaign_except_like" value="1" <? if (!empty($OUT_data["campaign_except_like"])) { echo "checked"; } ?>>部分一致<br>
        <? if (empty($OUT_data["campaign_except_type"])) { ?>
			<? $and_checked = ""; ?>
			<? $or_checked  = "checked"; ?>
		<? } else { ?>
			<? $and_checked = "checked"; ?>
			<? $or_checked  = ""; ?>
		<? } ?>
		<input type="radio" name="campaign_except_type" value="1" <?= $and_checked ?>>AND<input type="radio" name="campaign_except_type" value="0" <?= $or_checked ?>>OR<br>
		<div class="field"><textarea class="input textarea" name="campaign_except_list" rows="5" cols="50" placeholder="検索対象文字列を入力してください。"><? if (!empty($OUT_data["campaign_except_list"])) { echo $OUT_data["campaign_except_list"]; } ?></textarea></div>
	</fieldset>
</div>
<!--div class="medium primary btn" id="download_all_campaign_btn"><a href="#" class="js-href-canceled">全CPN一覧DL</a></div-->
<div class="medium primary btn" id="download_campaign_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= DOWNLOAD_CAMPAIGN_BTN; ?>">対象CPN一覧DL</a></div>
<? if (empty($OUT_data["refresh_campaign_flg"])) { ?>
	<div class="medium primary btn" id="refresh_campaign_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= REFRESH_CAMPAIGN_BTN; ?>">最新CPN取得</a></div>
<? } else { ?>
	<div class="medium primary btn" id="get_campaign_structure_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= GET_CAMPAIGN_STRUCTURE_BTN; ?>">掲載取得</a></div>
	<? if (!empty($OUT_data["chk_structure_flg"])) { ?>
		<div class="medium primary btn" id="unget_campaign_structure_btn"><a href="#" class="js-href-canceled ttip" data-tooltip="<?= UNGET_CAMPAIGN_STRUCTURE_BTN; ?>">掲載取得せず進む</a></div>
	<? } ?>
	<div class="medium primary btn" id="return_btn"><a href="#" class="js-href-canceled">戻る</a></div>
<? } ?>
