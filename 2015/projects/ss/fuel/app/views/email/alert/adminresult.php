予算アラート強制停止結果(管理用)

<? foreach ($admin_datas as $admin_data) { ?>
<?=$admin_data['client_name']?> 
ステータス更新エラー: <?
  if(isset($admin_data['alert_data'][BUDGET_ALERT_STATUS_ERROR])) {
    foreach ($admin_data['alert_data'][BUDGET_ALERT_STATUS_ERROR] as $item) {
?>

　アカウントID：<?=$item['account_id']?>
　アカウント名：<?=$item['account_name']?>
　予算リミット：\<?=$item['limit_budget']?>
　当月実績値：\<?=$item['consumption_cost']?>

<?
    }
  }
?>


強制停止成功: <?
  if(isset($admin_data['alert_data'][BUDGET_ALERT_STATUS_TODAYSTOP])) {
    foreach ($admin_data['alert_data'][BUDGET_ALERT_STATUS_TODAYSTOP] as $item) {
?>

　アカウントID：<?=$item['account_id']?>
　アカウント名：<?=$item['account_name']?>
　予算リミット：\<?=$item['limit_budget']?>
　当月実績値：\<?=$item['consumption_cost']?>

<?
    }
  }
?>

URL: <?=$admin_data['url']?>


<? } ?>
