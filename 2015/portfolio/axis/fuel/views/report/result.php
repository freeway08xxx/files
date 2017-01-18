<div class="async_table transition" ng-show="isShowTables">
	<div class="table-title-block" update-tables>
		<h4 class="table-title"><i class="glyphicon glyphicon-list-alt"></i> <span>{{async_data.title[0]}}</span></h4>

		<p class="table-info">
			<span class="client_name">{{async_data.title[1] | sub_str:7}}</span>
			<span class="term" ng-show="params.report_format!=='term_compare'">({{async_data.title[2] | sub_str:3}}) </span>
			<a href="javascript:void(0);" class="back" ng-click="deleteTables();">< 選択画面へ</a>
		</p>
	</div>

	<div set-tables>


		<div id="js-controlPositionArea" class="filter well col-max-limit clearfix" control-position-fixed>
			<div id="js-offsetPosition">
				<div class="row">
					<!-- デバイス -->
					<div class="select-filter-block col-md-3" ng-show="!isDeviceLength_1">
						<h6 class="filter-title"><a class="label-accordion link" ng-click="actions.toggleAccordion()" data-toggle="collapse" data-target="#accordion_device"><i class="glyphicon glyphicon-modal-window"></i> デバイス <span class="arrow">▼</span></a></h6>
						<div class="collapse in ac-nav js-ac-nav" id="accordion_device">
							<div class="display_content clearfix">
								<div class="collapse in btn-group report-type">
									<select class="form-control form-select" ng-model="settings.device" ng-change="checkPositionBottom();updateGraph()">
										<option ng-show="async_data[params.report_format].device.all_devices" class="btn btn-sm btn-default" value="all_devices">全てのデバイス</option>
										<option ng-show="async_data[params.report_format].device.pc" class="btn btn-sm btn-default" value="pc">PC</option>
										<option ng-show="async_data[params.report_format].device.pc_tab" class="btn btn-sm btn-default" value="pc_tab">PC+TAB</option>
										<option ng-show="async_data[params.report_format].device.tab" class="btn btn-sm btn-default" value="tab">TAB</option>
										<option ng-show="async_data[params.report_format].device.sp" class="btn btn-sm btn-default" value="sp">SP</option>
										<option ng-show="async_data[params.report_format].device.tab_sp" class="btn btn-sm btn-default" value="tab_sp">TAB+SP</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<!-- /デバイス -->

					<!-- グラフ -->
					<div class="select-filter-block col-md-9" ng-show="params.report_format==='daily'" >
						<h6 class="filter-title"><a class="label-accordion link" ng-click="actions.toggleAccordion()" data-toggle="collapse" data-target="#accordion_graph"><i class="glyphicon glyphicon-stats"></i> グラフ設定 <span class="arrow">▼</span></a></h6>
						<div class="collapse in ac-nav js-ac-nav" id="accordion_graph">
							<div class="display_content clearfix">

								<div class="graph-select collapse in">
									
									<p class="form-title">折れ線グラフ</p>
									<div class="select">
										<select id="graph_y" class="form-control form-select" ng-model="graph.y" ng-change="actions.checkPositionBottom();updateGraph()" ng-options="item.key as item.value for item in graph_elem_list"></select>
									</div>


									<p class="form-title">棒グラフ</p>
									<div class="select">
										<select id="graph_y2" class="form-control form-select" ng-model="graph.y2" ng-change="actions.checkPositionBottom();updateGraph()" ng-options="item.key as item.value for item in graph_elem_list"></select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /グラフ -->


					<!-- 表示項目 -->
					<div class="select-view-block clearfix col-md-12">
						<h6 class="filter-title"><a class="label-accordion link" ng-click="actions.toggleAccordion()" data-toggle="collapse" data-target="#accordion_indicate"><i class="glyphicon glyphicon-check"></i> 表示項目 <span class="arrow">▼</span></a></h6>
						<div class="collapse in ac-nav js-ac-nav" id="accordion_indicate">
							<ul class="col-md-12 display_content basic basic-title">
								<li ng-repeat="item in switch_type_items"><label><input ng-click="actions.checkPositionBottom();" type="checkbox" ng-model="isShow.basic[item.key]">{{item.value}}</label></li>
							</ul>
							<ul class="col-md-12 display_content basic basic-common clearfix">
								<li ng-repeat="item in report_elem_list"><label><input ng-click="actions.checkPositionBottom();"type="checkbox" ng-model="isShow.basic[item.key]">{{item.value}}</label></li>
							</ul>
						</div>
					</div>
					<!-- /表示項目 -->


					<div class="select-filter-block col-md-12" ng-controller="FormReportFilterCtrl" ng-show="params.report_format==='summary'" >
						<h6 class="filter-title"><a class="label-accordion link" ng-click="actions.toggleAccordion()" data-toggle="collapse" data-target="#accordion_filter"><i class="glyphicon glyphicon-search"></i> 絞り込み <span class="arrow">▼</span></a></h6>
						<div class="ac-nav js-ac-nav collapse in" id="accordion_filter">
							<div class="display_content basic media clearfix">
								<!--媒体-->
								<div class="pull-left">
									<div class="inner item_inner">
										<p class="form-title">媒体</p>
										<div class="btn-group filter_check">
											<? foreach ($GLOBALS["media_id_list"] as $key => $value) { ?>
												<label class="btn btn-default active" ng-model="params.report_filters[0].media[<?= $key ?>]" btn-checkbox="" ng-init="params.report_filters[0].media[<?= $key ?>]=false" ng-change="actions.checkPositionBottom();reportFilter()"><?= $value; ?></label>
											<? } ?>
										</div>
									</div>
								</div>
								<!--/ 媒体-->


								<!--マッチタイプ-->
								<div ng-show="params.summary_type==='keyword' || params.report_type==='query'">
									<div class="inner item_inner match_type">
										<p class="form-title">マッチタイプ</p>
										<div class="btn-group filter_check">
											<? foreach ($GLOBALS["filter_match_type_list"] as $key => $value) { ?>
												<label class="btn btn-default active" ng-model="params.report_filters[0].match_type[<?= $key ?>]" btn-checkbox="" ng-init="params.report_filters[0].match_type[<?= $key ?>]=false" ng-change="actions.checkPositionBottom();reportFilter()"><?= $value; ?></label>
											<? } ?>
										</div>
									</div>
								</div>
								<!--/マッチタイプ-->
							</div>

							<div class="display_content basic indicate">
								<div class="inner item_inner">
									<button type="button" class="btn btn-xs btn-info btn-add_filter" ng-click="addReportFilter()">
										<i class="glyphicon glyphicon-plus"></i> 指標項目追加
									</button>

									<p class="form-title">指標</p>
									<div class="row filter-input" ng-repeat="report_filter in params.report_filters">
										<!--指標-->
										<div>
											<div class="select">
												<select class="form-control form-select form-filter" name="report_filter_item[]" ng-model="params.report_filters[$index].filter_item" ng-change="actions.checkPositionBottom();reportFilter()">
													<option value="" selected>指標を選択</option>
													<option ng-repeat="item in filter_items" value="{{item.key}}" ng-show="isShow.basic[item.key]">{{item.value}}</option>
												</select>
											</div>
											<div class="col-md-2">
												<input class="form-control filter-text" type="text" name="report_filter_min[]" ng-model="params.report_filters[$index].filter_min" is-open="params.report_filters_is_open[$index].filter_min" value="" placeholder="min" ng-change="actions.checkPositionBottom();reportFilter()">
											</div>
											<div class="col-md-2">
												<input class="form-control filter-text" type="text" name="report_filter_max[]" ng-model="params.report_filters[$index].filter_max" is-open="params.report_filters_is_open[$index].filter_max" value="" placeholder="max" ng-change="actions.checkPositionBottom();reportFilter()">
											</div>
											<div class="col-md-3">
												<button type="button" class="btn btn-xs btn-default" ng-click="clearReportFilter($index);reportFilter();"><i class="glyphicon glyphicon-refresh"></i> クリア</button>
												<button type="button" class="btn btn-xs btn-warning" ng-click="deleteReportFilter($index);reportFilter();" ng-show="$index > 0"><i class="glyphicon glyphicon-minus-sign"></i> 削除</button>
											</div>
										</div>
										<!--/ 指標-->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<!--/ 件数-->
		<div class="sum-block" ng-class="{'invisible': is_processing || async_data.error}">
			<div ng-show="settings.device==='all_devices'" class="sum-item">全{{all_devices_filtered.length -1}}件</div>
			<div ng-show="settings.device==='pc'" class="sum-item">全{{pc_filtered.length -1}}件</div>
			<div ng-show="settings.device==='pc_tab'" class="sum-item">全{{pc_tab_filtered.length -1}}件</div>
			<div ng-show="settings.device==='tab'" class="sum-item">全{{tab_filtered.length -1}}件</div>
			<div ng-show="settings.device==='sp'" class="sum-item">全{{sp_filtered.length -1}}件</div>
			<div ng-show="settings.device==='tab_sp'" class="sum-item">全{{tab_sp_filtered.length -1}}件</div>
		</div>
		<!--/件数-->

		<!-- サマリ -->
		<div ng-show="params.report_format==='summary'" class="summary" report-filter>
			<div class="set-summary-tables"></div>
		</div>

		<!-- デイリー -->
		<div ng-show="params.report_format==='daily'" class="daily">
			<div class="panel panel-default graph-area">
				<div graph-directive><div id="chart"></div></div>
			</div> 
			<div class="set-daily-tables"></div>
		</div>

		<!-- 期間比較 -->
		<div ng-show="params.report_format==='term_compare'" class="term_compare">
			<div class="set-term_compare-tables"></div>
		</div>

	</div>
</div>

