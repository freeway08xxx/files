<? if ($action_type == 'search' || $action_type == 'detail'){ ?>
	<p id="toggle-text input_area_key">
		<a href="" onclick="toggle('input_area', 'input_area_key');return false;">
		<?=$word?>
		</a>
	</p>
<? } ?>

<div id="input_area" style="display:<?=$display?>">
	<table class="table table-condensed table-striped table-bordered">

		<tr>
			<th width="100">クライアント</th>
			<td id="default">
				<select id="parent" name="client_id">
					<option value="">--</option>
					<? foreach($client_list as $item) {
						if ($item["id"] == $client_id) {
							$selected = "selected";
						} else {
							$selected = "";
						} ?>
						<option value="<?=$item["id"]?>" <?=$selected?>><?=$item["name"]?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th width="150">クライアント詳細</th>
			<td id="default">
			<? foreach($client_list as $cl) { ?>
				<select id="child_<?= $cl["id"]?>" class="hide client-class" name="tmp_client_class_id">
					<option class="c_" value="">--</option>
					<? foreach($client_class_list as $item) {
						if ($item["client_id"] !== $cl["id"]) {
							continue;
						}

						if ($item["id"] == $client_class_id) {
							$selected = "selected";
						} else {
							$selected = "";
						} ?>
						<option class="" value="<?=$item["id"]?>" <?=$selected?>><?=$item["name"]?></option>
					<? } ?>
				</select>
			<? } ?>
			<input type="hidden" name="client_class_id" value="<?=$client_class_id?>">

			</td>
		</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered">
		<tr id="detail1">
			<th style="width:150px;">
				サマリ単位
			</th>
			<td id="default">
				<select name="line_type">
					<option value='url'<? if ($line_type == 'url') { ?> selected<? } ?>>URL</option>
					<option value='keyword'<? if ($line_type == 'keyword') { ?> selected<? } ?>>キーワード</option>
				</select>
			</td>
		</tr>
		<tr id="detail2">
			<th>
				集計日指定(yyyy/mm/dd)
			</th>
			<td id="default">
				<div>
					<select name="day_type" id="day_type">
						<option value='day'<? if ($day_type == 'day') { ?> selected<? } ?>>日別サマリ</option>
						<option value='fromto'<? if ($day_type == 'fromto') { ?> selected<? } ?>>期間サマリ</option>
					</select>

					<input type="text" class="xnarrow text input" id="from_day" name="from_day" value="<?=$from_day?>">
					<span class="js-to-day-wrap<? if ($day_type != 'fromto') { ?> hide<? } ?>"> ～
						<input type="text" class="xnarrow text input" id="to_day" name="to_day" value="<?=$to_day?>">
					</span>
				</div>
			</td>
		</tr>
		<tr id="detail3">
			<th>
				媒体費
			</th>
			<td id="default">
				<input type="text" size="3" name="media_cost" value="<? if ($media_cost) { echo $media_cost; }else{ echo 20; } ?>">％
			</td>
		</tr>
		<tr id="detail4">
			<th>
				算出方法
			</th>
			<td id="default">
				<select name="sum_type">
					<?
						foreach($sum_type_list as $key => $sum_type_name) {
							if ($key == $sum_type) {
								$selected = "selected";
							} else {
								$selected = "";
							}
					?>
						<option value='<?=$key?>' <?=$selected?>><?=$sum_type_name?></option>
					<?}?>
				</select>
				<?//=$messageUtil->getMessageIcon("#4");?>
			</td>
		</tr>
		<tr>
			<th>サブドメイン</th>
			<td id="default"><input type="checkbox" name="convert_sub_domein" value="checked" <?=$convert_sub_domein?>>ドメインに変換する。</td>
		</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered">
		<thead>
			<tr id="detail4">
				<th>
					媒体
				</th>
				<th>
					デバイス
				</th>
				<th>
					対象URL(オプション)
				</th>
				<th>
					URL変換(オプション)
				</th>
			</tr>
		</thead>
		<tbody>
			<tr id="detail4">
				<td id="default" valign="top">
					<span>Yahoo!</span>
					<input type="checkbox" class="target_media" name="yahoo_media" data-on="ON" data-off="OFF"<? if ($yahoo_media) { ?> checked<? } ?> />
					<br>
					<br>
					<span>Google</span>
					<input type="checkbox" class="target_media" name="google_media" data-on="ON" data-off="OFF"<? if ($google_media) { ?> checked<? } ?> />
				</td>
				<td id="default" valign="top">
					<select name="device_type">
						<option value='1'<? if ($device_type == '1') { ?> selected<? } ?>>PC</option>
						<option value='2'<? if ($device_type == '2') { ?> selected<? } ?>>SP</option>
						<option value='3'<? if ($device_type == '3') { ?> selected<? } ?>>SP(iPhone)</option>
						<option value='4'<? if ($device_type == '4') { ?> selected<? } ?>>SP(Android)</option>
					</select>
				</td>
				<td id="default">
					<div>
						<span style="margin-left:160px">部分一致</span>
						<input type="checkbox" name="check_url_df"<? if ($check_url_df) { ?> checked<? } ?> />
					</div>
					<TEXTAREA cols="18" rows="8" name="check_url" style="width:230px;height:150px"><?= $check_url ?></TEXTAREA>
				</td>
				<td id="default">
					<div style="margin-left:100px;float:left">変更前</div>
					<div style="margin-left:220px;float:left">変更後</div>
					<div style="clear:both;"></div>
					<TEXTAREA cols="18" rows="8" name="replace_url_before" style="width:230px;height:150px"><?= $replace_url_before ?></TEXTAREA>
					<TEXTAREA cols="18" rows="8" name="replace_url_after" style="width:230px;height:150px"><?= $replace_url_after ?></TEXTAREA>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="btn-area" id="search_btn">
		<a href="#" class="btn btn-sm btn-primary js-href-canceled">レポートを作成</a>
	</div>

</div>
