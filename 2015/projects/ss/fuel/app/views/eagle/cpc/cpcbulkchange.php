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

					<!-- CPC一括変更 -->
					<div class="clearfix">
						<fieldset class="columns">
							<legend>CPC一括変更</legend>
							<legend>変更方法</legend>
							<input type="radio" name="cpc_bulk_change_method" value="cpc" checked />CPC
							<input type="radio" name="cpc_bulk_change_method" value="mba" />MBA
							<input type="radio" name="cpc_bulk_change_method" value="default_cpc" />Default CPC
							</br>
							</br>
							<span class="cpc_bulk_change_device">
								<legend>変更デバイス</legend>
								<input type="radio" name="cpc_bulk_change_device" value="pc" checked />PC
								<input type="radio" name="cpc_bulk_change_device" value="sp" />SP
								<input type="radio" name="cpc_bulk_change_device" value="pc_sp" />PC/SP
								</br>
								</br>
							</span>
							<legend>変更内容</legend>
							下記の情報を１行ずつ入力してください。</br>各フィールドはタブで区切ってください。
							</br>
							<textarea name="cpc_bulk_change_text" rows="3" cols="65"></textarea>
							</br>
							</br>
							<? if (isset($get_structure_not_disp)) { ?>
								<div class="medium primary btn" id="bulk_edit" style="display:none"><a href="#" class="js-href-canceled">入稿</a></div>
								<div class="medium primary btn" id="bulk_cpc_change_dl"><a href="#" class="js-href-canceled">CPC変更内容DL</a></div>
							<? } else { ?>
								<div class="medium primary btn" id="cpc_bulk_change_set"><a href="#" class="js-href-canceled">設定</a></div>
							<? } ?>
							<div class="medium primary btn" id="cpc_bulk_change_close"><a href="#" class="js-href-canceled">閉じる</a></div>
						</fieldset>
					</div>
					<input type="hidden" name="action_type" />
				</form>
			</div>
		</div>
	</div>

	<!-- JS -->
	<?= Asset::js("eagle/cpc/cpc-bulk-change.js") ?>
	<?= Asset::js("eagle/cpc/cpc-validation.js") ?>
	<?= Asset::js("eagle/cpc/cpc-common.js") ?>
	<?= Asset::js("eagle/eagle-common.js") ?>

	<?= View::forge('template/ga') ?>
</body>
</html>
