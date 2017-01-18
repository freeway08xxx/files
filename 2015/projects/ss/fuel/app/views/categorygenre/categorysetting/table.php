<form enctype="multipart/form-data" id="form02" name="form02" method="POST" onsubmit="">
<h4><?if(isset($genre_info)){ print "カテゴリジャンル名：".$genre_info["category_genre_name"].""; }?></h4>
<div><a href="#" class="back_category_setting">カテゴリ設定に戻る</a></div><br>
<div class="info label category-title">カテゴリ設定一覧（総件数：<? echo ($max_count_flg) ? CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT : count($view_category_elem_list) ?>件）</div>
<?if($max_count_flg){?>
	<font color="red">最大表示件数(<?=CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT?>件)を超過したため、一部データは表示できません。</font>
<? }?>
<?if($view_category_elem_list){?>
<div class="category_elem_list">
	<table id="category_elem_combination" class="display">
		<thead>
		<tr class="table-label">
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_NO?>">No.</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_MEDIA_NAME?>">媒体名</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_ACCOUNT_ID?>">アカウントID</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_ACCOUNT_NAME?>">アカウント名</td>
			<? if($element_type_id > 1){ ?>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_CAMPAIGN_ID?>">キャンペーンID</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_CAMPAIGN_NAME?>">キャンペーン名</td>
				<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_CAMPAIGN_STATUS?>">ステータス</td>
				<? if($element_type_id > 2){ ?>
					<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_AD_GROUP_ID?>">広告グループID</td>
					<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_AD_GROUP_NAME?>">広告グループ名</td>
					<? if($element_type_id > 3){ ?>
						<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_KEYWORD_ID?>">キーワードID</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_KEYWORD_NAME?>">キーワード</td>
						<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_LINK_URL?>">リンク先URL</td>
					<? } ?>
				<? } ?>
			<? } ?>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_BEFORE_CATEGORY_TYPE?>">【現】設定単位</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_CATEGORY_TYPE?>">設定単位</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_BIG_NAME?>">大カテゴリ<br>
				<select class="js_category_big_id" name="js_category_big_id">
					<option value="--">選択してください</option>
					<? foreach ($category_big_id_list as $category_big_id => $category_name){ ?>
						<option value="<?=$category_big_id?>"><?=$category_name?></option>
					<? } ?>
				</select>
			</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_MIDDLE_NAME?>">中カテゴリ<br>
				<select class="js_category_middle_id" name="js_category_middle_id">
					<option value="--">選択してください</option>
					<? foreach ($category_middle_id_list as $category_middle_id => $category_name){ ?>
						<option value="<?=$category_middle_id?>"><?=$category_name?></option>
					<? } ?>
				</select>
			</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_NAME?>">小カテゴリ<br>
				<select class="js_category_id" name="js_category_id">
					<option value="--">選択してください</option>
					<? foreach ($category_id_list as $category_id => $category_name){ ?>
						<option value="<?=$category_id?>"><?=$category_name?></option>
					<? } ?>
				</select>
			</td>
			<td class="ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_CATEGORY_DELETE_ELEMENT?>"><input type="checkbox" id="elem_all_delete_flg" name="elem_all_delete_flg" value="1"><br>解除</td>
		</tr>
		</thead>
		<tbody>
		<?foreach ($view_category_elem_list as $key => $value){?>
			<tr class="category_elem_list">
				<td><?=$value['no']?></td>
				<td><?=$value['media_name']?></td>
				<td><?=$value['account_id']?></td>
				<td><?=$value['account_name']?></td>
				<? if($element_type_id > 1){ ?>
					<td class="campaign_id"><?=$value['campaign_id']?></td>
					<td><?=$value['campaign_name']?></td>
					<td><a class="<?=CategoryGenreConst::$campaign_status_label_list[$value['campaign_status']]?> label"><?=$value['campaign_status']?></a></td>
					<? if($element_type_id > 2){ ?>
						<td><?=$value['ad_group_id']?></td>
						<td><?=$value['ad_group_name']?></td>
						<? if($element_type_id > 3){ ?>
							<td><?=$value['keyword_id']?></td>
							<td><?=$value['keyword']?></td>
							<td><?=$value['link_url']?></td>
						<? } ?>
					<? } ?>
				<? } ?>
				<td><?=$value['before_element_type_name']?></td>
				<td><?=$value['element_type_name']?></td>
				<td>
					<select id="update_category_big_id" class="update_category_big_id" name="update_category_big_id">
						<? if($value['before_category_big_name'] == "[複数設定あり]"){
								$view_many = "[複数設定あり]";
								$selected_many = "selected";
								$val_many = "[複数設定あり]";
							}else{
								$view_many = "未設定";
								$selected_many = "";
								$val_many = "--";
						} ?>
						<option value=<?=$val_many?> <?=$selected_many?>><?=$view_many?></option>
						<? foreach ($category_big_id_list as $category_big_id => $category_name){ ?>
							<?
								if($category_big_id==$value['before_category_big_id']){
									$selected = "selected";
								}else{
									$selected = "";
								}
							?>
							<option value="<?=$category_big_id?>" <?=$selected?>><?=$category_name?></option>
						<? } ?>
					</select>
				</td>
				<td>
					<select id="update_category_middle_id" class="update_category_middle_id" name="update_category_middle_id">
						<? if($value['before_category_middle_name'] == "[複数設定あり]"){
								$view_many = "[複数設定あり]";
								$selected_many = "selected";
								$val_many = "[複数設定あり]";
							}else{
								$view_many = "未設定";
								$selected_many = "";
								$val_many = "--";
						} ?>
						<option value=<?=$val_many?> <?=$selected_many?>><?=$view_many?></option>
						<? foreach ($category_middle_id_list as $category_middle_id => $category_name){ ?>
							<?
								if($category_middle_id==$value['before_category_middle_id']){
									$selected = "selected";
								}else{
									$selected = "";
								}
							?>
							<option value="<?=$category_middle_id?>" <?=$selected?>><?=$category_name?></option>
						<? } ?>
					</select>
				</td>
				<td>
					<select id="update_category_id" class="update_category_id" name="update_category_id">
						<? if($value['before_category_name'] == "[複数設定あり]"){
								$view_many = "[複数設定あり]";
								$selected_many = "selected";
								$val_many = "[複数設定あり]";
							}else{
								$view_many = "未設定";
								$selected_many = "";
								$val_many = "--";
						} ?>
						<option value=<?=$val_many?> <?=$selected_many?>><?=$view_many?></option>
						<? foreach ($category_id_list as $category_id => $category_name){ ?>
							<?
								if($category_id==$value['before_category_id']){
									$selected = "selected";
								}else{
									$selected = "";
								}
							?>
							<option value="<?=$category_id?>" <?=$selected?>><?=$category_name?></option>
						<? } ?>
					</select>
				</td>
				<td><input type="checkbox" id="elem_delete_flg" name="elem_delete_flg" value="1"></td>
			</tr>
		<?}?>
		</tbody>
	</table>
</div>
<p>
	<div class="medium primary btn js-update-setting ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_UPDATE_BUTTON?>"><a href="javascript:viewCategorySettingList('update');">更新</a></div>　
	<div class="medium danger btn js-update-setting ttip" data-tooltip="<?=CATEGORY_SETTING_EXPLAIN_DELETE_BUTTON?>"><a href="javascript:viewCategorySettingList('delete');">解除</a></div><br>
</p>
<?}else{?>
	<br>該当の掲載内容が存在しません。<br>
<?}?>
<div class="back_category_setting"><a href="#" class="js-href-canceled">カテゴリ設定に戻る</a></div>
<input type="hidden" name="client_id" value="<?=$client_id?>"/>
<input type="hidden" class="action_type" name="action_type"/>
</form>