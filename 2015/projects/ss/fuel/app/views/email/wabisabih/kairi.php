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
<font color='red'>
<? if ($judge_of_bid_adjust > 0) { ?>
今回の入札では、ターゲットコスト迄到達しない予測です。
<br>
大きく乖離が続く場合には、下記を検討してください。
<br>
・月額予算ターゲットを下げる
<br>
・目標CPAを引き上げる
<br>
・入札上限値を引き上げる
<br>
<? } else { ?>
今回の入札では、ターゲットコスト迄下がらない予測です。
<br>
これ以上のマイナス調整は停止での対応を検討してください。
<br>
<? } ?>
</font>
<br>
<br>
クライアント名： <?= $client_name; ?>
<br>
入札ルールID  ： <?= $wabisabi_id; ?>
<br>
入札ルール名称： <?= $wabisabi_name; ?>
<br>
<br>
月額予算ターゲット  ： <?= '\\ '.number_format($target_budget); ?>
<br>
<? if ($target_budget_mode === TARGET_BUDGET_MODE_MON) { ?>
昨日までの使用金額  ： <?= '\\ '.number_format($sum_cost_this_month); ?>
<br>
残り日数            ： <?= $remainning_days; ?>
<br>
本日ターゲットコスト： <?= '\\ '.number_format(floor($target_budget * TARGET_BUDGET_RATE - $sum_cost_this_month) / $remainning_days); ?>
<br>
基準日コスト        ： <?= '\\ '.number_format($sum_ref_cost); ?>
<br>
<? } ?>
差分コスト          ： <?= '\\ '.number_format($bid_adjust_cost); ?>
<br>
入札後予測コスト    ： <?= '\\ '.number_format($sum_new_increase_cost); ?>
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
