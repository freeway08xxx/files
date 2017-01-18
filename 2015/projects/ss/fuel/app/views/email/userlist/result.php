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
<?= $user_name ?> 様
<br>
<br>
おつかれさまです。新SearchSuiteです。
<br>
<br>
ユーザリスト[<?=$processed?>]の[<?=$process_operation?>]処理が完了しました。
<br>
処理OK件数：<?=$ok_cnt?>
<br>
処理NG件数：<?=$ng_cnt?>
<br>
<br>
詳細結果については下記URLからファイルをダウンロードしてください。
<br>
<a href="<?=$url?>"><?=$url?></a><br />
<br>
<br>
不明点等ございましたら、SearchSuiteヘルプまでお問い合わせください。
<br>
<a href="http://sem-portal.cyberagent.co.jp/sem/help/help.php">http://sem-portal.cyberagent.co.jp/sem/help/help.php</a>
<br>
<br>
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>
