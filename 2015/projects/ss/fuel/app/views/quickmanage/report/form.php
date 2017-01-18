<form name="reportform" id="report_form" method="POST" ng-controller="ReportFormCtrl" ng-scrollable>

	<!-- レポート形式設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポート形式を設定</h4>
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-6">
					<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_TEMPLATE_REPORT_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">レポート種別</h5>

					<div class="btn-group report-type">
						<? foreach ($report_type_list as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.report_type" btn-radio="'<?= $key; ?>'"
							 ng-click="params.termdate.method.reset()" ng-required="!params.report_type"><?= $value; ?></label>
						<? } ?>
					</div>
					<input type="hidden" id="report_type" name="report_type" value="{{params.report_type}}" />
				</div>

				<div class="col-sm-5 col-sm-offset-1">
					<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_FORM_POSISION_ID; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">担当種別</h5>

					<div class="btn-group position-id">
						<? foreach ($position_id_list as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.position_id" btn-radio="'<?= $key; ?>'" ng-required="!params.position_id"><?= $value; ?></label>
						<? } ?>
					</div>
					<input type="hidden" id="position_id" name="position_id" value="{{params.position_id}}" />
				</div>
			</div>
		</div>

		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-3">
					<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_TEMPLATE_SUMMARY_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">サマリ種別</h5>

					<select class="form-control input-large" name="summary_type" ng-model="params.summary_type" ng-required="true">
						<? foreach ($summary_type_list as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>

				<div class="col-sm-9">
					<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_FORM_SUMMARY_OPTION; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">サマリオプション</h5>

					<? foreach ($summary_option_list as $key => $value) { ?>
						<label class="checkbox-inline option" for="<?= $key; ?>" ng-show="params.summary_option.<?= $key; ?>.visible">
							<input type="checkbox" id="<?= $key; ?>" name="<?= $key; ?>" ng-model="params.summary_option.<?= $key; ?>.value" value="true"> <?= $value; ?>
						</label>
					<? } ?>
				</div>
			</div>
		</div>

		<div class="controls list-group-item">
			<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_FORM_OPTION; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">オプション </h5>

			<? foreach ($option_list as $key => $value) { ?>
				<label class="checkbox-inline option" for="<?= $key; ?>" ng-show="params.option.<?= $key; ?>.visible">
					<input type="checkbox" id="<?= $key; ?>" name="<?= $key; ?>" ng-model="params.option.<?= $key; ?>.value" value="true"> <?= $value; ?>
				</label>
			<? } ?>
		</div>
	</div>

	<!-- レポート集計期間設定 -->
	<div class="block-title clearfix">
		<h4 class="title">集計期間を設定</h4>
	</div>

	<div class="form-block list-group">
		<div ss-termdate="termdate_config" ng-model="params.termdate"></div>
	</div>

	<!-- レポートフィルタリング設定 -->
	<div class="block-title clearfix">
		<h4 class="title">フィルタリング設定</h4>
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-3">
					<h5 class="control-label">フィルタ項目</h5>
					<select class="form-control form-select" name="filter_selected" ng-model="params.filter_selected" ng-change="resetFilter()">
						<? foreach ($GLOBALS["filter_option_list"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>

				<div class="col-sm-9">
					<h5 class="control-label">フィルタ設定</h5>

					<div ng-show="params.filter_selected === 'customer_class'">
						<? foreach ($GLOBALS["customer_class_list"] as $key => $value) { ?>
							<label class="checkbox-inline option" for="customer_class_list_<?= $key; ?>">
								<input type="checkbox" id="customer_class_list_<?= $key?>" name="customer_class_list[<?= $key; ?>]" ng-model="params.filter.customer_class_list.<?= $key; ?>" value="<?= $key; ?>"> <?= $value; ?>
							</label>
						<? } ?>
					</div>

					<div ng-show="params.filter_selected === 'business_type'">
						<select multiple class="form-control filter-select" name="business_type_list[]" ng-model="params.filter.business_type_list">
							<option value="">-</option>
							<? foreach ($business_type_list as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
							<? } ?>
						</select>
					</div>

					<div ng-show="params.filter_selected === 'media'">
						<? foreach ($GLOBALS["media_id_list"] as $key => $value) { ?>
							<label class="checkbox-inline option" for="media_list_<?= $value; ?>">
								<input type="checkbox" id="media_list_<?= $value?>" name="media_list[<?= $key; ?>]" ng-model="params.filter.media_list.<?= $key; ?>" value="<?= $key; ?>"> <?= $value; ?>
							</label>
						<? } ?>
					</div>

					<div ng-show="params.filter_selected === 'product'">
						<? foreach ($GLOBALS["product_list"] as $key => $value) { ?>
							<label class="checkbox-inline option" for="product_list_<?= $value; ?>">
								<input type="checkbox" id="product_list_<?= $value?>" name="product_list[<?= $key; ?>]" ng-model="params.filter.product_list.<?= $value; ?>" value="<?= $key; ?>"> <?= $value; ?>
							</label>
						<? } ?>
					</div>

					<div ng-show="params.filter_selected === 'device'">
						<? foreach ($GLOBALS["device_list"] as $key => $value) { ?>
							<label class="checkbox-inline option" for="device_list_<?= $value; ?>">
								<input type="checkbox" id="device_list_<?= $value?>" name="device_list[<?= $key; ?>]" ng-model="params.filter.device_list.<?= $value; ?>" value="<?= $key; ?>"> <?= $value; ?>
							</label>
						<? } ?>
					</div>

					<div ng-show="params.filter_selected === 'bureau_id'">
						<select multiple class="form-control filter-select" name="bureau_id_list[]" ng-model="params.filter.bureau_id_list">
							<option value="">-</option>
							<? foreach ($bureau_list as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
							<? } ?>
						</select>
					</div>

					<div ng-show="params.filter_selected === 'user_name'">
						<textarea class="form-control filter-textarea" type="text" name="search_user_name" ng-model="params.filter.search_user_name" value="" placeholder="担当名　※改行で複数入力できます"></textarea>
					</div>

					<div ng-show="params.filter_selected === 'client_name'">
						<textarea class="form-control filter-textarea" type="text" name="search_client_name" ng-model="params.filter.search_client_name" value="" placeholder="会社/事業部名　※改行で複数入力できます"></textarea>
					</div>

					<div ng-show="params.filter_selected === 'account_name'">
						<textarea class="form-control filter-textarea" type="text" name="search_account_name" ng-model="params.filter.search_account_name" value="" placeholder="アカウント名　※改行で複数入力できます"></textarea>
					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- レポート出力設定 -->
	<div class="bottom_10px">
		<div class="btn-group export-type">
			<label class="btn btn-sm btn-default" ng-model="params.export_type" btn-radio="'display'"><i class="glyphicon glyphicon-list-alt"></i> レポートを表示</label>
			<label class="btn btn-sm btn-default" ng-model="params.export_type" btn-radio="'export'"><i class="glyphicon glyphicon-save"></i> レポートを作成</label>
		</div>
		<input type="hidden" id="export_type" name="export_type" value="{{params.export_type}}" />
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item" ng-show="params.export_type=='export'">
			<a class="pull-right" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_HISTORY_NAME; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">レポート名を入力してください</h5>
			<div class="input-group bottom_10px">
				<input class="form-control" type="text" name="report_name" ng-model="params.report_name" value="" placeholder="レポート名（例：2014年サンプルクライアント日別推移レポート）">
				<span class="input-group-addon">.xls</span>
			</div>

			<div class="">
				<label for="send_mail_flg"><input type="checkbox" id="send_mail_flg" name="send_mail_flg" ng-model="params.send_mail_flg" value="1"> レポート作成が完了したら、通知メールを受け取る</label>
				<a class="" tooltip-placement="left" tooltip-html-unsafe="<?= EXPLAIN_FORM_SEND_MAIL; ?>">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
			</div>
		</div>
	</div>

	<div class="validate-msg alert alert-warning" ng-show="!isValidForm()">
		<p class="" ng-bind-html="msg"></p>
	</div>

	<div class="btn-area clearfix">
		<button type="button" class="btn btn-primary pull-left" ng-click="submit()" ng-class="{disabled: is_processing}" ng-disabled="reportform.$invalid">レポートを作成する</button>

		<div class="loading pull-left" ng-show="is_processing">
			<progressbar class="progress-striped active" type="success" animate="false" value="100"></progressbar>
			作成しています...
		</div>

		<button type="button" class="btn btn-sm btn-default pull-right" ng-click="save()" ng-class="{disabled: is_processing}" ng-show="params.export_type=='export'">テンプレートとして保存</button>
	</div>
</form>


<script type="text/ng-template" id="tmpl_editTemplate">
	<div class="modal-header">
		<button class="pull-right btn btn-link close-icon" ng-click="$dismiss()"><span class="glyphicon glyphicon-remove"></span></button>
		<h4>テンプレート編集</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="control-label">テンプレート名</label>
			<input class="form-control" type="text" name="template_name" ng-model="editing.template_name" value="">
		</div>

		<div class="form-group">
			<label class="control-label">レポート種別</label>
			<div class="btn-group report-type">
				<? foreach ($report_type_list as $key => $value) { ?>
					<label class="btn btn-sm btn-default" ng-model="editing.report_type" btn-radio="'<?= $key; ?>'"><?= $value; ?></label>
				<? } ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">担当種別</label>
			<div class="btn-group position-id">
				<? foreach ($position_id_list as $key => $value) { ?>
					<label class="btn btn-sm btn-default" ng-model="editing.position_id" btn-radio="'<?= $key; ?>'"><?= $value; ?></label>
				<? } ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">サマリ種別</label>
			<div class="row">
				<div class="col-xs-6">
					<select class="form-control" name="summary_type" ng-model="editing.summary_type">
						<? foreach ($summary_type_list as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">サマリオプション</label>

			<? foreach ($summary_option_list as $key => $value) { ?>
				<label class="checkbox-inline option" for="editing_<?= $key; ?>" ng-show="config.summary_option.<?= $key; ?>.visible">
					<input type="checkbox" id="editing_<?= $key; ?>" name="editing_<?= $key; ?>" ng-model="editing.summary_option.<?= $key; ?>" value="checked"> <?= $value; ?>
				</label>
			<? } ?>
		</div>

		<div class="form-group">
			<label class="control-label">オプション</label>

			<? foreach ($option_list as $key => $value) { ?>
				<label class="checkbox-inline option" for="editing_<?= $key; ?>" ng-show="config.option.<?= $key; ?>.visible">
					<input type="checkbox" id="editing_<?= $key; ?>" name="editing_<?= $key; ?>" ng-model="editing.option.<?= $key; ?>" value="checked"> <?= $value; ?>
				</label>
			<? } ?>
		</div>

		<div class="form-group">
			<div class="btn-group" ng-show="editing.report_type!=='term_compare'">
				<label class="control-label">集計期間</label>
				<? foreach ($GLOBALS["report_term_list"]["summary"] as $key => $value) { ?>
					<button type="button" class="btn btn-sm btn-default" ng-model="editing.report_term" btn-radio="'<?= $key; ?>'"><?= $value; ?></button>
				<? } ?>
			</div>

			<div class="row" ng-show="editing.report_type==='term_compare'">
				<div class="col-xs-6">
					<label class="control-label">集計期間</label>
					<select class="form-control" name="term_compare_preset" ng-model="editing.report_term">
						<? foreach ($GLOBALS["report_term_list"]["term_compare"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>

				<div class="col-xs-2">
					<label class="control-label">期間数</label>
					<select class="form-control" name="term_count" ng-model="editing.term_count">
						<? for ($i = 1; $i <= REPORT_TERM_COUNT_MAX; $i++) { ?>
							<option value="<?= $i ?>"><?= $i ?></option>
						<? } ?>
					</select>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">レポート名</label>

			<div class="input-group bottom_10px">
				<input class="form-control" type="text" name="report_name" ng-model="editing.report_name" value="" placeholder="レポート名（例：2014年サンプルクライアント日別推移レポート）">
				<span class="input-group-addon">.xls</span>
			</div>

			<div class="">
				<label for="editing_send_mail_flg"><input type="checkbox" id="editing_send_mail_flg" name="editing_send_mail_flg" ng-model="editing.send_mail_flg" value="1"> レポート作成が完了したら、通知メールを受け取る</label>
			</div>
		</div>

	</div>
	<div class="modal-footer">
		<div class="in-process pull-left" ng-show="is_processing">
			<progressbar class="progress-striped active pull-left" type="success" animate="false" value="100"></progressbar>
			更新しています...
		</div>
		<button class="btn btn-primary" ng-click="update()" ng-class="{disabled: is_processing}">編集内容を保存する</button>
		<button class="btn btn-default" ng-click="$dismiss('cancel')">キャンセル</button>
	</div>
</script>

<script type="text/ng-template" id="tmpl_saveTemplate">
	<div class="modal-header">
		<button class="pull-right btn btn-link close-icon" ng-click="$dismiss()"><span class="glyphicon glyphicon-remove"></span></button>
		<h4>テンプレートとして保存</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="control-label">テンプレート名 <span class="label label-warning">必須</span></label>
			<input type="text" class="form-control" ng-model="params.template_name" placeholder="（例）期間比較テンプレート"></input>
		</div>
		<div class="form-group">
			<label class="control-label">メモ</label>
			<textarea type="text" class="form-control" rows="3" ng-model="params.template_memo" placeholder="（例）テンプレートの説明文"></textarea>
		</div>
		<div class="form-group" ng-show="!params.report_name">
			<label class="control-label">レポート名 <span class="label label-warning">必須</span></label>
			<div class="input-group bottom_10px">
				<input class="form-control" type="text" name="report_name" ng-model="ins_param.report_name" value="" placeholder="レポート名（例：2014年サンプルクライアント日別推移レポート）">
				<span class="input-group-addon">.xls</span>
			</div>

			<div class="">
				<label for="save_send_mail_flg"><input type="checkbox" id="save_send_mail_flg" name="save_send_mail_flg" ng-model="ins_param.send_mail_flg" value="1"> レポート作成が完了したら、通知メールを受け取る</label>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<div class="in-process pull-left" ng-show="is_processing">
			<progressbar class="progress-striped active pull-left" type="success" animate="false" value="100"></progressbar>
			保存しています...
		</div>
		<button class="btn btn-primary" ng-click="insert()" ng-class="{disabled: is_processing, disabled: !is_valid}">テンプレートを保存する</button>
		<button class="btn btn-default" ng-click="$dismiss('cancel')">キャンセル</button>
	</div>
</script>
