<div class="header navbar" ng-controller="SsHeaderCtrl" ng-cloak>

<div class="container-fluid">
	<div class="navbar-header logo">
		<a href="/sem/global/index.php">
			<img src="/sem/new/assets/img/logo.png" alt="SearchSuite" />
		</a>
	</div>

	<div class="nav header-menu collapse navbar-collapse">
		<ul class="nav navbar-nav navbar-left" ng-init="getGlobalNav()">

			<li class="dropdown fw media-manipulate" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<span class="glyphicon glyphicon-cloud-upload"></span>
					入稿
					<i class="glyphicon glyphicon-triangle-bottom"></i>
				</a>
				<ul class="dropdown-menu">
					<li class="content">
						<div class="row">
							<div class="update dropdown-category" ng-if="checkEmpty(nav.gnavi.media.update)">
								<strong>更新</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.update"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="update dropdown-category" ng-if="checkEmpty(nav.gnavi.media.cooperate)">
 								<strong>連携機能</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.cooperate"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul> 
							</div>
							<div class="support dropdown-category" ng-if="checkEmpty(nav.gnavi.media.support)">
								<strong>サポート</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.support"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="auto_bid dropdown-category" ng-if="checkEmpty(nav.gnavi.media.auto_bid)">
								<strong>自動入札</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.auto_bid"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</div>
					</li>
				</ul>
			</li>

			<li class="dropdown fw report-menu" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<span class="glyphicon glyphicon-stats"></span>
					レポート
					<i class="glyphicon glyphicon-triangle-bottom"></i>
				</a>
				<ul class="dropdown-menu">
					<li class="content">
						<div class="row">
							<div class="output dropdown-category" ng-if="checkEmpty(nav.gnavi.report.output)">
								<strong>レポート出力</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.output"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="setting dropdown-category" ng-if="checkEmpty(nav.gnavi.report.setting)">
								<strong>レポート設定</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.setting"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="conversion dropdown-category" ng-if="checkEmpty(nav.gnavi.report.conversion)">
								<strong>コンバージョン</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.conversion"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

						</div>
					</li>
				</ul>
			</li>

			<li class="dropdown fw tool-menu" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<span class="glyphicon glyphicon-wrench"></span>
					ツール
					<i class="glyphicon glyphicon-triangle-bottom"></i>
				</a>
				<ul class="dropdown-menu">
					<li class="content">
						<div class="row">
							<div class="monitoring dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.monitoring)">
								<strong>モニタリング</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.monitoring"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="design dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.design)">
								<strong>設計サポート</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.design"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="operation dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.operation)">
								<strong>運用サポート</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.operation"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

						</div>
					</li>
				</ul>
			</li>

			<li class="dropdown support-menu" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<span class="glyphicon glyphicon-heart-empty"></span>
					サポート
					<i class="glyphicon glyphicon-triangle-bottom"></i>
				</a>
				<ul class="dropdown-menu">
					<li class="content">
						<div class="row">
							<div class="info dropdown-category" ng-if="checkEmpty(nav.gnavi.support.info)">
								<strong>情報</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.support.info"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>

							<div class="inquiry dropdown-category" ng-if="checkEmpty(nav.gnavi.support.inquiry)">
								<strong>依頼・問い合わせ</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.support.inquiry"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</div>
					</li>
				</ul>
			</li>

			<li class="dropdown config-menu" on-toggle="toggled(open)">
				<a href class="dropdown-toggle" ng-if="checkEmpty(nav.gnavi.config.sub)">
					<span class="glyphicon glyphicon-cog"></span>
				</a>
				<ul class="dropdown-menu">
					<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.config.sub"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
				</ul>
			</li>
		</ul>

		<ul class="nav navbar-nav navbar-right">
			<li class="info-menu dropdown" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<span class="label label-warning info-menu-count" id="info_menu_count">
						0
					</span>
				</a>
				<ul class="dropdown-menu info-menu-list" id="info_menu_list">
				</ul>
			</li>
			<li class="user-menu dropdown" on-toggle="toggled(open)">
				<a href class="dropdown-toggle">
					<i class="glyphicon glyphicon-user"></i>
					<?= Session::get("user_name"); ?>
					<i class="glyphicon glyphicon-triangle-bottom"></i>
				</a>
				<ul class="dropdown-menu personal">

					<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.user.sub">
						<a tabindex="-1" href="{{gnavi.path}}"><i class="glyphicon {{'myglyphicon_'+$index}}"></i> {{gnavi.name}}</a>
					</li>

					<li class="divider"></li>

					<li>
						<a tabindex="-1" href="/sem/global/change_password.php" target="_blank">
							<i class="glyphicon glyphicon-certificate"></i> パスワード変更
						</a>
					</li>
					<li>
						<a tabindex="-1" href="/sem/mora/user_account.php" target="_blank">
							<i class="glyphicon glyphicon-user"></i> 担当アカウント
						</a>
					</li>

					<li>
						<a tabindex="-1" href="/sem/new/sitemap">
							<i class="glyphicon glyphicon-road"></i> サイトマップ
						</a>
					</li>

					<li class="divider"></li>

					<li>
						<a tabindex="-1" href="/sem/global/index.php?type=logout">
							<i class="glyphicon glyphicon-log-out"></i> ログアウト
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>

</div>



</div>
