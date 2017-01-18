<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<style type="text/css">
	h1 {font-size:large;border-left:15px solid #38b032;  border-bottom:2px solid #38b032;  padding:.4em .6em;}
	table{table-layout:fixed; width:300px;color: #000000;font-weight: normal;border-collapse: collapse;border: solid #cccccc 1px;font-size: 1em;}
	th{background-color: #ffa500;}
	td, th{padding: 20px;border: solid #cccccc 1px;text-align: center;}
	th{font-weight: bold;padding: 5px;}
	.bg_red {font-weight: bold; background-color: #ff0000;}
	.bg_yellow {font-weight: bold; ;background-color: #ffff00;}
	.bg_green {font-weight: bold; background-color: #00fa9a;}
	.number { color: #ff0000;text-align:right;font-size: 2em;}
</style>
<body>
<br />
<?
  $user_name = "ご担当者";
  foreach($client_user as $item) {
    $user_name = $item['user_name'];
  }
?>
<h1>予算アラート(<?=$client_name?>)</h1>
<br />
<?=$client_name?> <?=$user_name?>様
<br />
<br />
お疲れ様です。
<br />
<br />
本日時点で下記の予算アラートが発生しております。
<br />
<br />
<table style="table-layout:fixed; width:30%;">
<tr><th>ステータス</th><th>件数</th></tr>
<tr>
<td class="bg_red">強制停止</td>
<td><font class="number"><?
  if(isset($alert_data[BUDGET_ALERT_STATUS_STOP])) {
    echo count($alert_data[BUDGET_ALERT_STATUS_STOP]);
  } else {
    echo 0;
  }
?>件</font></td>
</tr>
<tr>
<td class="bg_yellow">停止1～3日前</td>
<td><font class="number"><?
  if(isset($alert_data[BUDGET_ALERT_STATUS_FIVEDAYS])) {
    echo count($alert_data[BUDGET_ALERT_STATUS_FIVEDAYS]);
  } else {
    echo 0;
  }
?>件</font></td>
</tr>
<tr>
<td class="bg_green">停止4～6日前</td>
<td><font class="number"><?
  if(isset($alert_data[BUDGET_ALERT_STATUS_TENDAYS])) {
    echo count($alert_data[BUDGET_ALERT_STATUS_TENDAYS]);
  } else {
    echo 0;
  }
?>件</font></td>
</tr>
</table>
<br />
詳細を確認するには下記のURLをクリックして下さい。
<br />
<a href="<?=$url?>"><?=$url?></a>
<br />
<br />
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>

