<div class="container-fluid" ng-controller="QuickManageReportCtrl" ng-class="{reportdisplay: settings.is_report_display === true}">
	<div class="row clearfix">
		<ul class="nav nav-tabs">
			<li class="tab-form" ng-class="{active: settings.tab === 'form'}"><a href="" ng-click="settings.tab = 'form'">レポート作成</a></li>
			<li class="tab-report" ng-class="{active: settings.tab === 'list'}"><a href="" ng-click="settings.tab = 'list'">作成済レポート一覧</a></li>
		</ul>
	</div>

	<div class="row tab-content">
		<div class="tab-pane form-container clearfix" ng-class="{active: settings.tab === 'form'}" id="form">
			<?= $HMVC_report; ?>
		</div>
		<div class="tab-pane" ng-class="{active: settings.tab === 'list'}" id="list">
			<?= $HMVC_history; ?>
		</div>
	</div>
</div>

