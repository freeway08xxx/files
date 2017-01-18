<div class="row">
	<div class="col-sm-6 list-group">
		<div class="list-group-item">
			<h5 class="">クライアント一覧</h5>

			<? if ($admin_flg) { ?>
				<div id="client_0_edit" class="register well well-sm clearfix">
					<input type="text" class="form-control input-sm form-inline input-sort-num" id="client__sort" value="" placeholder="並び順">
					<input type="text" class="form-control input-sm form-inline input-client-name" id="client__name" value="" placeholder="クライアント名">
					<div class="btn btn-xs btn-info btn-insert js-insert-btn">
						新規登録
					</div>
				</div>
			<? } ?>

			<div id="client_base">
				<?= $client_list ?>
			</div>
		</div>
	</div>

	<div class="col-sm-6 list-group">
		<div class="list-group-item">
			<? if ($client_id) { ?>
				<h5><span class="label label-default">詳細</span> <?=$current_name?></h5>

				<? if ($admin_flg) { ?>
					<div id="client_class_0_edit" class="register well well-sm clearfix">
						<input type="hidden" id="client_class" value="<?=$client_id?>">

						<input type="text" class="form-control input-sm form-inline input-sort-num" id="client_class__sort" value="" placeholder="並び順">
						<input type="text" class="form-control input-sm form-inline input-client-name" id="client_class__name" value="" placeholder="クライアント詳細名">

						<div class="btn btn-xs btn-info btn-insert js-insert-btn">
							新規登録
						</div>
					</div>
				<? } ?>

				<div id="client_class_base">
					<?= $client_class_list ?>
				</div>
			<? } ?>

		</div>
	</div>

</div>

<!-- Modal Window -->
<div class="modal fade" id="access_user" tabindex="-1" role="dialog" aria-labelledby="access_user_label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">アクセスユーザーを編集</h4>
			</div>
			<div class="modal-body" id="popup_base">
				<p>form body...</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
					キャンセル
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>