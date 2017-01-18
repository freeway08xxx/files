<form enctype="multipart/form-data" id="form03" name="form03" method="POST" onsubmit="">
<div class="medium primary btn "><a href="javascript:loadHistoryTable();">Reload</a></div><br><br>
<div class="info label category-title">作業履歴一覧</div>
<?if($history_list){?>
<div class="history_list">
	<table id="history_combination" class="display">
		<thead>
			<tr>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_CATEGORY_GENRE_NAME?>">カテゴリジャンル名</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_ACTION_TYPE?>">作業種別</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_ELEMENT?>">設定単位</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_FILE_NAME?>">結果ファイル</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_EXPORT_STATUS?>">ステータス</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_ACCOUNT?>">対象アカウント</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_CREATE_USER?>">実行者</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_CREATE_DATETIME?>">開始日時</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_HISTORY_UPDATE_DATETIME?>">完了日時</td>
			</tr>
		</thead>
		<tbody>
		<?foreach ($history_list as $key => $value){?>
			<tr>
				<td><?=$value['category_genre_name']?></td>
				<td><?=$value['action_type']?></td>
				<td><?=nl2br($value['element_type'])?></td>
				<td>
					<? if (isset($value['file_path']) && $value['file_path'] != '') { ?>
						<? echo Html::anchor("/categorygenre/categorysetting/entrance/download/".$value['id'], "ダウンロード"); ?>
					<? } else { ?>
						--
					<? } ?>
				</td>
				<td><div class="columns"><ul><li class="<?=CategoryGenreConst::$category_edit_status_list[$value['status_id']]["label"]?>"><?=CategoryGenreConst::$category_edit_status_list[$value['status_id']]["name"]?></li></ul></div></td>
				<td><a href="javascript:selectAccountIdCopy('<?=nl2br($value['account_id_list'])?>');"><p class="over-text"><?=$value['account_id_list']?></p></a></td>
				<td><?=$value['user_name']?></td>
				<td><?=$value['created_at']?></td>
				<td><?=$value['updated_at']?></td>
			</tr>
		<?}?>
		</tbody>
	</table>
</div>
<?}else{?>
	作業履歴が存在しません。<br>
<?}?>
<input type="hidden" name="client_id" value="<?=$client_id?>"/>
<input type="hidden" class="action_type" name="action_type"/>
</form>