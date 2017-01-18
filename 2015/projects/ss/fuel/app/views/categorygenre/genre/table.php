<?if($categorygenre_list){?>
<div class="clearfix categorygenre_list">
	<form>
		<fieldset class="columns">
			<legend><div class="info label category-title">カテゴリジャンル一覧</div></legend>
			<table id="categorygenre_list" class="display">
				<thead>
					<tr>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_CATEGORY_GENRE_NAME?>">カテゴリジャンル名</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_MEMO?>">メモ</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_EDIT?>">編集</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_UPDATE_USER?>">更新者</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_UPDATE_DATETIME?>">更新日時</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_GENRE_EXPLAIN_DELETE?>">削除</td>
					</tr>
				</thead>
				<tbody>
					<?foreach ($categorygenre_list as $key => $value){?>
						<tr>
							<td><?=$value['category_genre_name'] ?></td>
							<td><?=nl2br($value['category_genre_memo']) ?></td>
							<td><div class="small secondary btn"><a href="javascript:modGenre('<?=$value['id']?>');">編集</a></div></td>
							<td><?=$value['user_name'] ?></td>
							<td><?=$value['datetime'] ?></td>
							<td><div class="small danger btn js-del-genre" data-item-id="<?=$value['id']?>" data-item-name="<?=$value['category_genre_name']?>"><a>削除</a></div></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<br>
<?}else{?>
	<br>カテゴリジャンルが存在しません。<br>
<?}?>