<ul ng-init="getGlobalNav()" ng-cloak>
	<li ng-repeat="item in nav">
		<!-- normal Link Menu -->
		<a href="/sem/new/{{item.path}}" ng-if="!isDropdownMenu(item)">{{item.name}}</a>

		<!-- Dropdown Menu -->
		<a href ng-if="isDropdownMenu(item)">
			{{item.name}}  <i class="icon-down-dir"></i>
		</a>
		<div class="dropdown" ng-if="isDropdownMenu(item)">
			<ul>
				<li ng-repeat="d in item.dropdown">
					<a href="/sem/new/{{d.path}}">{{d.name}}</a>
				</li>
			</ul>
		</div>
	</li>
</ul>