<div class="form-block list-group">
	<div class="list-group-item">

		<div class="header-area clearfix">
			<h4>目標設定
				<small class="pull-right"><?= FALCON_EXPLAIN_FORM_AIM_SETTING; ?></small>
			</h4>
		</div>

		<!-- list -->
		<div class="aim-list" ng-repeat="sheet in aim.models.sheet">
			<div class="list-table-wrap">
				<div class="clearfix">
					<h5 class="cat-header label label-default">{{sheet.name}}</h5>
				</div>
				<div class="table-wrap">

					<table class="table table-condensed table-hover table-striped">
						<thead>
							<!-- common -->
							<tr ng-if="sheet.key !== 'device'">
								<th class="row-type"></th>
								<th ng-repeat="col in aim.models.cols.common">
									{{col.name}}
								</th>
							</tr>
							<!-- / -->

							<!-- device -->
							<tr ng-if="sheet.key === 'device'">
								<th class="row-type"></th>
								<th ng-repeat="col in aim.models.cols.device.merged">
									{{col.name}}
								</th>
							</tr>
							<!-- / -->
						</thead>

						<tbody>
							<!-- Total -->
							<tr ng-if="sheet.key === 'total'">
								<td></td>

								<td ng-repeat="col in aim.models.cols.common">
									<input type="number" class="form-control input-sm"
									 min="0" name="" value="" placeholder=""
									 ng-model="aim.models.data[sheet.key][col.key]">
								</td>
							</tr>
							<!-- / -->

							<!-- device -->
							<tr ng-if="sheet.key === 'device'"
							ng-repeat="(device_id, col_keys) in aim.models.cols.device.separated_keys">
								<td>{{common.config.report.report_device_line[device_id].name}}</td>

								<td ng-repeat="merged in aim.models.cols.device.merged">
									<input type="number" class="form-control input-sm"
									 min="0" name="" value="" placeholder=""
									 ng-if="col_keys.indexOf(merged.key) !== -1"
									 ng-model="aim.models.data[sheet.key + ':' + device_id][merged.key]">
								</td>
							</tr>
							<!-- / -->

							<!-- media -->
							<tr ng-if="sheet.key === 'media'"
							ng-repeat="(media_id, media_name) in aim.models.media_list">
								<td ng-bind="media_name"></td>

								<td ng-repeat="col in aim.models.cols.common">
									<input type="number" class="form-control input-sm"
									 min="0" name="" value="" placeholder=""
									 ng-model="aim.models.data[sheet.key + ':' + media_id][col.key]">
								</td>
							</tr>
							<!-- / -->

							<!-- product -->
							<tr ng-if="sheet.key === 'ad_type'"
							ng-repeat="(product_id, product_name) in common.config.report.report_ad_type">
								<td ng-bind="product_name"></td>

								<td ng-repeat="col in aim.models.cols.common">
									<input type="number" class="form-control input-sm"
									 min="0" name="" value="" placeholder=""
									 ng-model="aim.models.data[sheet.key + ':' + product_id][col.key]">
								</td>
							</tr>
							<!-- / -->

						</tbody>

					</table>
				</div>
			</div>
		</div>
		<!-- / -->
	</div>

	<!-- list Category -->
	<div class="list-group-item category" ng-show="aim.isCategory()">

		<div class="header-area clearfix">
			<h5 class="form-inline">カテゴリ別の目標を設定する</h5>

			<span class="well well-sm data-top pull-right" ng-show="aim.models.category.registered">
				<span class="label label-success">設定済</span>
				{{aim.models.category.registered}}
			</span>
		</div>

		<div class="form-container">
			<div class="list-group form-block">

				<div class="controls list-group-item step step_download">
					<h5 class="pull-left right_10px">
						<span class="label label-success">1</span>
					</h5>

					<button type="button" id="dlcsv" class="btn btn-default" ng-click="aim.download()"
					ng-disabled="">
						<span class="glyphicon glyphicon-save"></span>
						カテゴリ別目標設定シートをダウンロード
						<span class="label label-warning left_10px">csv</span>
					</button>
				</div>

				<div class="controls list-group-item step step_upload">
					<h5 class="bottom_10px">
						<span class="label label-success">2</span>
						 編集済の設定シートを指定
					</h5>

					<div class="input-wrap">
						<ss-input-file ng-model="aim.models.category.file" button-value="'選択'">
					</div>

					<button type="button" class="btn btn-xs btn-default" ng-click="aim.clear()">クリア</button>
				</div>
			</div>
		</div>

		<div class="alert alert-warning annotation">
			選択中のカテゴリ × 表示項目に対して、CSVファイルで目標設定を登録できます。<br>
			すでに登録済みのデータを削除したい場合は、該当セルを空白にした設定シートで上書き保存してください。<br>
			<br>
			<span class="em">※ 編集済の設定シートを選択後、必ずテンプレートを保存してください。<br>
			※ テンプレート保存するまで設定は反映されません。</span>
		</div>
	</div>
	<!-- / -->

</div>
