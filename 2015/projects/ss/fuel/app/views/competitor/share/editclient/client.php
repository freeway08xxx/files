<? $item_status = ($current_flg) ? "edit_current" : "edit_cassette"; ?>

<div id="client_<?=$item['id']?>" class="<?= $item_status ?> client-row clearfix" data-item-id="<?=$item['id']?>">

	<div class="row" id="client_<?=$item['id']?>_pv">
		<div class="name">
			<span class="label label-success"><?=$item['sort']?></span> <?=$item['name']?>
		</div>

		<div class="controls col-xs-6 text-right">
			<? if (!$current_flg) { ?>
				<div class="btn btn-xs btn-default">
					<a href="<?=$url?><?=$item['id']?>">詳細</a>
				</div>
			<? } ?>

			<? if ($custom_flg) { ?>
				<div class="btn btn-xs btn-default js-edit-user">
					アクセスユーザー
				</div>
			<? } ?>

			<? if ($admin_flg) { ?>
				<button type="button" class="btn btn-xs btn-danger js-delete-btn">
					<i class="glyphicon glyphicon-remove"></i>
				</button>
			<? } ?>
		</div>
	</div>

	<div id="client_<?=$item['id']?>_pt" class="field edit-client hide">
		<input type="text" class="form-control input-sm form-inline input-sort-num" id="client_<?=$item['id']?>_sort" value="<?=$item['sort']?>" placeholder="並び順">
		<input type="text" class="form-control input-sm form-inline input-client-name" id="client_<?=$item['id']?>_name" value="<?=$item['name']?>" placeholder="クライアント名">
		<div class="btn btn-xs btn-primary js-update-btn">
			反映
		</div>
	</div>

</div>
