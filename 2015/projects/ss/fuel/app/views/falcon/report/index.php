<div class="form-container clearfix report">
	<!-- side menu (position mod) -->
	<div class="row menu">
		<div class="col-xs-4 col-sm-3">
			<ul class="nav nav-pills nav-stacked">
				<li ng-repeat="tab in common.config.tab"
				ng-class="{active: report.tab.isActive('{{tab.id}}'), option: tab.id === 'kw'}"
				ng-show="report.models.tab.status[tab.id].is_show">
					<small ng-show="tab.id === 'kw'">OPTION</small>
					<a ng-click="report.tab.set(tab.id)"
					ng-if="!report.models.tab.status[tab.id].is_disabled">
						<span class="glyphicon glyphicon-pencil"></span>
						{{tab.name}}
					</a>
					<span class="tablink-disabled" ng-if="report.models.tab.status[tab.id].is_disabled"
					tooltip="{{report.models.tab.tooltip[tab.id]}}">
						<span class="glyphicon glyphicon-pencil"></span>
						{{tab.name}}
					</span>

				</li>
			</ul>

			<falcon-template ng-model="report.models.template"></falcon-template>
		</div>
	</div>

	<!-- setting area -->
	<form name="falconReportForm" id="reportform">

		<div class="row form-content {{common.getReportTab()}}">
			<div class="col-xs-4 col-sm-3">
			</div>

			<div class="tab-content col-xs-8 col-sm-9" ng-show="common.models.client.client">
				<div class="tab-pane setting setting-format" ng-class="{active: report.tab.isActive('format')}" ng-controller="FalconReportFormatCtrl as format">
					<?= $view_setting_format ?>
				</div>
				<div class="tab-pane setting setting-display" ng-class="{active: report.tab.isActive('display')}" ng-controller="FalconReportDisplayCtrl as display">
					<?= $view_setting_display ?>
				</div>
				<div class="tab-pane setting setting-sheet" ng-class="{active: report.tab.isActive('sheet')}" ng-controller="FalconReportSheerCtrl as sheet">
					<?= $view_setting_sheet ?>
				</div>
				<div class="tab-pane setting setting-kw" ng-class="{active: report.tab.isActive('kw')}" ng-controller="FalconReportKwCtrl as kw">
					<?= $view_setting_kw ?>
				</div>
				<div class="tab-pane setting setting-cp" ng-class="{active: report.tab.isActive('cp')}" ng-controller="FalconReportCpSettingCtrl as cp">
					<?= $view_setting_cp ?>
				</div>
				<div class="tab-pane setting setting-aim" ng-class="{active: report.tab.isActive('aim')}" ng-controller="FalconReportAimCtrl as aim">
					<?= $view_setting_aim ?>
				</div>
			</div>
		</div>

		<!-- bottom btn area -->
		<div class="btn-area clearfix" id="btn_area">
			<button type="button" class="btn btn-primary pull-left" ng-click="report.create()"
			ng-disabled="!report.models.is_valid">
				レポートを作成する
			</button>

			<div class="msg" ng-show="!report.models.is_valid">
				<span class="glyphicon glyphicon-exclamation-sign"></span>
				{{report.models.msg}}
			</div>

			<button type="button" class="btn btn-sm btn-default pull-right"
			ng-click="report.save()"
			ng-class="{disabled: is_processing, hidden: !common.models.client.client}">
				<span class="glyphicon glyphicon-new-window"></span>
				テンプレートとして保存
			</button>
		</div>

	</form>

	<!-- CPSetting CSV Form -->
	<form enctype="multipart/form-data" name="cp_csvform" id="cp_csvform" class="hidden"></form>

	<!-- Aim Category Setting CSV Form -->
	<form enctype="multipart/form-data" name="aim_csvform" id="aim_csvform" class="hidden"></form>
</div>

