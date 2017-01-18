  <table class="table table-condensed table-bordered table-striped table-hover">
    <tr>
      <th id="rabel">ID</th>
      <th id="rabel">　テーブル名　|　カラム名　|　数値　|　条件</th>
    </tr>
    <tr>
      <td id="default">
        新規登録
      </td>
      <td id="default">
        <div>
          <form name="form01" method="post" action="#">
            <input type="hidden" name="id" value="">
            <input type="text" name="table" size="10" value="">
            <input type="text" name="column" size="10" value="">
            <input type="text" name="num" size="3" value="">
            <select name="type">
              <option value="">--</option>
              <option value="month">ヶ月以上</option>
              <option value="year">年以上</option>
              <option value="over">以上</option>
              <option value="under">以下</option>
            </select>
            <button type="button" class="btn btn-xs btn-default" id="button" onclick="doActionM('insert', 0)">挿入</button>
          </form>
        </div>
      </td>
    </tr>
<?
  if ($master_list) {
    foreach ($master_list as $item) {
?>
    <tr>
      <td id="default">
        <?=$item['id']?>
      </td>
      <td id="default">
        <div>
          <input type="text" name="table" size="10" id="table_<?=$item['id']?>" value="<?=$item['table_name']?>">
          <input type="text" name="column" size="10" id="column_<?=$item['id']?>" value="<?=$item['target_column']?>">
          <input type="text" name="num" size="3" id="num_<?=$item['id']?>" value="<?=$item['destroy_num']?>">
          <select name="type" id="type_<?=$item['id']?>">
            <option value="">--</option>
            <option value="month"<? if ($item['destroy_type'] == 'month'){ ?> selected<? } ?>>ヶ月以上</option>
            <option value="year"<? if ($item['destroy_type'] == 'year'){ ?> selected<? } ?>>年以上</option>
            <option value="over"<? if ($item['destroy_type'] == 'over'){ ?> selected<? } ?>>以上</option>
            <option value="under"<? if ($item['destroy_type'] == 'under'){ ?> selected<? } ?>>以下</option>
          </select>
          <button type="button" class="btn btn-xs btn-default" id="button" onclick="doActionM('update', <?=$item['id']?>)">更新</button>
          <button type="button" class="btn btn-xs btn-danger" id="button" onclick="doActionM('delete', <?=$item['id']?>)">削除</button>
        </div>
      </td>
    </tr>
<?
    }
  }
?>
  </table>
