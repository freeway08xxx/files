<div class="row clearfix">
	<ul class="nav nav-tabs">
		<li class="" ng-class="{active: service.isActivate(section.key)}" ng-repeat="(key,section) in model.sectionAll|filterSection:{}:true" ng-cloak> 
			<a href="" ng-click="action.clickSectionTab(section.key)">{{section.name}}</a>
		</li>
	</ul>
</div>
