<input type="hidden" name="summary_unit_count" id="summary_unit_count" value="<?= count($summary_elem_list) + 1 ?>" />


<div ng-controller="ReportViewCtrl">

	<div class="block-title clearfix">
		<h4 class="title pull-left">期間比較レポート</h4>
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
					<? for ($term=$FORM_term_count; $term>=1; $term--) { ?>
						<? $term_date = $FORM_date_list[$term]["start_ymd"] . "-" . $FORM_date_list[$term]["end_ymd"]; ?>

						<? if ($term !== $FORM_term_count) { ?>
							<th class="text-center"><?= $term_date; ?></th>
							<th class="text-center">差分(％)</th>
							<th class="text-center">差分(実数)</th>
						<? } else { ?>
							<th class="text-center"><?= $term_date; ?></th>
						<? } ?>
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
							<? if ($key !== "all") { ?>
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
								<tr>
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
					<!-- 期間別合計実績出力項目 -->
					<? for ($term=$FORM_term_count; $term>=1; $term--) { ?>
						<td class="report-value">
							<table class="report-value-table table table-hover table-striped">
								<? $total_term_report = $FORM_report_list[$term]["total"]; ?>
								<? $last_total_term_report = array(); ?>
								<? if (isset($FORM_report_list[$term + 1])) { ?>
									<? $last_total_term_report = $FORM_report_list[$term + 1]["total"]; ?>
								<? } ?>

								<? if ($term !== $FORM_term_count) { ?>
									<? foreach ($report_elem_list as $key => $value) { ?>
										<tr>
											<? if (isset($total_term_report[$key])) {
												$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($total_term_report[$key], $key);
												$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
												 ?>
												<td class="text-right"><?= $num ?></td>
											<? } ?>
										</tr>
									<? } ?>
									</table>
									</td>
									<td class="report-value">
									<table class="report-value-table table table-hover table-striped">
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
												<? if (isset($total_term_report[$key])) { ?>
													<td class="text-right"><?= \Util_QuickManage_ReportCommon::makeLastTermDiffRate($total_term_report, $last_total_term_report, $key) / 100; ?></td>
												<? } ?>
											</tr>
										<? } ?>
									</table>
									</td>
									<td class="report-value">
									<table class="report-value-table table table-hover table-striped">
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
												<? if (isset($total_term_report[$key])) { ?>
													<td class="text-right"><?= \Util_QuickManage_ReportCommon::makeLastTermDiffRate($total_term_report, $last_total_term_report, $key, "realnumber"); ?></td>
												<? } ?>
											</tr>
										<? } ?>
								<? } else { ?>
									<? foreach ($report_elem_list as $key => $value) { ?>
										<tr>
											<? if (isset($total_term_report[$key])) {
												$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($total_term_report[$key], $key);
												$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
												 ?>
												<td class="text-right"><?= $num ?></td>
											<? } ?>
										</tr>
									<? } ?>
								<? } ?>
							</table>
						</td>
					<? } ?>
				</tr>

				<!-- サマリ実績出力 -->
				<? $i = 1; ?>
				<? foreach ($FORM_report_list[1]["summary"] as $summary_key => $summary_report) { ?>
					<? $all_term_summary_report = $FORM_all_term_summary_list[$summary_key]; ?>
					<tr>
						<td class="summary-unit text-center"><?= $i; ?></td>
						<!-- サマリ単位 -->
						<? foreach ($summary_elem_list as $key => $value) { ?>
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
										<? if (isset($all_term_summary_report[$key])) {
											$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($all_term_summary_report[$key], $key);
											$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
											 ?>
											<td class="text-right"><?= $num ?></td>
										<? } ?>
										</tr>
								<? } ?>
							</table>
						</td>
						<!-- 期間別実績出力項目 -->
						<? for ($term=$FORM_term_count; $term>=1; $term--) { ?>
							<td class="report-value">
								<table class="report-value-table table table-hover table-striped">
									<? $summary_term_report = $summary_report[$term]; ?>
									<? $last_summary_term_report = array(); ?>
									<? if (isset($summary_report[$term + 1])) { ?>
										<? $last_summary_term_report = $summary_report[$term + 1]; ?>
									<? } ?>

									<? if ($term !== $FORM_term_count) { ?>
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
												<? if (isset($summary_term_report[$key])) {
													$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($summary_term_report[$key], $key);
													$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
													 ?>
													<td class="text-right"><?= $num ?></td>
												<? } ?>
											</tr>
										<? } ?>
										</table>
										</td>
										<td class="report-value">
										<table class="report-value-table table table-hover table-striped">
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
												<? if (isset($total_term_report[$key])) { ?>
													<td class="text-right"><?= $summary_report[$term."_percent"][$key] / 100; ?></td>
												<? } ?>
											</tr>
										<? } ?>
										</table>
										</td>
										<td class="report-value">
										<table class="report-value-table table table-hover table-striped">
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
											<? if (isset($total_term_report[$key])) { ?>
												<td class="text-right"><?= $summary_report[$term."_realnumber"][$key] / 100; ?></td>
											<? } ?>
											</tr>
										<? } ?>
									<? } else { ?>
										<? foreach ($report_elem_list as $key => $value) { ?>
											<tr>
												<? if (isset($summary_term_report[$key])) {
													$num = \Util_QuickManage_ReportCommon::makeRealNumberRateOutPut($summary_term_report[$key], $key);
													$num = \Util_QuickManage_ReportCommon::addFormatSymbol($num, $key);
													 ?>
													<td class="text-right"><?= $num ?></td>
												<? } ?>
											</tr>
										<? } ?>
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
