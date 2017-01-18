<small class="caution pull-right">※現在有効のみ</small>

<h4 class="title">目標設定一覧（今月のみ）</h4>

<!-- 目標設定一覧 -->
<? if (!empty($DB_aim_list)) { ?>
	<table class="report-table table table-bordered table-hover table-striped table-condensed" id="aim_table">
		<thead>
		<tr class="table-label">
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_CLIENT; ?>">クライアント</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_TARGET_YM; ?>">対象年月</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_Y_S; ?>">Yahoo!</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_Y_D; ?>">YDN</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_G_S; ?>">Google</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_G_D; ?>">GDN</span></td>
			<td class=""><span class="tooltips" data-html="true" data-tooltip="<?= EXPLAIN_AIM_D2C; ?>">D2C</span></td>
		</tr>
		</thead>
		<tbody>
		<? foreach ($DB_aim_list as $key => $value) { ?>
			<tr>
				<td class="table-default"><?= "[" . $value["client_id"] . "] " . $value["client_name"]; ?></td>
				<td class="table-default"><?= date("Y年m月", strtotime($value["target_ym"])); ?></td>
				<td class="table-default"><?= $value["Y_S_aim_budget"] . "円"; ?></td>
				<td class="table-default"><?= $value["Y_D_aim_budget"] . "円"; ?></td>
				<td class="table-default"><?= $value["G_S_aim_budget"] . "円"; ?></td>
				<td class="table-default"><?= $value["G_D_aim_budget"] . "円"; ?></td>
				<td class="table-default"><?= $value["D2C_aim_budget"] . "円"; ?></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
<? } else { ?>
	目標設定が存在しません。
<? } ?>