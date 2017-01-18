<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<style type="text/css">
</style>
<body>
<br>
<h1>QuickManage 進捗レポート(担当別)</h1>
<br>
<br>
<?= $user_name; ?> 様
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
		<? if ($client_report["user_id"] === $user_id) { ?>
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
