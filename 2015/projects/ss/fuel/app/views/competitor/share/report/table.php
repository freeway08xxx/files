<div class="top-control-area bottom_10px">
	<div class="btn btn-xs btn-default" id="button"  onclick="doExportAction('','');"><a href="#" class="js-href-calceled">Export</a></div>
	<div class="btn btn-xs btn-default" id="button"  onclick="doExportAction('all','');"><a href="#" class="js-href-calceled">日付別Export</a></div>
</div>

<h5>URL別シェア(最大<?=$max_url_count?>件を表示しています)</h5>

<div class="table-wrap">
		<table class="report-table table striped">
			<?
				$no = 1;
				foreach($url_result_list as $key => $url_result) {
					if ($no > $max_url_count) break;
					if ($no%2 == 1) {
						$yahoo_background = YAHOO_BGCOLOR_1;
						$google_background = GOOGLE_BGCOLOR_1;
						$total_background = TOTAL_BGCOLOR_1;
					} else {
						$yahoo_background = YAHOO_BGCOLOR_2;
						$google_background = GOOGLE_BGCOLOR_2;
						$total_background = TOTAL_BGCOLOR_2;
					}
					if ($no % 100 == 1) {
					?>
					<tr>
						<td id="rabel" rowspan="2" style="width:30px">No</td>
						<? if ($line_type == 'url') { ?>
							<td id="rabel" rowspan="2">URL</td>
						<? } else { ?>
							<td id="rabel" rowspan="2">キーワード</td>
						<? } ?>
						<? if ($yahoo_media) { ?>
							<td id="rabel" colspan="<? if ($line_type == 'url') { ?>6<? }else{ ?>5<? } ?>">Yahoo!</td>
						<? } ?>
						<? if ($google_media) { ?>
							<td id="rabel" colspan="<? if ($line_type == 'url') { ?>6<? }else{ ?>5<? } ?>">Google</td>
						<? } ?>
					</tr>
					<tr>
						<? if ($yahoo_media) { ?>
							<? if ($line_type == 'url') { ?>
								<td id="rabel" style="width:80px">KW数</td>
							<? } ?>
							<td id="rabel" style="width:80px">広告数</td>
							<td id="rabel" style="width:80px"><font size=1>インサーション</font></td>
							<td id="rabel" style="width:120px">imp&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "yahoo_imp");?>
							</td>
							<td id="rabel" style="width:120px">click&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "yahoo_click");?>
							</td>
							<td id="rabel" style="width:120px">cost&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "yahoo_cost");?>
							</td>
						<? } ?>
						<? if ($google_media) { ?>
							<? if ($line_type == 'url') { ?>
								<td id="rabel" style="width:80px">KW数</td>
							<? } ?>
							<td id="rabel" style="width:80px">広告数</td>
							<td id="rabel" style="width:80px"><font size=1>インサーション</font></td>
							<td id="rabel" style="width:120px">imp&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "google_imp");?>
							</td>
							<td id="rabel" style="width:120px">click&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "google_click");?>
							</td>
							<td id="rabel" style="width:120px">cost&nbsp;
								<?=$make_sort_link($sort_key, $sort_type, "google_cost");?>
							</td>
						<? } ?>
					</tr>
				<? } ?>
				<tr>
					<td id="rabel"><?=$no?></td>

					<? if ($line_type == 'url') {
							$link_key = "t_url=".urlencode($url_result['disp_key']);
							if ($url_result['yahoo_o_url']) {
								$o_url = $url_result['yahoo_o_url'];
							} else {
								$o_url = $url_result['google_o_url'];
							} ?>
							<td id="default"><a href="http://<?=$o_url?>" target="abount_blanc"><?=$url_result['disp_key']?></a></td>
					<? } else {
							$link_key = "t_key=".$key; ?>
							<td id="default"><?=$url_result['disp_key']?></td>
					<? } ?>

					<? if ($yahoo_media) { ?>
						<? if ($line_type == 'url') { ?>
							<td id="number" style='background-color:<?=$yahoo_background?>'>
						<? if ($url_result["yahoo_key_count"]) { ?>
						<a href="#" onclick="doDetailAction('y_media=1&<?=$link_key?>');">
								<?=number_format($url_result["yahoo_key_count"])?>
							</a>&nbsp;
						<? } else  { ?>
							<?=number_format($url_result["yahoo_key_count"])?>
						<? } ?>
						</td>
					<? } ?>
						<td id="number" style='background-color:<?=$yahoo_background?>'>
							<? if ($url_result["yahoo_ad_count"]) { ?>
							<a href="#" onclick="doDetailAction('y_media=1&<?=$link_key?>&ad=1');">
								<?=number_format($url_result["yahoo_ad_count"])?>
							</a>&nbsp;
							<? } else  { ?>
								<?=number_format($url_result["yahoo_ad_count"])?>
							<? } ?>
						</td>
						<td id="number" style='background-color:<?=$yahoo_background?>'>
							<? if ($url_result["yahoo_ins_count"]) { ?>
							<a href="#" onclick="doDetailAction('y_media=1&<?=$link_key?>&ins=1');">
								<?=number_format($url_result["yahoo_ins_count"])?>
							</a>&nbsp;
							<? } else  { ?>
								<?=number_format($url_result["yahoo_ins_count"])?>
							<? } ?>
						</td>
						<td id="number" style='background-color:<?=$yahoo_background?>'><?=number_format($url_result["yahoo_imp"])?>&nbsp;</td>
						<td id="number" style='background-color:<?=$yahoo_background?>'><?=number_format($url_result["yahoo_click"])?>&nbsp;</td>
						<td id="number" style='background-color:<?=$yahoo_background?>'>&yen;<?=number_format($url_result["yahoo_cost"])?>&nbsp;</td>
					<? } ?>
					<? if ($google_media) { ?>
						<? if ($line_type == 'url') { ?>
							<td id="number" style='background-color:<?=$google_background?>'>
						<? if ($url_result["google_key_count"]) { ?>
							<a href="#" onclick="doDetailAction('g_media=1&<?=$link_key?>');">
								<?=number_format($url_result["google_key_count"])?>
							</a>&nbsp;
						<? } else  { ?>
							<?=number_format($url_result["google_key_count"])?>
						<? } ?>
						</td>
					<? } ?>
					<td id="number" style='background-color:<?=$google_background?>'>
						<? if ($url_result["google_ad_count"]) { ?>
						<a href="#" onclick="doDetailAction('g_media=1&<?=$link_key?>&ad=1');">
							<?=number_format($url_result["google_ad_count"])?>
						</a>&nbsp;
						<? } else  { ?>
							<?=number_format($url_result["google_ad_count"])?>
						<? } ?>
					</td>
					<td id="number" style='background-color:<?=$google_background?>'>
						<? if ($url_result["google_ins_count"]) { ?>
						<a href="#" onclick="doDetailAction('g_media=1&<?=$link_key?>&ins=1');">
							<?=number_format($url_result["google_ins_count"])?>
						</a>&nbsp;
						<? } else  { ?>
							<?=number_format($url_result["google_ins_count"])?>
						<? } ?>
					</td>
					<td id="number" style='background-color:<?=$google_background?>'><?=number_format($url_result["google_imp"])?>&nbsp;</td>
					<td id="number" style='background-color:<?=$google_background?>'><?=number_format($url_result["google_click"])?>&nbsp;</td>
					<td id="number" style='background-color:<?=$google_background?>'>&yen;<?=number_format($url_result["google_cost"])?>&nbsp;</td>
					<? } ?>
				</tr>
			<? $no++;
	} ?>
	</table>
</div>


<div class="btn-area">
	<div class="btn btn-xs btn-default" id="button"  onclick="doExportAction('','');"><a href="#" class="js-href-calceled">Export</a></div>
	<div class="btn btn-xs btn-default" id="button"  onclick="doExportAction('all','');"><a href="#" class="js-href-calceled">日付別Export</a></div>
</div>
