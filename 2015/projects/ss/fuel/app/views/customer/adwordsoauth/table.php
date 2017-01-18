  <table width="30%">
    <tr>
      <td id="rabel">ID</td>
      <td id="rabel">アカウント名</td>
    </tr>
<? 
  if ($url_list) { 
    foreach ($url_list as $item) {
?>
    <tr>
      <td id="default">
        <?= $item['id']?>
      </td>
      <td id="default">
        <div>
          <a href="<?= $item['url'] ?>"><?= $item['name'] ?></a>
        </div>
      </td>
    </tr>
<?
    }
  }
?>
  </table>
