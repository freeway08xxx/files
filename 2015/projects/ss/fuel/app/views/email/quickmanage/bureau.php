<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<style type="text/css">
</style>
<body>
<br>
<h1>QuickManage 進捗レポート(局別)</h1>
<br>
<br>
<?= $bureau_name; ?> 様
<br>
<br>
<br>
おつかれさまです。新SearchSuiteです。
<br>
<br>
最新の進捗レポートを送付いたします。
<br>
※実績金額は定価を含む(20%)
<br>
<br>
<? $remain_day = date("t") - date("d", strtotime("-1 day")); ?>
【全体】
<br>
<table style="table-layout:fixed; width:1000px; color:#000000; font-weight:normal; border-collapse:collapse; border:solid #cccccc 1px; font-size:1em;">
	<tr>
		<th bgcolor="#ffa500">局</th>
		<th bgcolor="#ffa500">目標</th>
		<th bgcolor="#ffa500">実績</th>
		<th bgcolor="#ffa500">着地</th>
		<th bgcolor="#ffa500">達成率</th>
		<th bgcolor="#ffa500">昨日配信</th>
		<th bgcolor="#ffa500">７日間平均</th>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right; white-space:nowrap;"><?= $bureau_name; ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($bureau_summary_report[$bureau_id.":"]["cl_aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($bureau_summary_report[$bureau_id.":"]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($bureau_forecast_list[$bureau_id.":"]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($bureau_summary_report[$bureau_id.":"]["cl_aim_budget"])) { echo "100%"; } else { echo @number_format($bureau_summary_report[$bureau_id.":"]["cost"] / $bureau_summary_report[$bureau_id.":"]["cl_aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($bureau_daily_report[$last_term_date][$bureau_id.":"]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($bureau_forecast_list[$bureau_id.":"]["cost"] - $bureau_summary_report[$bureau_id.":"]["cost"]) / $remain_day); ?></td>
	</tr>
</table>
<br>
<br>
【媒体・プロダクト別】
<br>
<table style="table-layout:fixed; width:1000px; color:#000000; font-weight:normal; border-collapse:collapse; border:solid #cccccc 1px; font-size:1em;">
	<tr>
		<th bgcolor="#ffa500">媒体</th>
		<th bgcolor="#ffa500">プロダクト</th>
		<th bgcolor="#ffa500">目標</th>
		<th bgcolor="#ffa500">実績</th>
		<th bgcolor="#ffa500">着地</th>
		<th bgcolor="#ffa500">達成率</th>
		<th bgcolor="#ffa500">昨日配信</th>
		<th bgcolor="#ffa500">７日間平均</th>
	</tr>
	<tr>
		<?  $summary_key_1 = "1:Search Network:"; ?>
		<?  $summary_key_2 = "1:Display Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;" rowspan="3">Yahoo!</td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;">合計</td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"] + $media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"]); ?></td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$bureau_id.":".$summary_key_1]["cost"] + $media_forecast_list[$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"]) && empty($media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"])) { echo "100%"; } else { echo @number_format(($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"]) / ($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"] + $media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"]) * 100) . "%"; } ?></td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$bureau_id.":".$summary_key_1]["cost"] + $media_daily_report[$last_term_date][$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format((($media_forecast_list[$bureau_id.":".$summary_key_1]["cost"] + $media_forecast_list[$bureau_id.":".$summary_key_2]["cost"]) - ($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"])) / $remain_day); ?></td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;">Search</td>
		<? $summary_key = $bureau_id.":1:Search Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$summary_key]["aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$summary_key]["aim_budget"])) { echo "100%"; } else { echo @number_format($media_summary_report[$summary_key]["cost"] / $media_sumopt_aim_list[$summary_key]["aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($media_forecast_list[$summary_key]["cost"] - $media_summary_report[$summary_key]["cost"]) / $remain_day); ?></td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;">Display</td>
		<? $summary_key = $bureau_id.":1:Display Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$summary_key]["aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$summary_key]["aim_budget"])) { echo "100%"; } else { echo @number_format($media_summary_report[$summary_key]["cost"] / $media_sumopt_aim_list[$summary_key]["aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($media_forecast_list[$summary_key]["cost"] - $media_summary_report[$summary_key]["cost"]) / $remain_day); ?></td>
	</tr>
	<tr>
		<?  $summary_key_1 = "2:Search Network:"; ?>
		<?  $summary_key_2 = "2:Display Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;" rowspan="3">Google</td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;">合計</td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"] + $media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"]); ?></td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$bureau_id.":".$summary_key_1]["cost"] + $media_forecast_list[$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"]) && empty($media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"])) { echo "100%"; } else { echo @number_format(($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"]) / ($media_sumopt_aim_list[$bureau_id.":".$summary_key_1]["aim_budget"] + $media_sumopt_aim_list[$bureau_id.":".$summary_key_2]["aim_budget"]) * 100) . "%"; } ?></td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$bureau_id.":".$summary_key_1]["cost"] + $media_daily_report[$last_term_date][$bureau_id.":".$summary_key_2]["cost"]); ?></td>
		<td bgcolor="f#ffff00" style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format((($media_forecast_list[$bureau_id.":".$summary_key_1]["cost"] + $media_forecast_list[$bureau_id.":".$summary_key_2]["cost"]) - ($media_summary_report[$bureau_id.":".$summary_key_1]["cost"] + $media_summary_report[$bureau_id.":".$summary_key_2]["cost"])) / $remain_day); ?></td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;">Search</td>
		<? $summary_key = $bureau_id.":2:Search Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$summary_key]["aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$summary_key]["aim_budget"])) { echo "100%"; } else { echo @number_format($media_summary_report[$summary_key]["cost"] / $media_sumopt_aim_list[$summary_key]["aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($media_forecast_list[$summary_key]["cost"] - $media_summary_report[$summary_key]["cost"]) / $remain_day); ?></td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;">Display</td>
		<? $summary_key = $bureau_id.":2:Display Network:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$summary_key]["aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$summary_key]["aim_budget"])) { echo "100%"; } else { echo @number_format($media_summary_report[$summary_key]["cost"] / $media_sumopt_aim_list[$summary_key]["aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($media_forecast_list[$summary_key]["cost"] - $media_summary_report[$summary_key]["cost"]) / $remain_day); ?></td>
	</tr>
	<tr>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;" colspan="2">D2C</td>
		<? $summary_key = $bureau_id.":17:--:"; ?>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_sumopt_aim_list[$summary_key]["aim_budget"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_summary_report[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_forecast_list[$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($media_sumopt_aim_list[$summary_key]["aim_budget"])) { echo "100%"; } else { echo @number_format($media_summary_report[$summary_key]["cost"] / $media_sumopt_aim_list[$summary_key]["aim_budget"] * 100) . "%"; } ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($media_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
		<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($media_forecast_list[$summary_key]["cost"] - $media_summary_report[$summary_key]["cost"]) / $remain_day); ?></td>
	</tr>
</table>
<br>
<br>
【担当者別】
<br>
<table style="table-layout:fixed; width:1000px; color:#000000; font-weight:normal; border-collapse:collapse; border:solid #cccccc 1px; font-size:1em;">
	<tr>
		<th bgcolor="#ffa500">担当者</th>
		<th bgcolor="#ffa500">目標</th>
		<th bgcolor="#ffa500">実績</th>
		<th bgcolor="#ffa500">着地</th>
		<th bgcolor="#ffa500">達成率</th>
		<th bgcolor="#ffa500">昨日配信</th>
		<th bgcolor="#ffa500">７日間平均</th>
	</tr>
	<? foreach ($user_summary_report as $user_report) { ?>
		<? if ($user_report["bureau_id"] === $bureau_id) { ?>
			<? $summary_key = $user_report["summary_key"]; ?>
			<tr>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= $user_report["user_name"]; ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($user_report["cl_aim_budget"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($user_report["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($user_forecast_list[$summary_key]["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($user_report["cl_aim_budget"])) { echo "100%"; } else { echo @number_format($user_report["cost"] / $user_report["cl_aim_budget"] * 100) . "%"; } ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($user_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($user_forecast_list[$summary_key]["cost"] - $user_report["cost"]) / $remain_day); ?></td>
			</tr>
		<? } ?>
	<? } ?>
</table>
<br>
<br>
【クライアント別】
<br>
<table style="table-layout:fixed; width:1000px; color:#000000; font-weight:normal; border-collapse:collapse; border:solid #cccccc 1px; font-size:1em;">
	<tr>
		<th bgcolor="#ffa500">クライアント</th>
		<th bgcolor="#ffa500">目標</th>
		<th bgcolor="#ffa500">実績</th>
		<th bgcolor="#ffa500">着地</th>
		<th bgcolor="#ffa500">達成率</th>
		<th bgcolor="#ffa500">昨日配信</th>
		<th bgcolor="#ffa500">７日間平均</th>
	</tr>
	<? foreach ($client_summary_report as $client_report) { ?>
		<? if ($client_report["bureau_id"] === $bureau_id) { ?>
			<? $summary_key = $client_report["summary_key"]; ?>
			<tr>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= $client_report["client_name"]; ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($client_report["cl_aim_budget"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($client_report["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($client_forecast_list[$summary_key]["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><? if (empty($client_report["cl_aim_budget"])) { echo "100%"; } else { echo @number_format($client_report["cost"] / $client_report["cl_aim_budget"] * 100) . "%"; } ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format($client_daily_report[$last_term_date][$summary_key]["cost"]); ?></td>
				<td style="padding:10px; border:solid #cccccc 1px; text-align:right;"><?= @number_format(($client_forecast_list[$summary_key]["cost"] - $client_report["cost"]) / $remain_day); ?></td>
			</tr>
		<? } ?>
	<? } ?>
</table>
<br>
<br>
<font color="red">クライアント担当者が異なる場合は、<a href="http://sem-portal.cyberagent.co.jp/sem/mora/entry_client_charge.php">こちら</a>より担当変更してください。
<br>
数値精度を高める為にも徹底ください。
<br>
</font>
<br>
<br>
詳細につきましては、QuickManageにて確認してください。
<br>
<a href="<?= $url ?>"><?= $url ?></a>
<br>
<br>
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>
