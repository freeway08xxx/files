<small class="caution pull-right">※最新100件のみ</small>
<? if (!empty($DB_history_list)) { ?>
	<table class="report-table history-table table table-bordered table-hover table-striped table-condensed" id="history_table">
		<thead>
		<tr class="table-label">
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_REPORT_NAME; ?>">レポート名</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_CLIENT_NAME; ?>">クライアント名</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_TEMPLATE_INFO; ?>">テンプレート設定情報</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_EXPORT_STATUS; ?>">ステータス</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_EXPORT_TIME; ?>">出力時間(分)</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_REPORT_TERM; ?>">集計期間</span></td>
			<td><span ss-tooltip data-html="true" title="<?= EXPLAIN_HISTORY_CREATE_USER; ?>">作成者</span></td>
			<td><span ss-tooltip class="text-right" data-html="true" title="<?= EXPLAIN_HISTORY_CREATE_DATETIME; ?>">開始日時</span></td>
			<td><span ss-tooltip class="text-right" data-html="true" title="<?= EXPLAIN_HISTORY_UPDATE_DATETIME; ?>">完了日時</span></td>
		</tr>
		</thead>
		<tbody>
		<? foreach ($DB_history_list as $key => $value) { ?>
			<? $id = (isset($value["file_path"])) ? $value["id"] : null; ?>
			<tr ng-click="download(<?= $id; ?>)">
				<td class="table-default">
					<? if (isset($value["file_path"])) { ?>
						<a href="/sem/new/axis/export/download/<?= $value["id"]; ?>">
							<i class="glyphicon glyphicon-file"></i> <?= $value["report_name"]; ?>
						</a>
					<? } else { ?>
						<?= $value["report_name"]; ?>
					<? } ?>
				</td>
				<td class="table-default text-right"><?= $value["company_name"]; ?><? if (!empty($value["client_name"])) echo "//" . $value["client_name"]; ?></td>
				<td class="table-default">
					<? if (isset($value["template_id"])) { ?>
						<div class="js-mod-template" data-item-id="<?= $value["template_id"]; ?>"><a href="#" class="switch">テンプレート設定情報</a></div>
					<? } else { ?>
						--
					<? } ?>
				</td>
				<td class="table-default text-center"><span class="label-<?= $GLOBALS["export_status_list"][$value["status_id"]]["label"]; ?>"><?= $GLOBALS["export_status_list"][$value["status_id"]]["name"]; ?></span></td>
				<td class="table-default text-right"><?= $value["export_time"]; ?></td>
				<td class="table-default"><?= $GLOBALS["report_term_list"]["all"][$value["report_term"]]; ?></td>
				<td class="table-default"><?= $value["user_name"]; ?></td>
				<td class="table-default text-right"><?= $value["created_at"]; ?></td>
				<td class="table-default text-right"><?= $value["updated_at"]; ?></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
<? } else { ?>
	作成したレポートが存在しません。
<? } ?>
