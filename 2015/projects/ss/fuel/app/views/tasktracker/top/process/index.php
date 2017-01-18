<div ng-controller="TsktTopProcessCtrl as tsktTopProcess" class="tskt-top-process">
	<div class="panel panel-default">
		<div class="panel-body option-area">
			<div class="row">
				<div class="col-md-4 form-inline text-right">
					<select class="form-control" 
						ng-model="tsktTopProcess.models.status"
						ng-options="key as value for (key,value) in appConst.status"
						ng-change="tsktTopProcess.services.bindAction.changeStatus()"
					></select>
				</div>
				<div class="col-md-4">
					<input type="text" class="form-control" name="search_client" placeholder="クライアント名検索" ng-model="tsktTopProcess.models.client_name" ng-change="tsktTopProcess.services.bindAction.changeClient()">
				</div>
				<div ng-if="tsktTopProcess.models.status==1" class="pull-right" style="margin-right:15px;">
					開始：
					<div tskt-datepicker ng-model="tsktTopProcess.models.dateTo" option="tsktTopProcess.datepickerConfig" ></div>
				</div>
				<div ng-if="tsktTopProcess.models.status==2" class="pull-right" style="margin-right:15px;">
					終了
					<div tskt-datepicker ng-model="tsktTopProcess.models.dateFrom" option="tsktTopProcess.datepickerConfig" ></div> 〜
					<div tskt-datepicker ng-model="tsktTopProcess.models.dateTo" option="tsktTopProcess.datepickerConfig" ></div>
				</div>
			</div>
		</div>
		<div class="main-table">
			<div ss-table-rc="tsktTopProcess.processTable" external-scopes="tsktTopProcess"></div>
		</div>
	</div>

	<!--
	<div class="detail">
		<ng-include src="'/sem/new/tasktracker/top/detail/index.html'"></ng-include>
	</div>
	-->
</div>
