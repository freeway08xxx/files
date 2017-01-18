<?if($elem_list){?>
<div class="clearfix category_list">
	<form>
		<fieldset class="columns">
			<legend><div class="info label category-title"><?=CategoryGenreConst::$category_elem_name_list[$category_elem]?>一覧（<?=count($elem_list)?>件）</div></legend>
			<table id="category_list" class="display">
				<thead>
					<tr>
						<td class="ttip" data-tooltip="<?=CATEGORY_EXPLAIN_CATEGORY_NAME?>">カテゴリ名</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_MEMO?>">メモ</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_EXPLAIN_SORT_ORDER?>">並び順</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_EXPLAIN_EDIT?>">編集</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_UPDATE_USER?>">更新者</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_UPDATE_DATETIME?>">更新日時</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_EXPLAIN_DELETE?>">削除</td>
					</tr>
				</thead>
				<tbody>
					<?foreach ($elem_list as $key => $value){?>
						<tr class="category_list">
							<td><?=$value['category_name']?></td>
							<td><?=nl2br($value['category_memo'])?></td>
							<td><?=$value['sort_order']?></td>
							<td><div class="small secondary btn js-edit-category" data-item-id="<?=$value['id']?>" data-item-elem="<?=$value['category_elem']?>"><a>編集</a></div></td>
							<td><?=$value['user_name']?></td>
							<td><?=$value['datetime']?></td>
							<td><div class="small danger btn js-del-category" data-item-id="<?=$value['id']?>" data-item-elem="<?=$value['category_elem']?>" data-item-name="<?=$value['category_name']?>"><a>削除</a></div></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<?}else{?>
	<br>カテゴリが存在しません。<br>
<?}?>