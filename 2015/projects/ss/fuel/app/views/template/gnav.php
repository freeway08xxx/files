<ul class="nav navbar-nav" ng-init="getGlobalNav()" ng-cloak>
	<li ng-repeat="item in nav">
		<a href="/sem/new/{{item.path}}" ng-if="!isDropdownMenu(item) && item.path">{{item.name}}</a>

		<span ng-if="isDropdownMenu(item)" class="dropdown" on-toggle="toggled(open)">
			<a href class="dropdown-toggle">
				{{item.name}} <span class="caret">
			</a>
			<ul class="dropdown-menu">
				<li ng-repeat="d in item.dropdown">
					<a href="/sem/new/{{d.path}}">{{d.name}}</a>
				</li>
			</ul>
		</span>

	</li>
</ul>
