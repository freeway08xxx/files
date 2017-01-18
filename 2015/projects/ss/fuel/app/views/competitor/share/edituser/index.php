<?
	$cat_label = "";
	if($mode == 'client'){
		$cat_label = "クライアント";
	}
	if($mode == 'industry'){
		$cat_label = "業種";
	}
?>

<div class="edit-user">
	<span class="label label-default"> <?= $cat_label ?></span><br>
	<h5><?= $name ?> の アクセスユーザー</h5>

	<div id="user_base" data-id="<?=$id?>" class="user-base">
		<div id="user_edit" class="register field clearfix well well-sm">
			<input type="text" class="form-control input-sm form-inline user-name" id="user_name" value="" placeholder="user_mailaddress@cyberagent.co.jp">
			<div class="btn btn-xs btn-primary pull-right js-insert-user-btn">
				新規登録
			</div>
		</div>

		<div class="user-table-wrap">
			<table class="table table-hover table-striped">
				<tbody>
					<?= $user_list ?>
				</tbody>
			</table>
		</div>

	</div>
</div>

