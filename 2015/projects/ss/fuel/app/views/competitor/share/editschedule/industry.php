<? if ($current_flg) { ?>
    <div id="industry_<?=$item['id']?>" style="margin : 2px 3px 2px 3px; border: 2px solid #000;position: relative;height: 40px;background-color: #FAF6BD;">
<? } else { ?>
    <div id="industry_<?=$item['id']?>" style="margin : 2px 3px 2px 3px; border: 1px solid #000;position: relative;height: 40px">
<? } ?>
      <? if ($custom_flg) { ?>
        <span onclick="doDelAction(<?=$item['id']?>, false)" style="position: absolute;left: 2px;bottom: 10px;">
          <i class="glyphicon glyphicon-close text-danger"></i>
        </span>
      <? } ?>
      <p id="industry_<?=$item['id']?>_pv" <? if ($custom_flg) { ?>onclick="doEditAction(<?=$item['id']?>, false)"<? } ?> style="position: absolute;left: 35px;bottom: 8px;">
        [<?=$item['sort']?>]&nbsp;<?=$item['name']?>
      </p>
    <? if ($current_flg) { ?>
      <p id="industry_<?=$item['id']?>_pvb" style="position: absolute;right: 2px;bottom: 6px;">
        <? if ($custom_flg) { ?><input type="button" id="button"  onclick="doEditUserAction('<?=$item['id']?>')" value="アクセスユーザー管理"><? } ?>
      </p>
    <? } else { ?>
      <p id="industry_<?=$item['id']?>_pvb" style="position: absolute;right: 2px;bottom: 6px;">
        <input type="button" id="button"  onclick="location.href='<?=$url?><?=$item['id']?>'" value="業種詳細">
      </p>
    <? } ?>
      <p id="industry_<?=$item['id']?>_pt" style="position: absolute;left: 35px;bottom: 8px;display: none;">
        並び順：<input id="industry_<?=$item['id']?>_sort" type="text" value="<?=$item['sort']?>" size="1">
        業種名：<input id="industry_<?=$item['id']?>_name" type="text" value="<?=$item['name']?>" size="20">
      </p>
      <p id="industry_<?=$item['id']?>_ptb" style="position: absolute;right: 2px;bottom: 6px;display: none">
        <input type="button" id="button"  onclick="doUpdAction(<?=$item['id']?>, false)" value="反映">
      </p>
    </div>
