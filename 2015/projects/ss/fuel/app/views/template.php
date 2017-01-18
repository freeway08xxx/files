<!DOCTYPE html>
<html>
<head>

<?= View::forge('template/head') ?>

<!-- CSS -->
<?
$common_css = array(
	'bootstrap.min.css',
	'angular-ui-select/select.min.css',
	'angular-loading-bar/loading-bar.min.css',
	'vendor/select2.css',
	'main.css'
);
?>
<?= Asset::css($common_css); ?>

<!-- ページ固有CSS -->
<?= Asset::css($css); ?>

</head>
<body>
<!--
 ________  _______   ________  ________  ________  ___  ___          ________  ___  ___  ___  _________  _______
|\   ____\|\  ___ \ |\   __  \|\   __  \|\   ____\|\  \|\  \        |\   ____\|\  \|\  \|\  \|\___   ___\\  ___ \
\ \  \___|\ \   __/|\ \  \|\  \ \  \|\  \ \  \___|\ \  \\\  \       \ \  \___|\ \  \\\  \ \  \|___ \  \_\ \   __/|
 \ \_____  \ \  \_|/_\ \   __  \ \   _  _\ \  \    \ \   __  \       \ \_____  \ \  \\\  \ \  \   \ \  \ \ \  \_|/__
  \|____|\  \ \  \_|\ \ \  \ \  \ \  \\  \\ \  \____\ \  \ \  \       \|____|\  \ \  \\\  \ \  \   \ \  \ \ \  \_|\ \
    ____\_\  \ \_______\ \__\ \__\ \__\\ _\\ \_______\ \__\ \__\        ____\_\  \ \_______\ \__\   \ \__\ \ \_______\
   |\_________\|_______|\|__|\|__|\|__|\|__|\|_______|\|__|\|__|       |\_________\|_______|\|__|    \|__|  \|_______|
   \|_________|                                                        \|_________|
-->

<div class="wrap" ng-app="ss" ng-controller="SsCtrl">

	<!-- HeaderNav -->
	<?= View::forge('template/header_nav') ?>
	<!-- /HeaderNav -->

	<!-- Content App Area START -->
	<div class="contents-wrap">
		<div class="container-fluid">

			<!-- Title -->
			<div class="row title-area">
				<h3 class="page-title <? if (!empty($logo_img)) { ?> logo <? } ?>" id="page_title">
					<span class="text"><?= $title ?></span>
					<span class="logo-img"><? if (!empty($logo_img)) {?><?= $logo_img ?><? } ?></span>
				</h3>
				<?= $content_nav ?>
			</div>
			<!-- /Title -->

			<!-- Content -->
			<div class="row contents-area tab-content">
				<?= $content ?>

				<!-- msg Alert -->
				<div id="alert_message_attach" class="alert-area"></div>
				<input type="hidden" name="alert_message" value="<?= $alert_message; ?>">
				<!-- /msg Alert -->
			</div>
			<!-- /Content -->

		</div>
	</div>
	<!-- Content Area END -->
</div><!-- /wrap -->

<input type="hidden" name="alert_message" value="<?= $alert_message; ?>">

<!-- // JS Area  //-->

<!-- コンテンツ部分 AngularApp の起動 -->
<script>window.ssContentApp = '<?= $ngapp_name ?>';</script>

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
	'vendor/jquery.cookie.js',

	// Bootstrap Lib
	'bootstrap.min.js',
	'angular-bootstrap/ui-bootstrap-tpls.min.js',

	// JS Lib
	'lodash/lodash.min.js',
	'moment/moment.min.js',
	'moment/ja.js',
	'numeral/numeral.min.js',
	'numeral/ja.js',

	// SS Common JS
	'common/date-selector.js',
	'common/common.js',
	'common/common-util.js',

	// Common Angular Modules
	'common/ng/app.js',
	'common/ng/controllers.js',
	'common/ng/directives.js',
	'common/ng/filters.js',
	'common/ng/services.js'
);
?>
<?= Asset::js($common_js) ?>

<!-- ページ固有JS -->
<?= Asset::js($js) ?>

<?= View::forge('template/ga') ?>
<?= View::forge('template/const') ?>

</body>
</html>
