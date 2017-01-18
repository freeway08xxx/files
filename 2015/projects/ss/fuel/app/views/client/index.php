<div class="tab-pane active client-setting" ng-controller="clBaseCtrl as base">

	<div class="form-container row">
		<div class="col-xs-7 col-sm-7 col-md-6 pull-right">
			<div class="form-block list-group client-combobox-wrap inline-client">
				<div class="controls list-group-item">
					<div class="common-client-combobox" ss-client-combobox="base.clientComboboxConfig"
					 ng-model="base.models.client"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-4 col-sm-3 col-lg-2">
			<ul class="side-menu list-unstyled">
				<li ng-repeat="tab in base.models.tabs" ng-click="base.methods.chgLocation(tab.key)">
					<a ng-class="{active: tab.is_active}" ng-bind="tab.title"></a>
				</li>
			</ul>
		</div>

		<div class="col-xs-8 col-sm-9 col-lg-10">
			<div ng-view></div>
		</div>
	</div>
</div>