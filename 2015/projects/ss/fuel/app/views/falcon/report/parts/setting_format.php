<div class="form-block list-group">
	<div class="controls list-group-item">
		<div class="row">
			<div class="col-sm-4 col-md-3 col-lg-3">
				<h5 class="control-label">レポート種別</h5>

				<div class="btn-group report-type">
					<? foreach ($report_type_list as $key => $value) { ?>
						<label class="btn btn-sm btn-default" ng-model="format.models.report_type"
						ng-click="format.models.termdate.method.reset(); report.setAvailableSheets('<?= $key ?>')"
						btn-radio="'<?= $key; ?>'">
							<?= $value; ?>
						</label>
					<? } ?>
				</div>
			</div>

			<div class="col-sm-5 col-md-4 col-lg-4">
				<h5 class="control-label">サマリ単位</h5>

				<div class="btn-group summary-type">
					<? foreach ($report_category_type_list as $key => $value) { ?>
						<label class="btn btn-sm btn-default" ng-model="format.models.summary_type"
						ng-click="format.chgCommonSummary('<?= $key; ?>')"
						btn-radio="'<?= $key; ?>'">
							<?= $value; ?>
						</label>
					<? } ?>
				</div>

				<div class="btn-group summary-type category-element"
				ng-show="format.models.summary_type === 'category_id'">
					<? foreach ($report_category_element_list as $key => $value) { ?>
						<label class="btn btn-xs btn-default" ng-model="format.models.category_element"
						btn-radio="'<?= $key; ?>'">
							<?= $value; ?>
						</label>
					<? } ?>
				</div>
			</div>

			<div class="col-sm-3 col-md-4 col-lg-3">
				<h5 class="control-label">デバイス別出力</h5>

				<select class="form-control input-sm" name="device_type"
				 ng-model="format.models.device_type"
				 ng-click="report.setAvailableSheets()">
					<option value="0">無し（セパレート型）</option>
					<option value="1">PC / TAB / SP</option>
					<option value="2">PC+TAB / SP</option>
					<option value="3">PC / TAB+SP</option>
				</select>
			</div>
		</div>
	</div>

	<ss-termdate ng-model="format.models.termdate"></ss-termdate>

	<div class="controls list-group-item">
		<div class="row">

			<div class="col-sm-2">
				<h5 class="control-label">媒体費 <span class="label label-warning">必須</span></h5>

				<div class="input-group input-group-sm">
					<input type="number" class="form-control" name="media_cost"
					 ng-model="format.models.media_cost" ng-init="format.models.media_cost=<?= MEDIA_COST_RATE ?>"
					 max="100" min="0" ng-required="true" required />
					<span class="input-group-addon">%</span>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="form-block list-group">
	<div class="controls list-group-item">
		<h5 class="control-label">出力レポート名</h5>

		<div class="row">
			<div class="col-md-7">
				<div class="input-group">
					<input class="form-control" type="text" name="report_name" ng-model="format.models.report_name" placeholder="レポート名（例：2014年サンプルクライアント日別推移レポート）">
					<span class="input-group-addon">.xlsx</span>
				</div>
			</div>

			<div class="col-md-5 is-send-mail">
				<label><input type="checkbox" ng-model="format.models.is_send_mail" value="1"> レポート作成完了後、通知メールを受け取る</label>
			</div>
		</div>
	</div>
</div>
