<?if($genre_list){?>
<form>
	<div class="clearfix">
		<fieldset class="columns back-col">
			<legend>カテゴリジャンルを選択</legend>
			<div>
				<table class="form-table">
					<tr>
						<td>カテゴリジャンル名<font color="red">（必須）</font></td>
						<td>
							<div>
								<select class="select_genre_id" name="select_genre_id">
									<option value="">--</option>
									<? foreach($genre_list as $genre) { ?>
										<option value="<?=$genre["id"]?>"><?=$genre["category_genre_name"]?>　（<?=$genre["user_name"]?>：<?=$genre["datetime"]?>）</option>
									<?}?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td>カテゴリ種別<font color="red">（必須）</font></td>
						<td>
							<label for="category_big_id"><input type="radio" id="category_big_id" name="select_category_elem" value="category_big_id">大カテゴリ</label>
							<label for="category_middle_id"><input type="radio" id="category_middle_id" name="select_category_elem" value="category_middle_id">中カテゴリ</label>
							<label for="category_id"><input type="radio" id="category_id" name="select_category_elem" value="category_id">小カテゴリ</label>
						</td>
					</tr>
				</table>
			</div>
		</fieldset>
	</div>
</form>
<p>
	<div class="medium primary btn"><a href="javascript:viewCategoryList();">登録済みカテゴリを表示</a></div>　
	<div class="medium primary btn js-edit-category" data-item-id="" data-item-elem=""><a>＋新規カテゴリ登録</a></div>　
	<div class="medium primary btn"><a href="javascript:modBulkCategory();">＋新規カテゴリ一括登録</a></div>
</p>
<br>
<?}else{?>
	<br>カテゴリジャンルが存在しません。<br>
	事前にカテゴリジャンルを登録してください。<br>
	<br>
	<a href="/sem/new/categorygenre/genre/entrance/setting/<?=$client_id?>">カテゴリジャンル登録へ</a>
<?}?>