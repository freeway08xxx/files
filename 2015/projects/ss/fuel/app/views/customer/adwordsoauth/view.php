<? if ($token && $res) { ?>
  アカウント<?=$id?>へのOAuth承認処理が完了しました。
<? } elseif (!$token) { ?>
  アカウント<?=$id?>へのOAuth承認処理は承認済です。<br>
  APIエラーが発生する場合アカウントにて一度、承認の解除後、承認処理を行ってください。
<? } else { ?>
  アカウント<?=$id?>へのOAuth承認処理が失敗しました。<br>
  Databaseコネクションを確認後、下記情報をDBへ登録してください。
  <table width="30%">
    <tr>
      <td id="rabel">ID</td>
      <td id="rabel"></td>
    </tr>
    <tr>
      <td id="default">
        <?=$id?>
      </td>
      <td id="default">
        <div>
          <?=$token?>
        </div>
      </td>
    </tr>
  </table>
<? } ?>
