<input type="hidden" name="summary_unit_count" id="summary_unit_count" value="<?= count($summary_elem_list) + 1 ?>" />

<div ng-controller="ReportViewCtrl">

	<div class="block-title clearfix">
		<h4 class="title pull-left">日別推移レポート</h4>
		<button type="button" class="btn btn-link btn-sm pull-left backlink" ng-click="back()"><span class="glyphicon glyphicon-chevron-left"></span> 戻る</button>
	</div>

	<div class="table-wrap">

		<table class="reportview-table report-table table table-bordered table-condensed" id="reportview_table">
			<!-- タイトル -->
			<thead>
				<tr>
					<th class="text-center">No</th>
					<!-- サマリ単位 -->
					<? foreach ($summary_elem_list as $key => $value) {
						if ($value === 'ALL') { ?>
							<th class="text-center">指標</th>
						<? } ?>
						<th class="text-center"><?= $value; ?></th>
					<? } ?>
					<!-- 集計期間 -->
					<? foreach ($term_date_list as $key => $value) { ?>
						<th class="text-center"><?= $value; ?></th>
					<? } ?>
				</tr>
			</thead>

			<!-- 合計実績出力 -->
			<tbody>
				<tr class="sum">
					<td class="summary-unit text-center">合計</td>
					<!-- サマリ単位 -->
					<? foreach ($summary_elem_list as $key => $value) { ?>
						<? if (isset($FORM_total_report[$key])) { ?>
							<td class="summary-unit text-right"><?= $FORM_total_report[$key]; ?></td>
						<!-- 目標以外は出力しない -->
						<? } else { ?>
							<? if ($key !== "all" && $key !== "forecast") { ?>
								<td class="summary-unit text-center"><?= "-"; ?></td>
							<? } ?>
						<? } ?>
					<? } ?>
					<!-- 数値ラベル出力 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<? foreach ($report_elem_list as $key => $value) { ?>
								<tr>
									<td class="index"><?= $value; ?></td>
								</tr>
							<? } ?>
						</table>
					</td>
					<!-- 合計実績出力項目 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<? foreach ($report_elem_list as $key => $value) { ?>
								</tr>
									<? if (isset($FORM_total_report[$key])) {
										$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($FORM_total_report[$key], $key);
										$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
										 ?>
										<td class="text-right"><?= $num ?></td>
									<? } ?>
								</tr>
							<? } ?>
						</table>
					</td>
					<!-- 着地予想出力 -->
					<? if ($FORM_use_forecast) { ?>
						<td class="report-value">
							<table class="report-value-table table table-hover table-striped">
								<? foreach ($report_elem_list as $key => $value) { ?>
									<tr>
										<? if (isset($FORM_forecast_list["total"][$key])) {
											$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($FORM_forecast_list["total"][$key], $key);
											$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
											 ?>
											<td class="text-right"><?= $num ?></td>
										<? } ?>
									</tr>
								<? } ?>
							</table>
						</td>
					<? } ?>
					<!-- 日別合計実績出力項目 -->
					<? foreach($FORM_term_date_list as $term_date) { ?>
						<td class="report-value">
							<table class="report-value-table table table-hover table-striped">
								<? foreach ($report_elem_list as $key => $value) { ?>
									<tr>
										<? if (isset($FORM_daily_report_list[$term_date]["ALL"][$key])) {
											$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($FORM_daily_report_list[$term_date]["ALL"][$key], $key);
											$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
											 ?>
											<td class="text-right"><?= $num ?></td>
										<? } ?>
									</tr>
								<? } ?>
							</table>
						</td>
					<? } ?>
				</tr>

				<!-- サマリ実績出力 -->
				<? $i = 1; ?>
				<? foreach ($FORM_summary_report_list as $summary_report) { ?>
				<tr>
					<td class="summary-unit text-center"><?= $i; ?></td>
					<!-- サマリ単位 -->
					<? foreach ($summary_elem_list as $key => $value) { ?>
						<!-- サマリオプションが業種・媒体・プロダクトの場合 -->
						<? if ($key === "cl_aim_budget") { ?>
							<? if ($FORM_use_business_type_summary || $FORM_use_media_summary || $FORM_use_product_summary) { ?>
								<? if (isset($FORM_sumopt_aim_list[$summary_report["summary_key"]]["aim_budget"])) { ?>
									<td class="summary-unit text-right"><?= $FORM_sumopt_aim_list[$summary_report["summary_key"]]["aim_budget"]; ?></td>
								<? } ?>
								<? continue; ?>
							<? } ?>
						<? } ?>
						<? if (isset($summary_report[$key])) { ?>
							<!-- 局名リンク押下時、該当局の担当別レポート表示 -->
							<? if ($key === "bureau_name") { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"]     = "user"; ?>
								<? $param["bureau_id_list"]   = isset($summary_report["bureau_id"]) ? array($summary_report["bureau_id"]) : array(); ?>
								<? $param["search_user_name"] = null; ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= $summary_report[$key]; ?></a></td>
							<!-- 担当名リンク押下時、該当所属局のクライアント別レポート表示 -->
							<? } elseif ($key === "user_name") { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"]     = "client"; ?>
								<? $param["bureau_id_list"]   = array(); ?>
								<? $param["search_user_name"] = $summary_report[$key]; ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= $summary_report[$key]; ?></a></td>
							<? } else { ?>
								<td class="summary-unit"><?= $summary_report[$key]; ?></td>
							<? } ?>
						<!-- 目標出力しない場合 -->
						<? } else { ?>
							<? if ($key !== "all" && $key !== "forecast") { ?>
								<td class="summary-unit text-right"><?= $key; ?></td>
							<? } ?>
						<? } ?>
					<? } ?>

					<!-- 数値ラベル出力 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<? foreach ($report_elem_list as $key => $value) { ?>
								<tr>
									<td class="index"><?= $value; ?></td>
								</tr>
							<? } ?>
						</table>
					</td>

					<!-- 合計実績出力項目 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<? foreach ($report_elem_list as $key => $value) { ?>
								<tr>
									<? if (isset($summary_report[$key])) {
										$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($summary_report[$key], $key);
										$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
										 ?>
										<td class="text-right"><?= $num ?></td>
									<? } ?>
								</tr>
							<? } ?>
						</table>
					</td>
					<!-- 着地予想出力 -->
					<? if ($FORM_use_forecast) { ?>
						<td class="report-value">
							<table class="report-value-table table table-hover table-striped">
								<? foreach ($report_elem_list as $key => $value) { ?>
									<tr>
										<? if (isset($FORM_forecast_list[$summary_report["summary_key"]][$key])) {
											$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($FORM_forecast_list[$summary_report["summary_key"]][$key], $key);
											$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
											 ?>
											<td class="text-right"><?= $num ?></td>
										<? } ?>
									</tr>
								<? } ?>
							</table>
						</td>
					<? } ?>
					<!-- 日別実績出力項目 -->
					<? foreach($FORM_term_date_list as $term_date) { ?>
						<td class="report-value">
							<table class="report-value-table table table-hover table-striped">
								<? foreach ($report_elem_list as $key => $value) { ?>
									<tr>
										<? if (isset($FORM_daily_report_list[$term_date][$summary_report["summary_key"]][$key])) {
											$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($FORM_daily_report_list[$term_date][$summary_report["summary_key"]][$key], $key);
											$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
											 ?>
											<td class="text-right"><?= $num ?></td>
										<? } ?>
									</tr>
								<? } ?>
							</table>
						</td>
					<? } ?>
					<? $i++; ?>
				</tr>
				<? } ?>
			</tbody>
		</table>

	</div>
</div>
