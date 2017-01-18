<form name="reportform" id="report_form" method="POST" ng-hide="isShowTables" class="transition" change-form>


	<!-- レポート対象設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポート対象設定</h4>
	</div>

	<div class="form-block list-group report-form">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-md-12">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_CLIENT; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<div ss-client-combobox="clientComboboxConfig" ng-model="clientCombobox"></div>
				</div>
			</div>
		</div>
	</div>





	<!-- レポート形式設定 -->
	<div class="block-title clearfix">
		<h4 class="title">レポート形式設定</h4>
	</div>



	<div class="form-block list-group axis-form">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_FORMAT; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">レポート形式 <!-- <span class="label label-warning">必須</span> --></h5>
					<div class="btn-group report-type">
						<? foreach ($GLOBALS["report_format_list"] as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.report_format" ng-click="params.termdate.method.reset();getReportElem()" btn-radio="'<?= $key; ?>'" ng-required="!params.report_format"><?= $value; ?></label>
						<? } ?>
					</div>
				</div>


				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_REPORT_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">レポート種別 <!-- <span class="label label-warning">必須</span> --></h5>
					<select class="form-control input-large" name="" ng-model="params.report_type" ng-required="!params.report_type" ng-change="changeReportType()">
						<? foreach ($GLOBALS["report_type_list"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>


				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_SUMMARY_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">サマリ単位<!-- <span class="label label-warning">必須</span> --></h5>
					<select id="component" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'component'">
						<? foreach ($GLOBALS["summary_type_list_component"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="url"  class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'url'">
						<? foreach ($GLOBALS["summary_type_list_url"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="query" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'query'" required>
						<? foreach ($GLOBALS["summary_type_list_query"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="age" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'age'">
						<? foreach ($GLOBALS["summary_type_list_age"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>

					<select id="gender" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'gender'">
						<? foreach ($GLOBALS["summary_type_list_gender"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="hour" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'hour'">
						<? foreach ($GLOBALS["summary_type_list_hour"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="location" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'location'">
						<? foreach ($GLOBALS["summary_type_list_location"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select id="location" class="form-control input-large select-summary_type" name="summary_type" ng-model="params.summary_type" ng-change="resetExtCv();getReportElem()" ng-required="true" ng-if="params.report_type === 'category'">
						<? foreach ($GLOBALS["summary_type_list_category"] as $key => $value) { ?>
							<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>
			</div>
		</div>

		<div class="controls list-group-item">
			<div class="row">
				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_DEVICE_TYPE; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">デバイス別出力<!-- <span class="label label-warning">必須</span> --></h5>
					<div class="device-type">
						<select class="form-control input-large select-device_type" name="" ng-model="params.device_type" ng-required="!params.device_type" ng-change="getReportElem()">
							<? foreach ($GLOBALS["device_type_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
							<? } ?>
						</select>
					</div>
				</div>


				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_MEDIA_COST; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">媒体費 <!-- <span class="label label-warning">必須</span> --></h5>
					<div>
						<input type="text" id="media_cost" name="media_cost" ng-model="params.media_cost" ng-required="true" class="form-control">
					</div>
				</div>


				<div class="col-md-4">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_ONLY_HAS_LIST; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">絞り込み </h5>
					<div class="btn-group report-type">

						<? foreach ($GLOBALS["only_has_list"] as $key => $value) { ?>
							<label class="btn btn-sm btn-default" ng-model="params.<?= $key; ?>" btn-checkbox=""><?= $value; ?></label>
						<? } ?>

					</div>
				</div>
			</div>
		</div>

		<div class="controls list-group-item">
			<div class="row">
				<div class="col-md-12">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_OPTION; ?>"><span class="glyphicon glyphicon-question-sign"></span></a>

					<h5 class="control-label">指標選択</h5>
					<div class="label-block">
						<div>
							<label class="checkbox-inline option" for="{{item.key}}" ng-repeat="item in report_elem_basic_list">
								<input id="{{item.key}}" type="checkbox" ng-model="params.elem_list[item.key]">{{item.value}}</label>
							</label>
						</div>
						<div>
							<label class="checkbox-inline option" for="{{item.key}}" ng-repeat="item in report_elem_option_list">
								<input id="{{item.key}}" type="checkbox" ng-model="params.elem_list[item.key]">{{item.value}}</label>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>





		<div class="controls list-group-item" ng-controller="ExtCvCtrl as ExtCvCtrl" ng-show="params.report_type=='component' && params.summary_type!=='ad'">
			<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXT_CV; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label" data-toggle="collapse" data-target="#accordion_ext_cv"><i class="glyphicon glyphicon-triangle-bottom"></i> 外部CV設定</h5>
			<div class="row select-box collapse" id="accordion_ext_cv">

				<div class="col-md-6">
					<span class="glyphicon glyphicon-transfer icon-trans"></span>
					<div class="clearfix">
						<h5 class="sub-header pull-left">選択可能項目 <span class="ext_count"> ({{ExtCvCtrl.models.selected | pickup:true}}/{{ExtCvCtrl.models.available.length}}件)</span></h5>
						<button type="button" class="btn btn-xs btn-default pull-right" ng-click="ExtCvCtrl.addAll()">全選択</button>
					</div>
					<div class="extcv-filter-wrap ng-scope">
						<input type="search" class="form-control input-sm ng-pristine ng-valid" name="" ng-model="ExtCvCtrl.filterAvailable" placeholder="CV名で絞り込む" ng-change="ExtCvCtrl.selectQuery(false)">
					</div>

					<ul class="list-group available-list">
						<li class="option list-group-item clearfix"
							ng-repeat="item in ExtCvCtrl.models.available track by $index"
							ng-show="item.is_show && item.is_search_result"
							ng-click="ExtCvCtrl.addItem($index)"
							>
							<span class="name">{{item.cv_display}}</span>
						</li>
					</ul>
				</div>

				<div class="col-md-6">
					<div class="clearfix">
						<h5 class="sub-header pull-left">出力する項目</h5>
						<button type="button" class="btn btn-xs btn-default pull-right" ng-click="ExtCvCtrl.clear()">全解除</button>
					</div>

					<div class="extcv-filter-wrap ng-scope">
						<input type="search" class="form-control input-sm ng-pristine ng-valid" name="" ng-model="ExtCvCtrl.filterSelected" placeholder="CV名で絞り込む" ng-change="ExtCvCtrl.selectQuery(true)">
					</div>

					<ul class="list-group selected-list">
						<li class="option list-group-item clearfix"
							ng-repeat="item in ExtCvCtrl.models.selected track by $index"
							ng-show="item.is_show && item.is_search_result"
							ng-click="ExtCvCtrl.removeItem($index)"
							>
							<span class="name">{{item.cv_display}}</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>





	<!-- 集計期間設定 -->
	<div class="block-title clearfix">
		<h4 class="title">集計期間設定</h4>
	</div>

	<div class="form-block list-group">
		<div ss-termdate="termdate_config" max-days="365" ng-model="params.termdate" data-ng-init="getReportElem()"></div>
	</div>

	<!-- フィルタリング設定 -->
	<div class="block-title clearfix">
		<h4 class="title">フィルタリング設定</h4>
	</div>

	<div class="form-block list-group" ng-controller="FormFilterCtrl as FormFilterCtrl">
		<div class="controls list-group-item">
			<div class="row">
				<div class="col-md-5">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_FILTER_ELEM; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ項目
						<button type="button" class="btn btn-xs btn-info left_10px" ng-click="FormFilterCtrl.addFilter()">
							<i class="glyphicon glyphicon-plus"></i> フィルタを追加
						</button>
					</h5>
				</div>

				<div class="col-md-6 sub-filter">
					<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_FILTER_SETTING; ?>">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
					<h5 class="control-label">フィルタ設定</h5>
				</div>
			</div>
			<div class="row filter-input" ng-repeat="filter in params.filters | limitTo:<?= count($GLOBALS["filter_item_list"]); ?>">
				<label for="filter_item" class="term-number label label-number pull-left">{{$index + 1}}</label>
				<div class="col-md-3">
					<select class="form-control form-select" name="filter_item[]" ng-model="params.filters[$index].filter_item" is-open="params.filters_is_open[$index].filter_item" ng-change="FormFilterCtrl.ctrlDisabled($index)">
						<? foreach ($GLOBALS["filter_item_list"] as $key => $value) { ?>
							<option value="<?= $key; ?>" ng-show="FormFilterCtrl.is_showFilter('<?= $key; ?>')"><?= $value; ?></option>
						<? } ?>
					</select>
					<br>
					<button type="button" class="btn btn-xs btn-default" ng-click="FormFilterCtrl.clearFilter($index)"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
					<button type="button" class="btn btn-xs btn-warning" ng-click="FormFilterCtrl.deleteFilter($index)" ng-show="$index > 0"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
				</div>
				<div class="col-md-2">
					<select class="form-control form-select" name="filter_cond[]" ng-model="params.filters[$index].filter_cond" is-open="params.filters_is_open[$index].filter_cond" ng-show="params.filters[$index].filter_item==='match_type'">
						<? foreach ($GLOBALS["filter_match_type_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
					<select ng-disabled="FormFilterCtrl.filters.match_type.is_disabled[$index]" class="form-control form-select" name="filter_cond[]" ng-model="params.filters[$index].filter_cond" is-open="params.filters_is_open[$index].filter_cond" ng-show="params.filters[$index].filter_item!=='match_type'">
						<? foreach ($GLOBALS["filter_cond_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
						<? } ?>
					</select>
				</div>
				<div class="col-md-6">
					<textarea class="form-control filter-textarea" type="text" name="filter_text[]" ng-model="params.filters[$index].filter_text" is-open="params.filters_is_open[$index].filter_text" ng-show="params.filters[$index].filter_item!=='match_type'" value="" placeholder="フィルタリング条件　※改行で複数入力できます"></textarea>
				</div>
			</div>
		</div>
	</div>


	<!-- レポート出力設定 -->
	<div class="bottom_10px">
		<div class="btn-group export-type">
			<label class="btn btn-sm btn-default" ng-model="params.export_type" btn-radio="'display'"><i class="glyphicon glyphicon-list-alt"></i> レポートを表示</label>
			<label class="btn btn-sm btn-default" ng-model="params.export_type" btn-radio="'export'" desabled><i class="glyphicon glyphicon-save"></i> レポートを作成</label>
		</div>
		<input type="hidden" id="export_type" name="export_type" value="{{params.export_type}}" />
	</div>

	<div class="form-block list-group top_20px">
		<div class="controls list-group-item" ng-show="params.export_type==='export'">
			<a ss-tooltip class="pull-right" data-placement="left" title="<?= EXPLAIN_FORM_EXPORT_FORMAT; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">出力フォーマット <span class="label label-warning">必須</span></h5>
			<div class="btn-group export-format">
				<? foreach ($GLOBALS["export_format_list"] as $key => $value) { ?>
					<!-- これでいいのであろうか -->
					<? if ($key === "excel") { ?>
						<label class="btn btn-sm btn-default" ng-model="params.export_format" ng-disabled="params.report_format!=='summary' && params.device_type!=='0'" btn-radio="'<?= $key; ?>'" ng-required="!params.export_format"><?= $value; ?></label>
					<? } elseif ($key === "compare") { ?>
						<label class="btn btn-sm btn-default" ng-model="params.export_format" ng-disabled="params.report_format==='summary' || params.device_type!=='0'" btn-radio="'<?= $key; ?>'" ng-required="!params.export_format"><?= $value; ?></label>
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

	<div class="validate-msg alert alert-warning" ng-if="message && message.length!=0">
		<p class="">{{message}}</p>
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






















