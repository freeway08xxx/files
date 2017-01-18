<input type="hidden" name="summary_unit_count" id="summary_unit_count" value="<?= count($summary_elem_list) + 1; ?>" />

<div ng-controller="ReportViewCtrl">

	<div class="block-title clearfix">
		<h4 class="title pull-left">期間比較レポート</h4>
		<button type="button" class="btn btn-link btn-sm pull-left backlink" ng-click="back()"><span class="glyphicon glyphicon-chevron-left"></span> 戻る</button>
	</div>

	<div class="table-wrap">

		<table class="reportview-table report-table table table-bordered table-hover table-striped table-condensed" id="reportview_table">
			<!-- ヘッダ出力 -->
			<thead>
				<tr>
					<th class="text-center">No</th>
					<? foreach ($report["report_header"] as $tmp) { ?>
						<th class="text-center"><?= $tmp; ?></th>
					<? } ?>
				</tr>
			</thead>

			<!-- 実績出力 -->
			<tbody>
				<!-- 合計実績出力 -->
				<tr class="sum">
					<!-- サマリ項目は空 -->
					<td class="summary-unit">合計</td>
					<? for ($i = 0; $i < count($summary_elem_list); $i++) { ?>
						<td class="summary-unit"></td>
					<? } ?>
					<!-- 出力項目 -->
					<? for ($j = $i; $j < count($report["total_report"]); $j++) { ?>
						<td class="text-right"><?= isset($report["total_report"][$j]) ? $report["total_report"][$j] : "--"; ?></td>
					<? } ?>
				</tr>
				<!-- サマリ別合計実績出力 -->
				<? foreach ($report["total_summary_report_list"] as $total_summary_report) { ?>
					<tr>
						<!-- サマリ項目 -->
						<td class="summary-unit">サマリ別</td>
						<? for ($i = 0; $i < count($summary_elem_list); $i++) { ?>
							<!-- アカウントの場合 -->
							<? if ($i === $link_elem_list["account_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "campaign"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $total_summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($total_summary_report[$i]) ? $total_summary_report[$i] : "--"; ?></td>
							<!-- キャンペーンの場合 -->
							<? } elseif ($i === $link_elem_list["campaign_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "ad_group"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $total_summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($total_summary_report[$i]) ? $total_summary_report[$i] : "--"; ?></td>
							<!-- 広告グループの場合 -->
							<? } elseif ($i === $link_elem_list["ad_group_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "keyword"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $total_summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($total_summary_report[$i]) ? $total_summary_report[$i] : "--"; ?></td>
							<? } else { ?>
								<td class="summary-unit"><?= isset($total_summary_report[$i]) ? $total_summary_report[$i] : "--"; ?></td>
							<? } ?>
						<? } ?>
						<!-- 出力項目 -->
						<? for ($j = $i; $j < count($total_summary_report); $j++) { ?>
							<td class="text-right"><?= isset($total_summary_report[$j]) ? $total_summary_report[$j] : "--"; ?></td>
						<? } ?>
					</tr>
				<? } ?>
				<!-- 期間別合計実績出力 -->
				<? foreach ($report["term_total_report_list"] as $term_total_report) { ?>
					<tr>
						<!-- サマリ項目は空 -->
						<td class="summary-unit">期間別</td>
						<? for ($i = 0; $i < count($summary_elem_list); $i++) { ?>
							<td class="summary-unit"><?= isset($term_total_report[$i]) ? $term_total_report[$i] : "--"; ?></td>
						<? } ?>
						<!-- 出力項目 -->
						<? for ($j = $i; $j < count($term_total_report); $j++) { ?>
							<td class="text-right"><?= isset($term_total_report[$j]) ? $term_total_report[$j] : "--"; ?></td>
						<? } ?>
					</tr>
				<? } ?>
				<? $cnt = 1; ?>
				<!-- サマリ実績出力 -->
				<? foreach ($report["summary_report_list"] as $summary_report) { ?>
					<tr>
						<!-- サマリ項目 -->
						<td class="summary-unit"><?= $cnt; ?></td>
						<? for ($i = 0; $i < count($summary_elem_list); $i++) { ?>
							<!-- アカウントの場合 -->
							<? if ($i === $link_elem_list["account_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "campaign"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($summary_report[$i]) ? $summary_report[$i] : "--"; ?></td>
							<!-- キャンペーンの場合 -->
							<? } elseif ($i === $link_elem_list["campaign_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "ad_group"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($summary_report[$i]) ? $summary_report[$i] : "--"; ?></td>
							<!-- 広告グループの場合 -->
							<? } elseif ($i === $link_elem_list["ad_group_name"]) { ?>
								<? $param = Input::param(); ?>
								<? $param["summary_type"] = "keyword"; ?>
								<? unset($param["ssAccount"]); ?>
								<? $param["ssAccount"][] = json_encode(array("account_id" => $summary_report[$link_elem_list["account_name"]-1])); ?>
								<td class="summary-unit"><a href="<?= str_replace(Uri::base(), "/", Uri::update_query_string($param, Uri::main())); ?>"><?= isset($summary_report[$i]) ? $summary_report[$i] : "--"; ?></td>
							<? } else { ?>
								<td class="summary-unit"><?= isset($summary_report[$i]) ? $summary_report[$i] : "--"; ?></td>
							<? } ?>
						<? } ?>
						<!-- 出力項目 -->
						<? for ($j = $i; $j < count($summary_report); $j++) { ?>
							<td class="text-right"><?= isset($summary_report[$j]) ? $summary_report[$j] : "--"; ?></td>
						<? } ?>
					</tr>
					<? $cnt++; ?>
				<? } ?>
			</tbody>
		</table>

	</div>
</div>
