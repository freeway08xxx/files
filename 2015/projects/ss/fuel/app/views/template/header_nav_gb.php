<div class="header navbar" id="nav1" ng-controller="SsHeaderCtrl" ng-cloak>

	<!-- menuDropdown for responsive -->
	<a class="toggle" gumby-trigger="#nav1 > .row > ul" href="#"><i class="icon-menu"></i></a>

	<div class="row row-fluid">
		<h3 class="columns logo">
			<a href="/sem/global/index.php" title="SearchSuite">
				<img src="/sem/new/assets/img/logo.png" alt="SearchSuite" />
			</a>
		</h3>

		<ul class="nav navbar-nav navbar-left" ng-init="getGlobalNav()">
			<li class="fw media-manipulate">
				<a href="#" class="dropdown-toggle"><i class="icon-publish"></i> 入稿<i class="icon-down-dir"></i></a>
				<div class="dropdown">
					<ul class="dropdown-menu">
						<li class="content">
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.media.update)">
								<strong>更新</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.update"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.media.cooperate)">
 								<strong>連携機能</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.cooperate"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul> 
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.media.support)">
								<strong>サポート</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.support"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.media.auto_bid)">
								<strong>自動入札</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.media.auto_bid"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>

			<li class="fw media-manipulate">
				<a href="#" class="dropdown-toggle"><i class="icon-chart-bar"></i> レポート<i class="icon-down-dir"></i></a>
				<div class="dropdown">
					<ul class="dropdown-menu">
						<li class="content">
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.report.output)">
								<strong>レポート出力</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.output"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.report.setting)">
								<strong>レポート設定</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.setting"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.report.conversion)">
								<strong>コンバージョン</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.report.conversion"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>

			<li class="fw media-manipulate">
				<a href="#" class="dropdown-toggle"><i class="icon-tools"></i> ツール<i class="icon-down-dir"></i></a>
				<div class="dropdown">
					<ul class="dropdown-menu">
						<li class="content">
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.monitoring)">
								<strong>モニタリング</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.monitoring"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class=" dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.design)">
								<strong>設計サポート</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.design"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.tool.operation)">
								<strong>運用サポート</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.tool.operation"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>

			<li class="fw media-manipulate">
				<a href="#" class="dropdown-toggle"><i class="icon-heart-empty"></i> サポート<i class="icon-down-dir"></i></a>
				<div class="dropdown">
					<ul class="dropdown-menu">
						<li class="content">
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.support.info)">
								<strong>情報</strong>
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.support.info"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
							<div class="dropdown-category" ng-if="checkEmpty(nav.gnavi.support.inquiry)">
								<strong>依頼・問い合わせ</strong>
								<ul class="list-unstyled">
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.support.inquiry"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>

			<li class="fw media-manipulate">
				<a href="#" class="dropdown-toggle" ng-if="checkEmpty(nav.gnavi.config.sub)"><i class="icon-cog"></i></a>
				<div class="dropdown">
					<ul class="dropdown-menu">
						<li class="content">
							<div class="dropdown-category cog">
								<ul>
									<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.config.sub"><a href="{{gnavi.path}}">{{gnavi.name}}</a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</li>
		</ul>

		<ul class="columns navbar-nav navbar-right">
			<li class="info-menu">
				<a href="#">
					<span class="label info-menu-count" id="info_menu_count">0</span>
				</a>
				<div class="dropdown info-menu-list"><ul id="info_menu_list" class="dropdown-menu"></ul></div>
			</li>

 
			<li class="user-menu">
				<a href="#">
					<i class="icon-user"></i> <?= Session::get("user_name"); ?> <i class="icon-down-dir"></i>
				</a>
				<div class="dropdown">
					<ul class="dropdown-menu personal">
						<li class="{{gnavi.path | emptyResArg:'hidden'}}" ng-repeat="gnavi in nav.gnavi.user.sub"><a href="{{gnavi.path}}"><i class="{{'icon-'+$index}}"></i> {{gnavi.name}}</a></li>
						<li class="divider"></li>
						<li><a tabindex="-1" href="/sem/global/change_password.php"><i class="icon-cog"></i> パスワード変更</a></li>
						<li><a tabindex="-1" href="/sem/mora/user_account.php"><i class="icon-users"></i> 担当アカウント</a></li>
						<li><a tabindex="-1" href="/sem/new/sitemap/"><i class="icon-list"></i> サイトマップ</a></li>
						<li class="divider"></li>
						<li><a tabindex="-1" href="/sem/global/index.php?type=logout"><i class="icon-logout"></i> ログアウト</a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</div> 