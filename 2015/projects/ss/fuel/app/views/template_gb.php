<!DOCTYPE html>
<html>
<head>

<?= View::forge('template/head') ?>

<!-- CSS -->
<?
$common_css = array(
	'vendor/jquery-ui.min.css',
	'vendor/jquery.ui.theme.css',
	'angular-loading-bar/loading-bar.min.css',
	'vendor/select2.css',
	'vendor/gumby.css',
	'main_gumby.css'
);
?>
<?= Asset::css($common_css) ?>

<!-- ページ固有CSS -->
<?= Asset::css($css) ?>

</head>
<body>

<div class="modal" id="modal1">
	<div class="content">
		<a class="switch" gumby-trigger="|#modal1"><i class="icon-cancel-circled" /></i>閉じる</a>
		<div class="row">
			<div class="eleven columns centered popup-base" id="popup_base">
			</div>
		</div>
	</div>
</div>

<div class="wrap" ng-app="ss">
	<!-- HeaderNav -->
	<?= View::forge('template/header_nav_gb') ?>
	<!-- /HeaderNav -->

	<!-- Content Area START -->
	<div class="container row row-fluid clearfix">
		<?= $sidebar ?>

		<div class="main-content">
			<!-- Title -->
			<h3 class="" id="page_title"><?= $title ?></h3>
			<!-- /Title -->

			<!-- Content -->
			<?= $content ?>
			<!-- /Content -->
		</div>
	</div>
	<!-- Content Area END -->
</div>

<input type="hidden" name="alert_message" value="<?= $alert_message; ?>">

<!-- // JS Area  //-->

<!-- コンテンツ部分 AngularApp の起動 -->
<script>
	window.ssContentApp = '<?= (!empty($ngapp_name)) ? $ngapp_name : "dummy" ?>';
</script>

<?
$common_js = array(
	'modernizr/modernizr.js',

	// AngularJS Lib
	'angular-route/angular-route.min.js',
	'angular-animate/angular-animate.min.js',
	'angular-sanitize/angular-sanitize.min.js',
	'angular-ui-select/select.min.js',
	'angular-ui-utils/ui-utils.min.js',
	'angular-loading-bar/loading-bar.min.js',
	'checklist-model/checklist-model.js',

	// jQuery Lib
	'vendor/jquery-ui-1.10.4.custom.min.js',
	'vendor/jquery.cookie.js',
	'vendor/jquery.ui.datepicker-ja.js',

	// Bootstrap Lib
	'angular-bootstrap/ui-bootstrap-tpls.min.js',

	// Gumby Lib
	'vendor/gumby.min.js',

	// JS Lib
	'lodash/lodash.min.js',
	'moment/moment.min.js',
	'moment/ja.js',
	'numeral/numeral.min.js',
	'numeral/ja.js',
	'vendor/select2.js',

	// SS Common JS
	'common/date-selector.js',
	'common/common.js',
	'common/common-util.js',

	// Common Angular Modules
	'common/ng/app.js',
	'common/ng/controllers.js',
	'common/ng/directives.js',
	'common/ng/filters.js',
	'common/ng/services.js',

	'common/ng/dummy.js'
);
?>
<?= Asset::js($common_js) ?>

<!-- ページ固有JS -->
<?= Asset::js($js) ?>

<?= View::forge('template/ga') ?>

</body>
</html>
