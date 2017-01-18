予算アラート強制停止中(管理用)

<? foreach ($admin_datas as $admin_data) { ?>
<?=$admin_data['client_name']?> 


強制停止中: <?
  if(isset($admin_data['alert_data'])) {
    foreach ($admin_data['alert_data'] as $item) {
?>

　アカウントID：<?=$item['account_id']?>
　アカウント名：<?=$item['account_name']?>

<?
    }
  }
?>

URL: <?=$admin_data['url']?>


<? } ?>
