<form name="reportform" id="report_form" method="POST" ng-scrollable>



	<!-- レポート対象設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポート対象設定</h4>
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-10">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_CLIENT; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<div>
						<ss-client-combobox ng-model="clientCombobox"></ss-client-combobox>	
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- レポート形式設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポート形式設定</h4>
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-3">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">レポート種別 <span class="label label-warning">必須</span></h5>
					<div class="btn-group report-type">
						<? foreach ($GLOBALS["report_type_list"] as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.report_type" ng-click="resetTerm()" btn-radio="'<?= $key; ?>'" ng-required="!params.report_type"><?= $value; ?></label>
						<? } ?>
					</div>
					<input type="hidden" id="report_type" name="report_type" value="{{params.report_type}}" />
				</div>

				<div class="col-sm-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_DEVICE_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">デバイス別出力 <span class="label label-warning">必須</span></h5>{{params.device_type}}
					<div class="btn-group device-type">
						<? foreach ($GLOBALS["device_type_list"] as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.device_type" btn-radio="'<?= $key; ?>'" ng-required="!params.device_type"><?= $value; ?></label>
						<? } ?>
					</div>
					<input type="hidden" id="device_type" name="device_type" value="{{params.device_type}}" />
				</div>

				<div class="col-sm-3">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_MEDIA_COST; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">媒体費 <span class="label label-warning">必須</span></h5>
					<div class="input-group">
						<input type="text" id="media_cost" name="media_cost" ng-model="params.media_cost" ng-required="true"/>
					</div>
				</div>
			</div>
		</div>

		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-3">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_CATEGORY_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">カテゴリ種別 <span class="label label-warning">必須</span></h5>
					<div class="btn-group category-type">
						<? foreach ($GLOBALS["report_category_type_list"] as $key => $value) { ?>
							<!-- これでいいのであろうか -->
							<? if ($key === "category_id") { ?>
								<label class="btn btn-sm btn-default" ng-model="params.category_type" btn-radio="'<?= $key; ?>'" ng-disabled="!clientCombobox.categoryGenre.id" ng-required="!params.category_type"><?= $value; ?></label>
							<? } else { ?>
								<label class="btn btn-sm btn-default" ng-model="params.category_type" btn-radio="'<?= $key; ?>'" ng-required="!params.category_type"><?= $value; ?></label>
							<? } ?>
						<? } ?>
					</div>
					<input type="hidden" id="category_type" name="category_type" value="{{params.category_type}}" />
				</div>

				<div class="col-sm-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_SUMMARY_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">サマリ種別 <span class="label label-warning">必須</span></h5>
					<select class="form-control input-large" name="summary_type" ng-model="params.summary_type" ng-change="resetFilter(); resetExtCv();" ng-required="true">
						<? foreach ($GLOBALS["summary_type_list_account"] as $key => $value) { ?>
							<option value="<?= $key; ?>" ng-show="params.category_type==='account_id'"><?= $value; ?></option>
						<? } ?>
						<? foreach ($GLOBALS["summary_type_list_category"] as $key => $value) { ?>
							<option value="<?= $key; ?>" ng-show="params.category_type==='category_id'"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>

				<div class="col-sm-3">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_OPTION; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">オプション </h5>
					<? foreach ($GLOBALS["option_list"] as $key => $value) { ?>
						<label class="checkbox-inline option" for="<?= $key; ?>">
							<input type="checkbox" id="<?= $key; ?>" name="<?= $key; ?>" ng-model="params.<?= $key; ?>" ng-checked="params.<?= $key; ?>" value="checked"> <?= $value; ?>
						</label>
					<? } ?>
				</div>
			</div>
		</div>

		<div class="controls list-group-item" ng-controller="FormExtCvCtrl">
			<div class="row">
				<div class="col-sm-10">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXT_CV; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">外部CV </h5>
					<div ui-select multiple ng-model="params.ext_cv_list" theme="select2" ng-show="params.summary_type!=='ad' && params.summary_type!=='domain' && params.summary_type!=='url' && params.summary_type!=='query'" style="width:100%;">
						<ui-select-match placeholder="外部CVを選択">{{$item.cv_display}}</ui-select-match>
						<ui-select-choices repeat="ext_cv in ext_cv_list | filter:$select.search">
						<div ng-bind-html="ext_cv.cv_display | highlight: $select.search"></div>
						</ui-select-choices>
					</div>
					<input type="hidden" id="ext_cv_list" name="ext_cv_list" value="{{params.ext_cv_list}}" />
				</div>
			</div>
		</div>
	</div>

	<!-- 集計期間設定 -->
	<div class="block-title clearfix">
		<h4 class="title">集計期間設定</h4>
	</div>

	<div class="form-block list-group">
		<div ss-termdate="termdate_config" ng-model="params.termdate"></div>
	</div>

	<!-- フィルタリング設定 -->
	<div class="block-title clearfix">
		<h4 class="title">フィルタリング設定</h4>
	</div>

	<div class="form-block list-group" ng-controller="FormFilterCtrl">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-sm-5">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_FILTER_ELEM; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ項目
						<button type="button" class="btn btn-xs btn-info left_10px" ng-click="addFilter()">
							<i class="glyphicon glyphicon-plus"></i> フィルタを追加
						</button>
					</h5>
				</div>

				<div class="col-sm-6">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_FILTER_SETTING; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ設定</h5>
				</div>
			</div>
			<div class="row filter-input" ng-repeat="filter in params.filters | limitTo:<?= count($GLOBALS["filter_item_list"]); ?>">
				<label for="filter_item" class="term-number label label-default pull-left">{{$index + 1}}</label>
				<div class="col-sm-3">
					<select class="form-control form-select" name="filter_item[]" ng-model="params.filters[$index].filter_item" is-open="params.filters_is_open[$index].filter_item">
						<? foreach ($GLOBALS["filter_item_list"] as $key => $value) { ?>
							<option value="<?= $key; ?>" ng-show="is_showFilter('<?= $key; ?>')"><?= $value; ?></option>
						<? } ?>
					</select>
					<br>
					<button type="button" class="btn btn-xs btn-default" ng-click="clearFilter($index)"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
					<button type="button" class="btn btn-xs btn-warning" ng-click="deleteFilter($index)" ng-show="$index > 0"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
				</div>
				<div class="col-sm-2">
					<select class="form-control form-select" name="filter_cond[]" ng-model="params.filters[$index].filter_cond" is-open="params.filters_is_open[$index].filter_cond" ng-show="params.filters[$index].filter_item==='match_type'">
						<? foreach ($GLOBALS["filter_match_type_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select class="form-control form-select" name="filter_cond[]" ng-model="params.filters[$index].filter_cond" is-open="params.filters_is_open[$index].filter_cond" ng-show="params.filters[$index].filter_item!=='match_type'">
						<? foreach ($GLOBALS["filter_cond_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>
				<div class="col-sm-6">
					<textarea class="form-control filter-textarea" type="text" name="filter_text[]" ng-model="params.filters[$index].filter_text" is-open="params.filters_is_open[$index].filter_text" ng-show="params.filters[$index].filter_item!=='match_type'" value="" placeholder="フィルタリング条件　※改行で複数入力できます"></textarea>
				</div>
			</div>
		</div>
	</div>

	<!-- レポートフィルタリング設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポートフィルタリング設定</h4>
	</div>

	<div class="form-block list-group">
		<div class="controls list-group-item" ng-controller="FormReportFilterCtrl">
			<div class="row">
				<div class="col-sm-5">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_FILTER_ELEM; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ項目
						<button type="button" class="btn btn-xs btn-info left_10px" ng-click="addReportFilter()">
							<i class="glyphicon glyphicon-plus"></i> フィルタを追加
						</button>
					</h5>
				</div>

				<div class="col-sm-6">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_FILTER_SETTING; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ設定</h5>
				</div>
			</div>
			<div class="row filter-input" ng-repeat="report_filter in params.report_filters | limitTo:<?= count($GLOBALS["report_filter_list"]); ?>">
				<label for="filter_item" class="term-number label label-default pull-left">{{$index + 1}}</label>
				<div class="col-sm-5">
					<select class="form-control form-select" name="report_filter_item[]" ng-model="params.report_filters[$index].filter_item" is-open="params.report_filters_is_open[$index].filter_item">
						<? foreach ($GLOBALS["report_filter_list"] as $key => $value) { ?>
							<option value="<?= $key; ?>" ng-show="is_showReportFilter('<?= $key; ?>')"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>
				<div class="col-sm-2">
					<input class="form-control filter-text" type="text" name="report_filter_min[]" ng-model="params.report_filters[$index].filter_min" is-open="params.report_filters_is_open[$index].filter_min" value="" placeholder="min">
				</div>
				<div class="col-sm-2">
					<input class="form-control filter-text" type="text" name="report_filter_max[]" ng-model="params.report_filters[$index].filter_max" is-open="params.report_filters_is_open[$index].filter_max" value="" placeholder="max">
				</div>
				<div class="col-sm-2">
					<button type="button" class="btn btn-xs btn-default" ng-click="clearReportFilter($index)"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
					<button type="button" class="btn btn-xs btn-warning" ng-click="deleteReportFilter($index)" ng-show="$index > 0"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
				</div>
			</div>
		</div>
		<div class="controls list-group-item" ng-controller="FormExtCvFilterCtrl" ng-show="params.ext_cv_list.length">
			<div class="row">
				<div class="col-sm-5">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXT_CV_FILTER_ELEM; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ項目(外部CV個別)]
						<button type="button" class="btn btn-xs btn-info left_10px" ng-click="addExtCvFilter()">
							<i class="glyphicon glyphicon-plus"></i> フィルタを追加
						</button>
					</h5>
				</div>

				<div class="col-sm-6">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXT_CV_FILTER_SETTING; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ設定(外部CV個別)</h5>
				</div>
			</div>
			<div class="row filter-input" ng-repeat="ext_cv_filter in params.ext_cv_filters | limitTo: params.ext_cv_list.length * 3">
				<label for="filter_item" class="term-number label label-default pull-left">{{$index + 1}}</label>
				<div class="col-sm-3">
					<select class="form-control form-select" name="ext_cv_filter_elem[]" ng-model="params.ext_cv_filters[$index].filter_elem" is-open="params.ext_cv_filters[$index].filter_elem" ng-options="ext_cv.cv_display for ext_cv in params.ext_cv_list track by ext_cv.cv_key"></select>
				</div>
				<div class="col-sm-2">
					<select class="form-control form-select" name="ext_cv_filter_item[]" ng-model="params.ext_cv_filters[$index].filter_item" is-open="params.ext_cv_filters[$index].filter_item">
						<option value="cv">CVs</option>
						<option value="cvr">CVR</option>
						<option value="cpa">CPA</option>
					</select>
				</div>
				<div class="col-sm-2">
					<input class="form-control filter-text" type="text" name="ext_cv_filter_min[]" ng-model="params.ext_cv_filters[$index].filter_min" is-open="params.ext_cv_filters_is_open[$index].filter_min" value="" placeholder="min">
				</div>
				<div class="col-sm-2">
					<input class="form-control filter-text" type="text" name="ext_cv_filter_max[]" ng-model="params.ext_cv_filters[$index].filter_max" is-open="params.ext_cv_filters_is_open[$index].filter_max" value="" placeholder="max">
				</div>
				<div class="col-sm-2">
					<button type="button" class="btn btn-xs btn-default" ng-click="clearExtCvFilter($index)"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
					<button type="button" class="btn btn-xs btn-warning" ng-click="deleteExtCvFilter($index)" ng-show="$index > 0"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
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
		<div class="controls list-group-item" ng-show="params.export_type==='export'">
			<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXPORT_FORMAT; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">出力フォーマット <span class="label label-warning">必須</span></h5>
			<div class="btn-group export-format">
				<? foreach ($GLOBALS["export_format_list"] as $key => $value) { ?>
					<!-- これでいいのであろうか -->
					<? if ($key === "excel") { ?>
						<label class="btn btn-sm btn-default" ng-model="params.export_format" ng-disabled="params.report_type!=='summary' && params.device_type!=='0'" btn-radio="'<?= $key; ?>'" ng-required="!params.export_format"><?= $value; ?></label>
					<? } elseif ($key === "compare") { ?>
						<label class="btn btn-sm btn-default" ng-model="params.export_format" ng-disabled="params.report_type==='summary' || params.device_type!=='0'" btn-radio="'<?= $key; ?>'" ng-required="!params.export_format"><?= $value; ?></label>
					<? } else { ?>
						<label class="btn btn-sm btn-default" ng-model="params.export_format" btn-radio="'<?= $key; ?>'" ng-required="!params.export_format"><?= $value; ?></label>
					<? } ?>
				<? } ?>
			</div>
			<input type="hidden" id="export_format" name="export_format" value="{{params.export_format}}" />
		</div>

		<div class="controls list-group-item" ng-show="params.export_type==='export'">
			<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_NAME; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">レポート名を入力してください</h5>
			<div class="input-group bottom_10px">
				<input class="form-control" type="text" name="report_name" ng-model="params.report_name" value="" placeholder="レポート名（例：2014年サンプルクライアント日別推移レポート）">
				<span class="input-group-addon">.xls</span>
			</div>

			<div class="">
				<label for="send_mail_flg"><input type="checkbox" id="send_mail_flg" name="send_mail_flg" ng-model="params.send_mail_flg" value="1"> レポート作成が完了したら、通知メールを受け取る</label>
				<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_SEND_MAIL; ?>">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
			</div>
		</div>
	</div>

	<div class="validate-msg alert alert-warning" ng-show="msg">
		<p class="">{{msg}}</p>
		<p class="" ng-show="reportform.start_date.$error.required">集計期間を設定してください。</p>
	</div>

	<div class="btn-area clearfix">
		<button type="button" class="btn btn-primary pull-left" ng-click="submit()" ng-class="{disabled: is_processing}" ng-disabled="reportform.$invalid">レポートを作成する</button>

		<div class="loading pull-left" ng-show="is_processing">
			<progressbar class="progress-striped active" type="success" animate="false" value="100"></progressbar>
			作成しています...
		</div>

		<button type="button" class="btn btn-sm btn-default pull-right" ng-click="save()" ng-class="{disabled: is_processing}" ng-show="params.export_type==='export'" ng-disabled="reportform.$invalid">テンプレートとして保存</button>
	</div>












</form>









<script type="text/ng-template" id="tmpl_saveTemplate">
	<div class="modal-header">
		<button class="pull-right btn btn-link close-icon" ng-click="$dismiss()"><span class="glyphicon glyphicon-remove"></span></button>
		<h4>テンプレートとして保存</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="control-label">テンプレート名 <span class="label label-warning">必須</span></label>
			<input type="text" class="form-control" ng-model="params.template_name" ng-required="true" placeholder="（例）期間比較テンプレート"></input>
		</div>
		<div class="form-group">
			<label class="control-label">メモ</label>
			<textarea type="text" class="form-control" rows="3" ng-model="params.template_memo" placeholder="（例）テンプレートの説明文"></textarea>
		</div>
		<div class="form-group" ng-show="!params.report_name">
			<label class="control-label">レポート名 </label>
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
		<button class="btn btn-primary" ng-show="!is_update" ng-click="insert()" ng-class="{disabled: is_processing, disabled: !is_valid}">テンプレートを新規保存する</button>
		<button class="btn btn-primary" ng-show="is_update" ng-click="update()" ng-class="{disabled: is_processing, disabled: !is_valid}">テンプレートを上書き保存する</button>
		<button class="btn btn-default" ng-click="$dismiss('cancel')">キャンセル</button>
	</div>
</script>






















