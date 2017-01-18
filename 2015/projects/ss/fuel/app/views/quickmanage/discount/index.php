<div class="container-fluid" ng-controller="QuickManageDiscountCtrl">
	<div class="row clearfix">
		<ul class="nav nav-tabs">
			<li class="tab-discount-list" ng-class="{active: settings.tab === 'list'}"><a href="" ng-click="settings.tab = 'list'">値引設定一覧</a></li>
			<li class="tab-discount-form" ng-class="{active: settings.tab === 'form'}"><a href="" ng-click="settings.tab = 'form'">値引設定を登録</a></li>
		</ul>
	</div>

	<div class="row tab-content">
		<!-- 値引設定一覧 -->
		<div class="tab-pane" ng-class="{active: settings.tab === 'list'}">
			<?= $HMVC_list; ?>
		</div>
		<!-- 値引設定 -->
		<div class="tab-pane form-container" ng-class="{active: settings.tab === 'form'}">
			<?= $HMVC_form; ?>
		</div>
	</div>
</div>