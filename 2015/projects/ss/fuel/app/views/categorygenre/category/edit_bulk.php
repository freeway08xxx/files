<form>
	<fieldset class="columns">
		<legend><div class="info label category-title">新規<?=CategoryGenreConst::$category_elem_name_list[$category_elem]?>一括登録</div></legend>
		<div class="tow columns">
			<li class="warning alert">
			・メモの登録は出来ません。<br>
			　登録後に再度編集しメモを更新してください。<br>
			・並び順は入力された順番で登録されます。<br>
			　選択されたカテゴリジャンルに既に並び順が登録されている場合は、<br>
			　［登録済み並び順の最大値＋1］の順番から登録されます。
			</li>
		</div>
		<br><br><br><br><br><br>
		<div class="user-base">
			<div class="register field clearfix">
				<table>
					<tr>
						<td class="table-type-ex1">
							カテゴリ名<font color="red">（必須）</font>
						</td>
						<td class="table-type-ex2">
							<textarea class="category_name_list" name="category_name_list" cols=50 rows=2 placeholder="改行区切りでカテゴリ名を入力してください"></textarea>
						</td>
					</tr>
				</table>
			</div>
			<div class="medium primary btn"><a href="javascript:editBulkCategory();">登録</a></div>
		</div>
	</fieldset>
</form>
<input type="hidden" class="genre_id" name="genre_id" value="<?=$genre_id?>"/>
<input type="hidden" class="category_elem" name="category_elem" value="<?=$category_elem?>"/>