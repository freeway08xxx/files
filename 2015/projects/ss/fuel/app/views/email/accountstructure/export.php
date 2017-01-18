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
アカウントの設定内容の取得が完了しました。
<br><br>
設定内容は下記URLからダウンロード可能です。
<br>
<a href="<?=$download_url?>"><?=$download_url?></a><br />
<br>
※ダウンロード一覧画面から過去データ含めてダウンロードが可能です。
<hr>
アカウントID単位のサマリ情報は以下になります。
<br>
<? foreach ($item_num_info_list as $account_id => $item_num_info) { ?>
媒体名：<?= $account_info_list[$account_id]["media_name"] ?><br>
アカウント名：<?= $account_info_list[$account_id]["account_name"] ?><br>
アカウントID：<?= $account_id ?><br>
<br>
キャンペーン数：<?= $item_num_info["cpn"]["total"]."(".$item_num_info["cpn"]["active"].")" ?><br>
広告グループ数：<?= $item_num_info["adgroup"]["total"]."(".$item_num_info["adgroup"]["active"].")" ?><br>
キーワード数：<?= $item_num_info["kw"]["total"]."(".$item_num_info["kw"]["active"].")" ?><br>
広告数：<?= $item_num_info["td"]["total"]."(".$item_num_info["td"]["active"].")" ?><br>
除外キーワード数：<?= $item_num_info["negativekw"]["total"] ?><br>
ターゲット-ユーザリスト数：<?= $item_num_info["userlist"]["total"] ?><br>
ターゲット-スケジュール数：<?= $item_num_info["schedule"]["total"] ?><br>
ターゲット-性別数：<?= $item_num_info["gender"]["total"] ?><br>
ターゲット-年齢数：<?= $item_num_info["age"]["total"] ?><br>
ターゲット-地域数：<?= $item_num_info["area"]["total"] ?><br>
ターゲット-プレースメント数：<?= $item_num_info["placement"]["total"] ?><br>
<hr>
<? } ?>
<br>
※ 括弧内の数値はアクティブ数
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
