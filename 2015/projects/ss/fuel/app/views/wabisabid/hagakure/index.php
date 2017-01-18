<form name="HagakureForm" ng-init="init();" ng-controller="HagakureCtrl as hagakureCtrl" class="hagakure">


	<!-- 設定一覧 -->
	<div class="" ng-show="display_type === 'setting'">
		<h4 class="title pull-left">設定一覧</h4>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="table-label">
					<td rowspan="2" class="text-right">入札ルールID</td>
					<td rowspan="2" class="">入札ルール名称</td>
					<td rowspan="2" class="text-center">ステータス</td>
					<td rowspan="2" class="text-center">登録日</td>
					<td rowspan="2" class="text-left">登録者</td>
					<td rowspan="2" class="text-center">更新日</td>
					<td rowspan="2" class="text-left">更新者</td>
					<td colspan="4" class="text-center user">ユーザー操作</td>
				</tr>
				<tr class="table-label user">
					<td class="text-center">処理結果</td>
					<td class="text-center">入札履歴</td>
					<td class="text-center">設定変更</td>
					<td class="text-center">削除</td>
				</tr>
			<thead>
			<tbody>
				<tr class="" ng-repeat="setting in setting_list">
					<td class="wabisabi_id">{{setting.wabisabi_id}}</td>
					<td class="">{{setting.wabisabi_name}}</td>
					<td class="status">
						<div class="btn-group">
							<label class="btn btn-sm btn-default" ng-model="setting.status" btn-radio="'<?= DB_STATUS_ON; ?>'" ng-click="status_on(setting.wabisabi_id);">運用</label>
							<label class="btn btn-sm btn-default" ng-model="setting.status" btn-radio="'<?= DB_STATUS_OFF; ?>'" ng-click="status_off(setting.wabisabi_id);">停止</label>
						</div>
					</td>

					<td class="text-center">{{setting.created_at | moment: 'YYYY.MM.DD HH:MM'}}</td>
					<td class="">{{setting.created_user}}</td>
					<td class="text-center">{{setting.updated_at | moment: 'YYYY.MM.DD HH:MM'}}</td>
					<td class="">{{setting.updated_user}}</td>
					<td class="setting">
						<button type="button" class="btn btn-primary" ng-click="result(setting.wabisabi_id);">処理結果</button>
					</td>
					<td class="setting">
						<button type="button" class="btn btn-primary" ng-click="bidding(setting.wabisabi_id);">入札履歴</button>
					</td>
					<td class="setting">
						<button type="button" class="btn btn-primary glyphicon glyphicon-cog" ng-click="edit(setting.wabisabi_id);"></button>
					</td>
					<td class="remove">
						<button type="button" class="btn btn-danger" ng-click="delete(setting.wabisabi_id);"><span class="glyphicon glyphicon-remove"></span></button>
					</td>
				</tr>
			<tbody>
		</table>

		<!-- 新規登録ボタン -->
		<button type="button" class="btn btn-primary btn-sm" ng-click="regist();">新規登録</button>
	</div>

	<!-- 処理結果一覧 -->
	<div class="result" ng-show="display_type === 'result'">
		<h4 class="title pull-left">処理結果一覧</h4>
		<button type="button" class="btn btn-default pull-left back" ng-click="back();"><span class="glyphicon glyphicon-chevron-left"></span>戻る</button>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="table-label">
					<td class="">処理種別</td>
					<td class="">処理日</td>
					<td class="">処理結果</td>
					<td class="">登録日</td>
					<td class="">更新日</td>
				</tr>
			</thead>
			<tbody>
				<tr class="" ng-repeat="result in result_list">
					<td class="exec_type"><span class="label label-{{result.exec_type_label}}">{{result.exec_type}}</span></td>
					<td class="">{{result.exec_date | moment: 'YYYY.MM.DD'}}</td>
					<td class="status"><span class="label label-{{result.status_label}}">{{result.status}}</span></td>
					<td class="">{{result.created_at | moment: 'YYYY.MM.DD HH:MM'}}</td>
					<td class="">{{result.updated_at | moment: 'YYYY.MM.DD HH:MM'}}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- 入札履歴一覧 -->
	<div class="bidding" ng-show="display_type === 'bidding' || display_type === 'biddetail'">
		<h4 class="title pull-left">入札履歴一覧</h4>
		<button type="button" class="btn btn-default pull-left back" ng-click="back();"><span class="glyphicon glyphicon-chevron-left"></span>戻る</button>
		<table class="table table-hover table-bordered">
			<thead>
				<tr class="table-label">
					<td rowspan="2" class="text-right">入札日</td>
					<td rowspan="2" class="text-right">入札件数</td>
					<td colspan="2" class="text-center">入札詳細</td>
				</tr>

				<tr class="table-label">
					<td class="text-center">閲覧</td>
					<td class="text-center download">ダウンロード</td>
				</tr>

			</thead>
			<tbody>
				<tr class="" ng-repeat="bidding in bidding_list">
					<td class="text-right">{{bidding.new_cpc_update_date | moment: 'YYYY.MM.DD'}}</td>
					<td class="text-right">{{bidding.count | number: 0}}</td>
					<td class="text-center">
						<button type="button" class="btn btn-primary" ng-click="biddetail(bidding.wabisabi_id, bidding.new_cpc_update_date);">閲覧</button>
					</td>
					<td class="text-center">
						<a class="btn btn-primary" href="/sem/new/wabisabid/hagakure/biddownload/{{bidding.wabisabi_id}}/{{bidding.new_cpc_update_date}}"><span class="glyphicon glyphicon-download-alt"></span></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- 入札履歴詳細一覧 -->
	<div class="biddetail" ng-show="display_type === 'biddetail'">
		<table class="table table-bordered">
			<thead>
				<tr class="table-label">
					<td class="">アカウントID</td>
					<td class="">アカウント名</td>
					<td class="">キャンペーンID</td>
					<td class="">キャンペーン名</td>
					<td class="">広告グループID</td>
					<td class="">広告グループ名</td>
					<td class="">キーワードID</td>
					<td class="">キーワード</td>
					<td class="">入札額</td>
					<td class="">入札調整率</td>
					<td class="">新入札額</td>
					<td class="">新入札調整率</td>
					<td class="">入札日</td>
				</tr>
			</thead>

			<tbody>
				<tr class="" ng-repeat="biddetail in biddetail_list | offset:pagination.offset | limitTo: pagination.limit">
					<td class="">{{biddetail.account_id}}</td>
					<td class="account_name"><img ng-if="!!biddetail.icon" class="icon-media" ng-src={{biddetail.icon}}>{{biddetail.account_name}}</td>
					<td class="">{{biddetail.campaign_id}}</td>
					<td class="">{{biddetail.campaign_name}}</td>
					<td class="">{{biddetail.adgroup_id}}</td>
					<td class="adgroup_name">{{biddetail.adgroup_name}}</td>
					<td class="">{{biddetail.keyword_id}}</td>
					<td class="keyword">{{biddetail.keyword}}</td>
					<td class="">{{biddetail.cpc_max | number: 0}}</td>
					<td class="">{{biddetail.bid_modifier}} %</td>
					<td class="">{{biddetail.new_cpc_max | number: 0}}</td>
					<td class="">{{biddetail.new_bid_modifier}} %</td>
					<td class="">{{biddetail.new_cpc_update_date | moment: 'YYYY.MM.DD'}}</td>
				</tr>
			</tbody>
		</table>

		<div class="clearfix">
			<pagination total-items="biddetail_list.length" ng-model="pagination.currentPage" max-size="pagination.maxSize" 
			class="pagination-sm" boundary-links="true" rotate="false" num-pages="pagination.numPages" items-per-page="pagination.limit"
			previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
		</div>

	</div>

	<div class="form" ng-show="display_type === 'form'">
		<div class="block-title clearfix">
			<h4 class="title">設定を登録</h4>
		</div>

		<!-- 入札ルール名称 -->
		<div class="form-block list-group">
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-3 wabisabi_name">
						<label class="control-label">入札ルール名称</label>
						<span class="label label-warning align-right">必須</span>
					</div>
					<div class="col-sm-9">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-wrench"></span></div>
							<input type="text" class="form-control" name="wabisabi_name" ng-model="params.wabisabi_name" ng-required="true">
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 設定内容 -->
		<div class="setting_details">
			<div class="row">
			<div class="col-sm-6">
				<div class="form-block left-block list-group">
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">媒体費</label>
								<span class="label label-warning align-right">必須</span>
							</div>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="media_cost" ng-model="params.media_cost" ng-required="true" placeholder="0～100% でパーセンテージ入力">
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">ターゲット予算</label>
								<span class="label label-warning align-right">必須</span>
							</div>
							<div class="col-sm-3">
								<div class="btn-group">
									<label class="btn btn-sm btn-default" ng-model="params.target_budget_mode" btn-radio="'<?= TARGET_BUDGET_MODE_MON; ?>'" ng-required="!params.target_budget_mode">月額</label>
									<label class="btn btn-sm btn-default" ng-model="params.target_budget_mode" btn-radio="'<?= TARGET_BUDGET_MODE_DAY; ?>'" ng-required="!params.target_budget_mode">日別</label>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></div>
									<input type="number" class="form-control" name="target_budget" ng-model="params.target_budget" ng-required="true">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">目標CPA</label>
								<span class="label label-warning align-right">必須</span>
							</div>
							<div class="col-sm-8">
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></div>
									<input type="number" class="form-control" name="target_cpa" ng-model="params.target_cpa" ng-required="true">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">入札調整率の<br>上下限値</label>
								<span class="label label-warning align-right">必須</span>
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-arrow-up"></span></div>
									<select class="form-control input-large" name="new_bid_rate_max" ng-model="params.new_bid_rate_max" ng-required="true">
										<? foreach ($GLOBALS['new_bid_rate_max_list'] as $key => $value) { ?>
											<option value="<?= $key; ?>"><?= $value; ?></option>
										<? } ?>
									</select>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="input-group add_content">
									<div class="input-group-addon"><span class="glyphicon glyphicon-arrow-down"></span></div>
									<select class="form-control input-large" name="new_bid_rate_min" ng-model="params.new_bid_rate_min" ng-required="true">
										<? foreach ($GLOBALS['new_bid_rate_min_list'] as $key => $value) { ?>
											<option value="<?= $key; ?>"><?= $value; ?></option>
										<? } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">入札額の上限値</label>
							</div>
							<div class="col-sm-8">
								<div class="input-group">
									<div class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></div>
									<input type="number" class="form-control" name="limit_cpc" ng-model="params.limit_cpc">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label">モバイル調整率の上限値</label>
							</div>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="limit_mba" ng-model="params.limit_mba" placeholder="-100% or -90～300% でパーセンテージ入力">
							</div>
						</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="form-block right-block list-group">
					<div class="row">
						<div class="col-sm-4">
							<label class="control-label">入札モード</label>
							<span class="label label-warning align-right">必須</span>
						</div>
						<div class="col-sm-8">
							<div class="btn-group">
								<label class="btn btn-sm btn-default width_fix" ng-model="params.no_bids_flg" btn-radio="'<?= NO_BIDS_FLG_OFF; ?>'" ng-required="!params.no_bids_flg">入札する</label>
								<label class="btn btn-sm btn-default width_fix" ng-model="params.no_bids_flg" btn-radio="'<?= NO_BIDS_FLG_ON; ?>'" ng-required="!params.no_bids_flg">入札しない</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label class="control-label">基準日コストパターン</label>
							<span class="label label-warning align-right">必須</span>
						</div>
						<div class="col-sm-8">
							<div class="btn-group">
								<label class="btn btn-sm btn-default" ng-model="params.reference_cost_pattern" btn-radio="'<?= REF_COST_ALWAYS; ?>'" ng-required="!params.reference_cost_pattern">常に前日コストを基準</label>
								<label class="btn btn-sm btn-default" ng-model="params.reference_cost_pattern" btn-radio="'<?= REF_COST_LAST; ?>'" ng-required="!params.reference_cost_pattern">最終平日コストを基準</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label class="control-label">入札実施日パターン</label>
							<span class="label label-warning align-right">必須</span>
						</div>
						<div class="col-sm-8">
							<div class="btn-group">
								<label class="btn btn-sm btn-default" ng-repeat="bid_day in params.bid_days" ng-model="params.checked_bid_days[$index]" btn-checkbox>{{bid_day.name}}</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label class="control-label">未参照日パターン</label>
						</div>
						<div class="col-sm-8">
							<div class="btn-group">
								<label class="btn btn-sm btn-default" ng-repeat="no_sum_day in params.no_sum_days" ng-model="params.checked_no_sum_days[$index]" btn-checkbox>{{no_sum_day.name}}</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label class="control-label">データ参照開始日</label>
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" class="form-control" name="sum_start_date"  ng-model="params.sum_start_date" placeholder="YYYY/MM/DD"
									is-open="datepicker.opened" min-date="datepicker.minDate" max-date="datepicker.maxDate" datepicker-popup="{{datepicker.format}}" datepicker-options="datepicker.dateOptions" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-default" ng-click="datepicker.open($event)"><i class="glyphicon glyphicon-calendar"></i></button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>

		<!-- 外部CV -->
		<div class="form-block list-group">
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-2">
						<label class="control-label">外部CV</label>
					</div>
					<div class="col-sm-10">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-tag"></span></div>
							<div ui-select multiple ng-model="params.extcv_list" theme="select2" style="width:100%;">>
								<ui-select-match placeholder="外部CVを選択">{{$item.cv_display}}</ui-select-match>
								<ui-select-choices repeat="extcv in extcv_list | filter:$select.search">{{extcv.cv_display}}</ui-select-choices>
							</div>
						</div>
					</div>
				</div>
				<div class="row" ng-show="params.extcv_list">
					<div class="col-sm-2">
						<label class="control-label">処理開始時間</label>
					</div>
					<div class="col-sm-10">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-time"></span></div>
							<select class="form-control input-large" name="extcv_exec_hour" ng-model="params.extcv_exec_hour">
								<? foreach ($GLOBALS['extcv_exec_hour_list'] as $key => $value) { ?>
									<option value="<?= $key; ?>"><?= $value; ?></option>
								<? } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- フィルタリング -->
		<div class="form-block list-group filter">
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-5">
							<button type="button" class="btn btn-xs btn-info" ng-click="addFilter()">
								<i class="glyphicon glyphicon-plus"></i> フィルタを追加
							</button>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
					</div>
					<div class="col-sm-7">
					</div>
				</div>

				<div class="row repeat-block" ng-repeat="filter in params.filters | limitTo:<?= count($GLOBALS["filter_item_list"]); ?>">

					<div class="col-sm-1 number">
						<label class="term-number label label-default pull-left">{{$index + 1}}</label>
					</div>

					<div class="col-sm-1 text-right content">
						<label class="control-label">フィルタ項目</label>
					</div>

					<div class="col-sm-2">
						<select class="form-control form-select" name="filter_item[]" ng-model="params.filters[$index].filter_item" is-open="params.filters_is_open[$index].filter_item">
							<? foreach ($GLOBALS["filter_item_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
							<? } ?>
						</select>
					</div>

					<div class="btns">
						<button type="button" class="btn btn-xs btn-default" ng-click="clearFilter($index)"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
						<button type="button" class="btn btn-xs btn-warning" ng-click="deleteFilter($index)"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
					</div>
					<div class="col-sm-2">
						<select class="form-control form-select" name="filter_cond[]" ng-model="params.filters[$index].filter_cond" is-open="params.filters_is_open[$index].filter_cond"  ng-required="true">


							<? foreach ($GLOBALS["filter_cond_list"] as $key => $value) { ?>
								<option value="<?= $key; ?>"><?= $value; ?></option>
							<? } ?>
						</select>
					</div>

					<div class="col-sm-4">
						<textarea class="form-control filter-textarea" type="text" name="filter_text[]" ng-model="params.filters[$index].filter_text" is-open="params.filters_is_open[$index].filter_text" ng-show="params.filters[$index].filter_item!=='match_type'" value="" ng-required="true" placeholder="フィルタリング条件　※改行で複数入力できます"></textarea>
					</div>
				</div>
			</div>
		</div>

		<!-- ボタン -->
		<div class="">
			<div class="btn-group">
				<label class="btn btn-default btn-sm" ng-click="back();">キャンセル</label>
				<label class="btn btn-primary btn-sm" ng-click="save();" ng-disabled="HagakureForm.$invalid">設定を保存する</label>
			</div>
		</div>
	</div>
</form>
