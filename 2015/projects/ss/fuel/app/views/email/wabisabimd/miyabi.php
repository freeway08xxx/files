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
<br>
<br>
<?= $client_name ?> ご担当者様
<br>
<br>
お疲れ様です。MIYABI'dシステム担当です。
<br>
<br>
<? if ($mail_kbn === MIYABI_MAIL_RES_OK) { ?>
設定名称:<?= $miyabi_name ?> のAPI入稿が完了しました。
<? } elseif ($mail_kbn === MIYABI_MAIL_RES_NG) { ?>
設定名称:<?= $miyabi_name ?> のAPI入稿に失敗しました。
<? } elseif ($mail_kbn === MIYABI_MAIL_RES_NO_TARGET) { ?>
設定名称:<?= $miyabi_name ?> のAPI入稿を行いましたが、入稿対象のデータが存在しませんでした。
<br>
設定画面にて、実施頻度を長く設定するなどの対応をお願い致します。
<? } ?>
<br>
<br>
詳細を確認するには下記のURLをクリックして下さい。
<br>
<br>
<a href="http://sem-portal.cyberagent.co.jp/sem/wabisabi_md/miyabi_list.php">http://sem-portal.cyberagent.co.jp/sem/wabisabi_md/miyabi_list.php</a>
<br>
<br>
<br>
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>
