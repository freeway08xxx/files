
<div class="top-control-area bottom_10px">
	<div class="btn btn-xs btn-info" id="button" onclick="doBackAction();">
			<span class="glyphicon glyphicon-chevron-left"></span>
		戻る
	</div>
	<div class="btn btn-xs btn-default" id="button" onclick="doExportAction('detail','<?=$link_key?>');"><a href="#" class="js-href-calceled">Export</a></div>
	<div class="btn btn-xs btn-default" id="button" onclick="doExportAction('daysdetail','<?=$link_key?>');"><a href="#" class="js-href-calceled">日付別Export</a></div>
</div>

<h5>URL別シェア(最大<?=$max_url_count?>件を表示しています)</h5>

<div class="table-wrap">
	<table class="report-table detail-table table table-striped table-hover">
		<?$no = 1;?>
		<? foreach($url_result_list as $url => $url_result) {
			if ($no%2 == 1) {
				if ($media_name == "yahoo") {
					$background = YAHOO_BGCOLOR_1;
				} else {
					$background = GOOGLE_BGCOLOR_1;
				}
			} else {
				if ($media_name == "yahoo") {
					$background = YAHOO_BGCOLOR_2;
				} else {
					$background = GOOGLE_BGCOLOR_2;
				}
			}

			if ($no % 100 == 1) { ?>
				<thead>
					<tr>
						<th>No</th>
						<th>デバイス</th>
						<? if ($line_type != 'url' || !$ad_flg) { ?>
							<th><? if($line_type == 'url') { ?>キーワード<? }else{ ?>URL<? } ?></th>
						<? } ?>
						<th>変換前URL</th>
						<th>ランク</th>
						<th>タイトル</th>
						<th>説明文</th>
						<th>imp&nbsp;</th>
						<th>click&nbsp;</th>
						<th>cost&nbsp;</th>
					</tr>
				</thead>
			<? } ?>

			<tbody>
				<tr>
					<td><?=$no?></td>
					<td>
						<?
							if ($device_id == 1) echo "PC";
							if ($device_id == 3) echo "SP:";
							if (isset($url_result[$media_name."_carrier_1"])) {
								echo "iPhone";
							}
							if (isset($url_result[$media_name."_carrier_2"])) {
								if (isset($url_result[$media_name."_carrier_1"])) {
									echo "　";
								}
								echo "Android";
							}
						?>
					</td>
					<? if ($line_type != 'url' || !$ad_flg) { ?>
						<td><?=$url_result['disp_key']?></td>
					<? } ?>
					<td>
						<?=$url_result[$media_name."_o_url"]?>
					</td>
					<td class="number">
							<?
								if ($device_type == '2') {
									echo round($url_result[$media_name."_rank_sum"] / $url_result[$media_name."_count"]);
								} else {
									echo $url_result[$media_name."_rank"];
								}
							?>
					</td>
					<td class="title">
							<?=$url_result[$media_name."_title"]?>
					</td>
					<td class="description">
							<?=$url_result[$media_name."_description"]?>
					</td>
					<td class="number"><?=number_format($url_result[$media_name."_imp"])?>&nbsp;</td>
					<td class="number"><?=number_format($url_result[$media_name."_click"])?>&nbsp;</td>
					<td class="number">&yen;<?=number_format($url_result[$media_name."_cost"])?>&nbsp;</td>
				</tr>
			</tbody>
			<? $no++ ?>
		<? } ?>
	</table>
</div>

<div class="btn-area bottom_10px">
	<div class="btn btn-xs btn-info" id="button"  onclick="doBackAction();">
		<span class="glyphicon glyphicon-chevron-left"></span>
		戻る
	</div>
	<div class="btn btn-xs btn-default" id="button"  onclick="doExportAction('detail','<?=$link_key?>');"><a href="#" class="js-href-calceled">Export</a></div>
</div>
