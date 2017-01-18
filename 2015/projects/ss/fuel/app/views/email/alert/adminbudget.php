予算アラート(管理用)

<? foreach ($admin_datas as $admin_data) { ?>
<?=$admin_data['client_name']?> 
強制停止: <?
  if(isset($admin_data['alert_data'][BUDGET_ALERT_STATUS_STOP])) {
    echo count($admin_data['alert_data'][BUDGET_ALERT_STATUS_STOP]);
  } else {
    echo 0;
  }
?>件
停止1～3日前: <?
  if(isset($admin_data['alert_data'][BUDGET_ALERT_STATUS_FIVEDAYS])) {
    echo count($admin_data['alert_data'][BUDGET_ALERT_STATUS_FIVEDAYS]);
  } else {
    echo 0;
  }
?>件
停止4～6日前: <?
  if(isset($admin_data['alert_data'][BUDGET_ALERT_STATUS_TENDAYS])) {
    echo count($admin_data['alert_data'][BUDGET_ALERT_STATUS_TENDAYS]);
  } else {
    echo 0;
  }
?>件
URL: <?=$admin_data['url']?>


<? } ?>
