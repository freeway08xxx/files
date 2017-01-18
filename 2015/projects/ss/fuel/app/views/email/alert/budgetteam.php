御担当者様

お疲れ様です。

以下の媒体予算の変更をお願いします。

・媒体：<? if ($account['media_id'] == 1) {
            echo "Yahoo!";
           } elseif($account['media_id'] == '3') {
            echo "YDN";
           } ?>

・アカウント名：<?=$account['account_name']?>

・アカウントID：<?=$account['account_id']?>


＜変更前＞
<? if ($account['budget_type_id'] != $budget_type_id) { ?>
・予算タイプ：<? if ($account['budget_type_id'] == '1') {
                  echo '月額';
               } else {
                  echo '総額';
               } ?>

<? } ?>
・予算金額（税抜）：￥<?=number_format($account['budget'])?> 

＜変更後＞
<? if ($account['budget_type_id'] != $budget_type_id) { ?>
・予算タイプ：<? if ($budget_type_id == '1') {
                  echo '月額';
               } else {
                  echo '総額';
               } ?>

<? } ?>
・予算金額（税抜）：￥<?=number_format($account_budget)?> 

以上

申請担当者：<?=$user_name?>

