<form>
	<fieldset class="columns">
		<legend><div class="info label category-title"><?=CategoryGenreConst::$category_elem_name_list[$category_elem]?><? $action_type == "view" ? print "編集" : print "登録"?></div></legend>
		<div>
			<div class="register field clearfix">
				<table>
					<tr>
						<td class="table-type-ex1">
							カテゴリ名<font color="red">（必須）</font>
						</td>
						<td class="table-type-ex2">
							<? if($action_type == 'view' && isset($category_info['category_name'])){
								$category_name = $category_info['category_name'];
							}else{
								$category_name = "";
							}?>
							<input type="text" class="category_name" name="category_name" value="<?=$category_name?>" placeholder="入力されたカテゴリ名で保存されます" size="50"/>
						</td>
					</tr>
					<tr>
						<td class="table-type-ex1">
							メモ
						</td>
						<td class="table-type-ex2">
							<? if($action_type == 'view' && isset($category_info['category_memo'])){
								$category_memo = $category_info['category_memo'];
							}else{
								$category_memo = "";
							}?>
							<textarea class="category_memo" name="category_memo" cols=50 rows=2 placeholder="例）「BIG」を含むCPN"><?=$category_memo?></textarea>
						</td>
					</tr>
					<tr>
						<td class="table-type-ex1">
							並び順
						</td>
						<td class="table-type-ex2">
							<select class="sort_order" name="sort_order">
								<option value="">--</option>
								<? if(!isset($category_info['sort_order'])){
									$elem_count += 1; //新規登録時は登録済カテゴリ数＋1
								}?>
								<? for($i=1; $i<=$elem_count; $i++) { ?>
									<? if($action_type == 'view' && isset($category_info['sort_order']) && $i == $category_info['sort_order']){
										$selected = "selected";
									}else{
										$selected = "";
									}
									?>
									<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
								<?}?>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<? if($action_type == 'view' && isset($category_info['id'])){
				$category_id = $category_info['id'];
			}else{
				$category_id = "";
			}?>
			<div class="medium primary btn"><a href="javascript:editCategory('<?=$category_id?>');"><? $action_type == "view" ? print "編集" : print "登録"?></a></div>
		</div>
	</fieldset>
</form>
<input type="hidden" class="genre_id" name="genre_id" value="<?=$genre_id?>"/>
<input type="hidden" class="category_elem" name="category_elem" value="<?=$category_elem?>"/>