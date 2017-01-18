<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=SJIS">
<style type="text/css">
</style>
<body>
<br>
ご担当者様
<br>
<br>
<br>
おつかれさまです。新SearchSuiteです。
<br>
<br>
下記の入札ルール設定/理由にて、処理が回避されました。
<br>
<br>
クライアント名：<?= $client_name; ?>
<br>
入札ルールID  ：<?= $wabisabi_id; ?>
<br>
入札ルール名称：<?= $wabisabi_name; ?>
<br>
処理回避の理由：<?= $message; ?>
<br>
<br>
<br>
<font color="red">設定内容は、<a href="<?= $url; ?>">こちら</a>より変更してください。</font>
<br>
<br>
尚、こちらのメールにご返信頂いても回答はできませんのでご了承ください。
<br>
お問い合わせは、<a href="<?= MAIL_TO_WABISABID; ?>">こちら</a>からお願いします。
<br>
<br>
<p>Copyright c 1998-<?=date('Y')?> CyberAgent, Inc. All Rights Reserved.</p>
</body>
</html>
