<div class="form-block list-group">
	<div class="controls list-group-item" ng-class="{edit: display.models.is_edit_mode}">

		<!-- list -->
		<div class="transition" ng-hide="display.models.is_edit_mode">
			<div class="header-area clearfix">
				<a class="pull-right" rel="tooltip"
					data-toggle="tooltip" data-html="true" data-placement="left"
					title="<?= FALCON_EXPLAIN_FORM_LINE_SETTING; ?>">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>

				<h4>表示項目設定
					<small class="left_10px">デバイス別出力：
						<span ng-show="report.isSetDeviceType()">有り</span>
						<span ng-show="!report.isSetDeviceType()">なし</span>
					</small>
				</h4>
			</div>

			<div class="col-list" ng-class="{device: report.isSetDeviceType()}"
			ng-repeat="device in common.config.report.report_device_line"
			ng-hide="display.isDeviceOutPutEnabled(device.key)">
				<div class="list-table-wrap">
					<table class="table table-condensed table-hover table-striped">
						<thead>
							<tr>
								<th class="clearfix">
									{{device.name}}
									<button type="button" class="edit-btn btn btn-xs btn-default"
									 ng-click="display.edit(device.key)">
										<span class="glyphicon glyphicon-pencil"></span> 編集
									</button>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="item in display.models.col_arr[device.key]">
								<td>{{item.name}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- / -->

		<!-- edit -->
		<div class="edit-view transition" ng-show="display.models.is_edit_mode" ng-controller="FalconReportDisplayEditCtrl as edit">
			<div class="header-area clearfix">
				<h4 class="pull-left right_10px">表示項目を編集</h4>
				<h5 class="pull-left">
					<span class="device-name label label-sm label-warning" ng-bind="edit.models.device.name"></span>
				</h5>
			</div>

			<div class="row">
				<div class="available-list col-xs-4">
					<h5>選択可能項目</h5>

					<accordion close-others="true">
						<accordion-group heading="基本項目" is-open="status.isFirstOpen">
							<table class="table table-condensed table-hover">
								<tbody>
									<tr ng-repeat="item in edit.models.available.standard" ng-show="item.view_flg">
										<td>
											<a class="avail-link" ng-click="edit.add(item, $index)">
												<span>{{item.name}}</span>
												<span class="text-success pull-right">
													<span class="glyphicon glyphicon-plus"></span>
												</span>
											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</accordion-group>
						<accordion-group heading="媒体CV">
							<table class="table table-condensed table-hover">
								<tbody>
									<tr ng-repeat="item in edit.models.available.media_cv" ng-show="item.view_flg">
										<td>
											<a class="avail-link" ng-click="edit.add(item, $index)">
												<span>{{item.name}}</span>
												<span class="text-success pull-right">
													<span class="glyphicon glyphicon-plus"></span>
												</span>
											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</accordion-group>
						<accordion-group class="ext-cv" ng-show="edit.models.available.ext_cv">
							<accordion-heading>
								外部CV
								<span class="badge pull-right">{{filteredItems.length}}</span>
							</accordion-heading>

							<div class="extcv-filter-wrap">
								<input type="search" class="form-control input-sm" name=""
								ng-model="edit.models.filter.name" placeholder="CV名で絞り込む">
							</div>
							<div class="extcv-table-wrap">
								<table class="table table-condensed table-hover">
									<tbody>
										<tr ng-repeat="item in filteredItems = (edit.models.available.ext_cv | extCvFilter:edit.models.filter:edit.models.col_arr)">
											<td>
												<a class="avail-link" ng-click="edit.add(item, $index)">
													<span>{{item.name}}</span>
													<span class="text-success pull-right">
														<span class="glyphicon glyphicon-plus"></span>
													</span>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</accordion-group>
					</accordion>

					<button type="button" class="btn btn-sm btn-default" ng-click="edit.openFormulaModal()">
						<span class="glyphicon glyphicon-new-window"></span>
						数式を追加する
					</button>

				</div>
				<div class="col-xs-8">
					<div class="col-header clearfix">
						<h5 class="pull-left">表示する項目</h5>
					</div>

					<table class="editing-list tabel table-condensed table-hover table-striped">
						<thead>
							<tr>
								<th>表示順</th>
								<th>項目名</th>
								<th>レポート表示名</th>
								<th>セル書式</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="item in edit.models.col_arr track by $index" ng-show="item.view_flg">
								<td class="order">
									<input type="number" class="form-control input-sm text-right" name="order" tabindex="{{$index+1}}" value="" ng-model="item.order" ng-init="item.order=$index+1" placeholder="{{$index+1}}">
								</td>
								<td class="name clearfix">
									{{item.name}}
									<button type="button" class="btn btn-xs btn-info pull-right"
									 ng-show="item.element_type === 'formula'"
									 ng-click="edit.editFormula($index)">
										<span class="glyphicon glyphicon-pencil"></span>
										編集
									</button>
								</td>
								<td class="output-name">
									<input type="text" class="form-control input-sm" name="" value="" ng-model="item.output_name" placeholder="--">
								</td>
								<td class="cell-format">
									<select name="cell-format" class="form-control input-sm"
										ng-if="item.element_type !== 'formula'"
										ng-model="item.formula_cell_type"
										ng-options="type.key as type.name for type in common.config.report.report_formula_cell_type">
									</select>
								</td>
								<td class="action">
									<button type="button" class="btn btn-xs btn-danger" ng-click="edit.delete($index)">
										<span class="glyphicon glyphicon-remove"></span>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="btn-area">
				<button class="btn btn-sm btn-primary" ng-click="edit.update()">更新する</button>
				<button class="btn btn-sm btn-default" ng-click="edit.cancel()">キャンセル</button>
			</div>
		</div>
		<!-- / -->

	</div>
</div>