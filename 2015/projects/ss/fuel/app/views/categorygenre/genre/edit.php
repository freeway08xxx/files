<form>
	<fieldset class="columns">
		<legend>カテゴリジャンル<? $action_type == "view" ? print "編集" : print "登録"?></legend>
		<div class="user-base">
			<div class="register field clearfix">
				<table>
					<tr>
						<td class="table-type-ex1">
							カテゴリジャンル名<font color="red">（必須）</font>
						</td>
						<? if($action_type == 'view' && $genre_info['category_genre_name']){
							$category_genre_name = $genre_info['category_genre_name'];
						}else{
							$category_genre_name = "";
						}?>
						<td class="table-type-ex2">
							<input type="text" class="category_genre_name" name="category_genre_name" value="<?=$category_genre_name?>" placeholder="入力されたカテゴリジャンル名で保存されます" size="50"/>
						</td>
					</tr>
					<tr>
						<td class="table-type-ex1">
							メモ
						</td>
						<? if($action_type == 'view' && $genre_info['category_genre_memo']){
							$category_genre_memo = $genre_info['category_genre_memo'];
						}else{
							$category_genre_memo = "";
						}?>
						<td class="table-type-ex2">
							<textarea class="category_genre_memo" name="category_genre_memo" cols=50 rows=2 placeholder="例）日別レポート用ジャンル"><?=$category_genre_memo?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<? if($action_type == 'view' && $genre_info['id']){
				$genre_id = $genre_info['id'];
			}else{
				$genre_id = "";
			}?>
			<div class="medium primary btn"><a href="javascript:editGenre('<?=$genre_id?>');"><? $action_type == "view" ? print "編集" : print "登録"?></a></div>
		</div>
	</fieldset>
</form>
