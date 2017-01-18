<?
	if ($mode == 'industry') {
		$mode_name = '業種';
	}
	if ($mode == 'client') {
		$mode_name = 'クライアント';
	}
?>

<div class="register well">
	<p>
		<span class="label label-default"><?=$mode_name?></span>
	</p>

	<p>
		<span class="form-label form-inline">
			<?=$mode_name?>名
		</span>
		<span class="form-inline"><?=$target['parent_name']?></span>
		<br>
		<span class="form-label form-inline">
			<?=$mode_name?>詳細名
		</span>
		<span class="form-inline"><?=$target['name']?></span>
	</p>

	<textarea class="form-control" name="keyword" class="keyword-input" placeholder="登録するキーワード(改行で複数登録)"></textarea>

	<div class="btn-area">
		<div class="btn btn-sm btn-primary" id="entry_btn">登録する</div>
	</div>
</div>

