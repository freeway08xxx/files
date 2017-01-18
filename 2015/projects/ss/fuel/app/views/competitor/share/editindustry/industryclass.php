<div id="industry_class_<?=$item['id']?>" class="edit_cassette industry-row clearfix">

	<div id="industry_class_<?=$item['id']?>_pv"  class="name pull_left">
		<span class="light badge"><?=$item['sort']?></span> <?=$item['name']?>
	</div>

	<div id="industry_class_<?=$item['id']?>_pvb" class="controls pull_right">
			<span class="light rounded label">キーワード数</span> <?=$count?>

		<? if ($custom_flg) { ?>
			<div class="small secondary btn keyword-btn"><a href="<?=$keyword_url?><?=$item['id']?>" target="_blank">キーワード設定</a></div>
			<i class="icon-cancel-squared cancel-btn js-delete-btn"></i>
		<? } ?>
	</div>

	<div id="industry_class_<?=$item['id']?>_pt" class="field hide">
		<input type="text" class="xnarrow text input" id="industry_class_<?=$item['id']?>_sort" value="<?=$item['sort']?>" placeholder="並び順">
		<input type="text" class="wide text input" id="industry_class_<?=$item['id']?>_name" value="<?=$item['name']?>" placeholder="業種詳細名">
		<div class="small primary btn js-update-btn pull_right"><a href="#">反映</a></div>
	</div>
</div>



