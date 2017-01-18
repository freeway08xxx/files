<? if ($keyword_list) { ?>
<div class="list-group">
	<div class="list-group-item">
		<h5>登録済みのキーワード</span> <span class="badge"> 全 <?= count($keyword_list) ?> 件</h5>

		<div class="controls well well-sm">
			<a href="#" class="btn btn-xs btn-default right_10px toggle js-href-canceled" id="check_all">
				全選択
			</a>

			<a href="#" class="btn btn-xs btn-danger js-href-canceled" id="delete_btn">選択したキーワードを削除</a>
		</div>

		<div class="row keyword-list">
			<? foreach($keyword_list as $keyword) { ?>
				<div class="col-xs-4 col-sm-3 col-lg-2">
					<div class="checkbox">
						<label for="check_<?= $keyword['class_id'] ?>">
							<input class="" name="check_keyword[]" id="check_<?= $keyword['class_id'] ?>" value="<?= $keyword['class_id'] ?>" type="checkbox">
							<span></span> <?= $keyword["keyword"] ?>
						</label>
					</div>
				</div>
			<? } ?>
		</div>
	</div>
</div>

<? } else {?>
	<div class="alert alert-danger">キーワードは登録されていません。</div>
<? } ?>
