<div id="client_class_<?=$item['id']?>" class="edit_cassette client-row clearfix" data-item-id="<?=$item['id']?>">

	<ul class="list-inline" id="client_class_<?=$item['id']?>_pv">
		<li class="name">
			<span class="label label-success"><?=$item['sort']?></span> <?=$item['name']?>
		</li>

		<li class="pull-right">
			<div class="controls">
					<span class="label label-default">キーワード数</span> <?=$count?>

				<? if ($custom_flg) { ?>
					<div class="btn btn-xs btn-default keyword-btn">
						<a href="<?=$keyword_url?><?=$item['id']?>" target="_blank">キーワード設定</a>
					</div>
				<? } ?>

				<? if ($admin_flg) { ?>
					<button type="button" class="btn btn-xs btn-danger js-delete-btn">
						<i class="glyphicon glyphicon-remove"></i>
					</button>
				<? } ?>
			</div>
		</li>
	</ul>

	<div id="client_class_<?=$item['id']?>_pt" class="field edit-client hide">
		<input type="text" class="form-control input-sm form-inline input-sort-num" id="client_class_<?=$item['id']?>_sort" value="<?=$item['sort']?>" placeholder="並び順">
		<input type="text" class="form-control input-sm form-inline input-client-name" id="client_class_<?=$item['id']?>_name" value="<?=$item['name']?>" placeholder="クライアント詳細名">
		<div class="btn btn-xs btn-primary js-update-btn">
			反映
		</div>
	</div>

</div>
