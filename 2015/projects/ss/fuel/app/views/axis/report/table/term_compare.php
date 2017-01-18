<input type="hidden" name="summary_unit_count" id="summary_unit_count" value="<?= count($summary_elem_list) + 1; ?>" />

<div ng-controller="ReportViewCtrl">

	<div class="block-title clearfix">
		<h4 class="title pull-left">期間比較レポート</h4>
		<button type="button" class="btn btn-link btn-sm pull-left backlink" ng-click="back()"><span class="glyphicon glyphicon-chevron-left"></span> 戻る</button>
	</div>

	<div class="table-wrap">

		<table class="reportview-table report-table table table-bordered table-condensed" id="reportview_table">
			<!-- ヘッダ出力 -->
			<thead>
				<tr>
					<!-- サマリ項目 -->
					<th class="text-center">No</th>
					<? foreach ($summary_elem_list as $key => $value) { ?>
						<? if ($key !== "term_view") { ?>
							<th class="text-center"><?= $value; ?></th>
						<? } ?>
					<? } ?>
					<th class="text-center">指標</th>
					<th class="text-center">ALL</th>
					<? foreach ($date_list as $tmp) { ?>
						<th class="text-center"><?= $tmp["start_ymd"] . " - " . $tmp["end_ymd"]; ?></th>
					<? } ?>
				</tr>
			</thead>

			<!-- 実績出力 -->
			<tbody>
				<!-- 合計実績出力 -->
				<tr class="sum">
					<td class="summary-unit">合計</td>
					<!-- サマリ項目は空 -->
					<? foreach ($summary_elem_list as $key => $value) { ?>
						<? if ($key !== "term_view") { ?>
							<td class="summary-unit"></td>
						<? } ?>
					<? } ?>
					<!-- 出力項目 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<? foreach ($report_elem_list as $key => $value) { ?>
								<tr>
									<td class="index"><?= $value; ?></td>
								</tr>
							<? } ?>
						</table>
					</td>
					<!-- 合計実績出力 -->
					<td class="report-value">
						<table class="report-value-table table table-hover table-striped">
							<!-- 出力項目毎にループ -->
							<? for ($j = count($summary_elem_list); $j < count($report["total_report"]); $j++) { ?>
								<tr>
									<td class="text-right"><?= isset($report["total_report"][$j]) ? $report["total_report"][$j] : "--"; ?></td>
								</tr>
							<? } ?>
						</table>
					</td>
					<!-- 期間別合計実績出力 -->
					<? foreach ($report["term_total_report_list"] as $term_total_report) { ?>
					    <td class="report-value">
					        <table class="report-value-table table table-hover table-striped">
					            <!-- 出力項目毎にループ -->
					            <? for ($j = count($summary_elem_list); $j < count($term_total_report); $j++) { ?>
					                <tr>
					                    <td class="text-right"><?= isset($term_total_report[$j]) ? $term_total_report[$j] : "--"; ?></td>
					                </tr>
					            <? } ?>
					        </table>
					    </td>
					<? } ?>
				</tr>
				<!-- サマリ実績出力 -->
				<? $cnt = 1; ?>
				<? for ($i = 0; $i < count($report["summary_report_list"]); $i++) { ?>
					<!-- 出力期間の先頭 -->
					<? if ($i % $term_count === 0) { ?>
						<tr class="num">
							<!-- サマリ項目 -->
							<td class="summary-unit"><?= $cnt; ?></td>
							<!-- term_view 以外 -->
							<? for ($j = 0; $j < count($summary_elem_list) - 1; $j++) { ?>
								<!-- アカウントの場合 -->
								<? if ($j === $link_elem_list["account_name"]) { ?>
									<? $param = Input::param(); ?>
									<? $param["summary_type"] = "campaign"; ?>
									<? unset($param["ssAccount"]); ?>
									<? $param["ssAccount"][] = json_encode(array("account_id" => $report["summary_report_list"][$i][$link_elem_list["account_name"]-1])); ?>
									<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($report["summary_report_list"][$i][$j]) ? $report["summary_report_list"][$i][$j] : "--"; ?></td>
								<!-- キャンペーンの場合 -->
								<? } elseif ($j === $link_elem_list["campaign_name"]) { ?>
									<? $param = Input::param(); ?>
									<? $param["summary_type"] = "ad_group"; ?>
									<? unset($param["ssAccount"]); ?>
									<? $param["ssAccount"][] = json_encode(array("account_id" => $report["summary_report_list"][$i][$link_elem_list["account_name"]-1])); ?>
									<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($report["summary_report_list"][$i][$j]) ? $report["summary_report_list"][$i][$j] : "--"; ?></td>
								<!-- 広告グループの場合 -->
								<? } elseif ($j === $link_elem_list["ad_group_name"]) { ?>
									<? $param = Input::param(); ?>
									<? $param["summary_type"] = "keyword"; ?>
									<? unset($param["ssAccount"]); ?>
									<? $param["ssAccount"][] = json_encode(array("account_id" => $report["summary_report_list"][$i][$link_elem_list["account_name"]-1])); ?>
									<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($report["summary_report_list"][$i][$j]) ? $report["summary_report_list"][$i][$j] : "--"; ?></td>
								<? } else { ?>
									<td class="summary-unit"><?= isset($report["summary_report_list"][$i][$j]) ? $report["summary_report_list"][$i][$j] : "--"; ?></td>
								<? } ?>
							<? } ?>
							<!-- 出力項目 -->
							<td class="report-value">
								<table class="report-value-table table table-hover table-striped">
									<? foreach ($report_elem_list as $key => $value) { ?>
										<tr>
											<td class="index"><?= $value; ?></td>
										</tr>
									<? } ?>
								</table>
							</td>
							<!-- 合計実績出力 -->
							<td class="report-value">
								<table class="report-value-table table table-hover table-striped">
									<!-- 出力項目毎にループ -->
									<? for ($j = count($summary_elem_list); $j < count($report["total_summary_report_list"][$cnt-1]); $j++) { ?>
										<tr>
											<td class="text-right"><?= isset($report["total_summary_report_list"][$cnt-1][$j]) ? $report["total_summary_report_list"][$cnt-1][$j] : "--"; ?></td>
										</tr>
									<? } ?>
								</table>
							</td>
							<!-- 期間別実績出力 -->
							<? for ($j = $i; $j < $i + $term_count; $j++) { ?>
								<td class="report-value">
									<table class="report-value-table table table-hover table-striped">
										<? for ($k = count($summary_elem_list); $k < count($report["summary_report_list"][$j]); $k++) { ?>
											<tr>
												<td class="text-right"><?= isset($report["summary_report_list"][$j][$k]) ? $report["summary_report_list"][$j][$k] : "--"; ?></td>
											</tr>
										<? } ?>
									</table>
								</td>
							<? } ?>
							<? $cnt++; ?>
						</tr>
					<? } ?>
				<? } ?>
			</tbody>
		</table>

	</div>
</div>
