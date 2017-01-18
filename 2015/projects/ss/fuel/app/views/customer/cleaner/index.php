<div class="legacy" style="padding-top: 32px">
  <? if ($errors){ ?>
    <ul>
    <? foreach($errors as $error) { ?>
      <li>
        <?=$error;?>
      </li>
    <? } ?>
    </ul>
  <? } ?>
  <?= $table;?>
</div>

<script language=javascript>
	function doActionM(action, id){
		//入力チェック
    document.form01.action="/sem/new/customer/cleaner/" + action;
    if (id > 0) {
      document.form01.id.value=id;
      document.form01.table.value=$('#table_'+id).val();
      document.form01.column.value=$('#column_'+id).val();
      document.form01.type.value=$('#type_'+id).val();
      document.form01.num.value=$('#num_'+id).val();
    }
    document.form01.submit();
	}
</script>