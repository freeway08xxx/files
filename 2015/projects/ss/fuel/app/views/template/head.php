<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="description" content="">

<meta name="robots" content="noindex, nofollow, noarchive">
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="expires" content="Fri, 01 Jan 1990 00:00:00 GMT">
<meta http-equiv="cache-control" content="no-cache">

<title><?= $title ?> | <?= SYSTEM_NAME ?></title>
<link rel="shortcut icon" href="/sem/new/assets/img/favicon.ico" />

<input type="hidden" id="websocket_host" name="websocket_host" value="<?= WEBSOCKET_HOST; ?>">
<input type="hidden" id="header_user_id" value="<?= Session::get("user_id_sem"); ?>">

<!-- jQuery ※AngularJSのjqLiteをオーバーライドするため最初にロード -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/sem/new/assets/lib/js/jquery/jquery.min.js"><\/script>')</script>

<!-- AngularJS -->
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.27/angular.min.js"></script>
<script>window.angular || document.write('<script src="/sem/new/assets/lib/js/angular/angular.min.js"><\/script>')</script>
<?= Asset::js('angular-i18n/angular-locale_ja-jp.js') ?>