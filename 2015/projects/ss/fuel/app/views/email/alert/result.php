<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<style type="text/css">
	h1 {font-size:large;border-left:15px solid #38b032;  border-bottom:2px solid #38b032;  padding:.4em .6em;}
  .red {font-size:large;color:red;text-decoration: underline;}
  .account {color:blue;}
</style>
<body>
<br />
<?
  $user_name = "ご担当者";
  foreach($client_user as $item) {
    $user_name = $item['user_name'];
  }
?>
<h1>(<?=$client_name?>)アカウント強制停止実行</h1>
<br />
<?=$client_name?> <?=$user_name?>様
<br />
<br />
お疲れ様です。
<br />
<br />
<div class="red">本日、以下の停止対象アカウントの強制停止を実行しました。</div>
<br />
<? foreach ($alert_data as $item) { ?>
<br />
<div class="account">アカウントID：<?=$item['account_id']?></div>
<div class="account">アカウント名：<?=$item['account_name']?></div>
<div class="money">予算リミット：￥<?=number_format($item['limit_budget'])?></div>
<div class="money">当月実績値：￥<?=number_format($item['consumption_cost'])?></div>
<br />
<? } ?>
<br />
再開する場合は、アラート画面から予算リミットを変更の上、「停止解除」を押してください。
<br />
<br />
詳細を確認するには下記のURLをクリックして下さい。
<br />
<a href="<?=$url?>"><?=$url?></a>
<br />
<br />
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>

