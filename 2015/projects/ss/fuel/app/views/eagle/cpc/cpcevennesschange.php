<!DOCTYPE html>
<html>
<head>

<?= View::forge('template/head') ?>

<!-- CSS -->
<?
$common_css = array(
	'vendor/jquery-ui.min.css',
	'vendor/jquery.ui.theme.css',
	'vendor/gumby.css',
	'main_gumby.css'
);
?>
<?= Asset::css($common_css) ?>

<!-- CSS -->
<?= Asset::css("eagle/cpc.css") ?>

</head>
<body>
	<div class="wrap">
		<div class="container row row-fluid clearfix">
			<div class="main-content">
				<form method="post" name="form01">

					<!-- CPC一律変更 -->
					<div class="clearfix">
						<fieldset class="columns">
							<legend>CPC一律変更</legend>
							<legend>変更デバイス</legend>
							<input type="radio" name="cpc_evenness_change_device" value="pc" checked />PC
							<input type="radio" name="cpc_evenness_change_device" value="sp" />SP
							<input type="radio" name="cpc_evenness_change_device" value="pc_sp" />PC/SP
							</br>
							</br>
							<legend>変更方法</legend>
							<input type="radio" name="cpc_evenness_change_method" value="amount" checked />金額
							<input type="radio" name="cpc_evenness_change_method" value="percent" />パーセント
							</br>
							</br>
							<legend>変更内容</legend>
							一律で
							<input type="text" name="cpc_evenness_change_value" />
							<span id="cpc_evenness_change_unit">円</span>
							<select name="cpc_evenness_change_type">
								<option value="up" selected>上げる</option>
								<option value="down">下げる</option>
							</select>
							</br>
							</br>
							<? if (isset($get_structure_not_disp)) { ?>
								<div class="medium primary btn" id="evenness_edit" style="display:none"><a href="#" class="js-href-canceled">入稿</a></div>
								<div class="medium primary btn" id="evenness_cpc_change_dl"><a href="#" class="js-href-canceled">CPC変更内容DL</a></div>
							<? } else { ?>
								<div class="medium primary btn" id="cpc_evenness_change_set"><a href="#" class="js-href-canceled">設定</a></div>
							<? } ?>
							<div class="medium primary btn" id="cpc_evenness_change_close"><a href="#" class="js-href-canceled">閉じる</a></div>
						</fieldset>
					</div>
					<input type="hidden" name="action_type" />
				</form>
			</div>
		</div>
	</div>

	<!-- JS -->
	<?= Asset::js("eagle/cpc/cpc-evenness-change.js") ?>
	<?= Asset::js("eagle/cpc/cpc-validation.js") ?>
	<?= Asset::js("eagle/cpc/cpc-common.js") ?>
	<?= Asset::js("eagle/eagle-common.js") ?>

	<?= View::forge('template/ga') ?>
</body>
</html>
