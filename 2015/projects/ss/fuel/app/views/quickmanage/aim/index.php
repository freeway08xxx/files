<div class="container-fluid" ng-controller="QuickManageAimCtrl">
	<div class="row clearfix">
		<ul class="nav nav-tabs">
			<li class="tab-aim-list" ng-class="{active: settings.tab === 'list'}"><a href="" ng-click="settings.tab = 'list'">目標設定一覧</a></li>
			<li class="tab-aim-form" ng-class="{active: settings.tab === 'form'}"><a href="" ng-click="settings.tab = 'form'">目標設定を登録</a></li>
		</ul>
	</div>

	<div class="row tab-content">
		<!-- 目標設定一覧 -->
		<div class="tab-pane" ng-class="{active: settings.tab === 'list'}">
			<?= $HMVC_list; ?>
		</div>
		<!-- 目標設定 -->
		<div class="tab-pane form-container" ng-class="{active: settings.tab === 'form'}">
			<?= $HMVC_form; ?>
		</div>
	</div>
</div>