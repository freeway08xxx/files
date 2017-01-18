<small class="caution pull-right">※現在有効のみ</small>

<h4 class="title">値引設定一覧（今月のみ）</h4>

<!-- 値引設定一覧 -->
<? if (!empty($DB_discount_list)) { ?>
	<table class="report-table table table-bordered table-hover table-striped table-condensed" id="discount_table">
		<thead>
		<tr class="table-label">
			<td class=""><span class="tooltips" data-tooltip="<?= EXPLAIN_DISCOUNT_MEDIA; ?>">媒体</span></td>
			<td class=""><span class="tooltips" data-tooltip="<?= EXPLAIN_DISCOUNT_ACCOUNT; ?>">アカウント</span></td>
			<td class=""><span class="tooltips" data-tooltip="<?= EXPLAIN_DISCOUNT_TARGET_YM; ?>">対象年月</span></td>
			<td class=""><span class="tooltips" data-tooltip="<?= EXPLAIN_DISCOUNT_DISCOUNT; ?>">値引</span></td>
		</tr>
		</thead>
		<tbody>
		<? foreach ($DB_discount_list as $key => $value) { ?>
			<tr>
				<td class="table-default"><?= $GLOBALS["all_media_id_list"][$value["media_id"]]; ?></td>
				<td class="table-default"><?= "[" . $value["account_id"] . "] " . $value["account_name"]; ?></td>
				<td class="table-default"><?= date("Y年m月", strtotime($value["target_ym"])); ?></td>
				<td class="table-default"><? if ($value["discount_type"] === "1") { echo $value["discount_rate"] . "(%)"; } else { echo $value["discount_rate"] . "円"; } ?></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
<? } else { ?>
	値引設定が存在しません。
<? } ?>