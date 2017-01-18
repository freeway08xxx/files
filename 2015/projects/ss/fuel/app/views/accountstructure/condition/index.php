<div class="container-fluid" ng-scrollable>
	<div class="row clearfix">
		<ul class="nav nav-tabs">
			<li class="tab-account" ng-class="{active: settings.tab === 'account'}"><a href="" ng-click="settings.tab = 'account'">アカウント</a></li>
			<li class="tab-campaign" ng-class="{active: settings.tab === 'filter'}"><a href="" ng-click="settings.tab = 'filter'">検索条件入力</a></li>
		</ul>
	</div>
	
	<form name="accountstructure_form" id="accountstructure_form" method="POST">	
		<div class="row tab-content">
			<div class="tab-pane form-container clearfix" ng-class="{active: settings.tab === 'account'}" id="account">
				<?= $HMVC_account ?>
			</div>
			<div class="tab-pane form-container clearfix" ng-class="{active: settings.tab === 'filter'}" id="filter">
				<?= $HMVC_filter ?>
			</div>
		</div>
		<button type="button" class="btn btn-primary" ng-if="settings.tab === 'account'" ng-click="submit()" ng-disabled="accountstructure_form.$invalid">アカウント設定内容を取得する</button>
	</form>
</div>