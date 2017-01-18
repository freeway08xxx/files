<? $item_status = ($current_flg) ? "edit_current" : "edit_cassette"; ?>

<div id="industry_<?=$item['id']?>" class="<?= $item_status ?> industry-row clearfix" data-item-id="<?=$item['id']?>">

	<div id="industry_<?=$item['id']?>_pv" class="name pull_left">
		<span class="light badge"><?=$item['sort']?></span> <?=$item['name']?>
	</div>

	<div id="industry_<?=$item['id']?>_pvb" class="controls pull_right">
		<? if (!$current_flg) { ?>
			<div class="small secondary btn"><a href="<?=$url?><?=$item['id']?>">詳細</a></div>
		<? } ?>

		<? if ($custom_flg) { ?>
			<div class="small default btn js-edit-user"><a href="#" class="switch" gumby-trigger=".modal">アクセスユーザー</a></div>
		<? } ?>

		<? if ($admin_flg) { ?>
			<i class="icon-cancel-squared cancel-btn js-delete-btn"></i>
		<? } ?>
	</div>

	<div id="industry_<?=$item['id']?>_pt" class="field hide">
		<input type="text" class="xnarrow text input" id="industry_<?=$item['id']?>_sort" value="<?=$item['sort']?>" placeholder="並び順">
		<input type="text" class="wide text input" id="industry_<?=$item['id']?>_name" value="<?=$item['name']?>" placeholder="業種名">
		<div class="small primary btn js-update-btn pull_right"><a href="#">反映</a></div>
	</div>

</div>
