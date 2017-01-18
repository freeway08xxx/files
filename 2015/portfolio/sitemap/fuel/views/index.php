<!-- <div ng-controller="SsHeaderCtrl"> -->
<div ng-controller="SitemapBaseCtrl as baseCtrl" class="sitemap" ng-cloak>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-cloud-upload"></i> 入稿</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-6 col-md-3" ng-if="baseCtrl.nav.gnavi.media.insert">
					<dl>
						<dt>新規・追加</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.media.insert"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>更新系</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.media.update"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>サポート</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.media.support"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>自動入札</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.media.auto_bid"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>連携機能</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.media.cooperate"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>
			</div>
		</div>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-stats"></i> レポート</h3>
		</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>レポート出力</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.report.output"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>レポート設定</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.report.setting"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>コンバージョン</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.report.conversion"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-wrench"></i> ツール</h3>
		</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>モニタリング</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.tool.monitoring"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>設計サポート</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.tool.design"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>運用サポート</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.tool.operation"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-heart-empty"></i> サポート</h3>
		</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>情報</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.support.info"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

				<div class="col-xs-6 col-md-3">
					<dl>
						<dt>依頼・問合せ</dt>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.support.inquiry"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-cog"></i> 設定</h3>
		</div>
		<div class="panel-body">

			<div class="row">
				<div class="col-xs-6 col-md-3">
					<dl>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.config.sub"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> ユーザーメニュー</h3>
		</div>
		<div class="panel-body">

			<div class="row">

				<div class="col-xs-6 col-md-3">
					<dl>
						<dd ng-repeat="navi in baseCtrl.nav.gnavi.user.sub"><a href="{{navi.path}}" class="{{navi.path | emptyResArg:'disabled'}}">{{navi.name}}<span class="label label-info">{{navi.label}}</span></a></dd>
					</dl>
				</div>

			</div>
		</div>
	</div>
</div>






