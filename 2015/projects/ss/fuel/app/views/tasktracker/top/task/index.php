<div ng-controller="TsktTopTaskCtrl as tsktTopTask" class="tskt-top-task">
	<div class="panel panel-default">
		<div class="panel-body option-area">
			<div class="row">
				<div class="col-md-4 form-inline text-right">
					<select class="form-control" 
						ng-model="tsktTopTask.models.status"
						ng-options="key as value for (key,value) in appConst.status"
						ng-change="tsktTopTask.services.bindAction.changeStatus()"
					></select>
				</div>
				<div class="col-md-4">
					<input type="text" class="form-control" name="search_client" placeholder="クライアント名検索" ng-model="tsktTopTask.models.client_name" ng-change="tsktTopTask.services.bindAction.changeClient()">
				</div>
				<div ng-if="tsktTopTask.models.status==1" class="pull-right" style="margin-right:15px;">
					期限
					<div tskt-datepicker ng-model="tsktTopTask.models.dateTo" option="tsktTopTask.datepickerConfig" ></div>
				</div>
				<div ng-if="tsktTopTask.models.status==2" class="pull-right" style="margin-right:15px;">
					終了
					<div tskt-datepicker ng-model="tsktTopTask.models.dateFrom" option="tsktTopTask.datepickerConfig" ></div> 〜
					<div tskt-datepicker ng-model="tsktTopTask.models.dateTo" option="tsktTopTask.datepickerConfig" ></div>
				</div>
			</div>
		</div>
		<div class="main-table">
			<div ss-table-rc="tsktTopTask.taskTable" external-scopes="tsktTopTask"></div>
		</div>
	</div>

	<!--
	<div class="detail">
		<ng-include src="'/sem/new/tasktracker/top/detail/index.html'"></ng-include>
	</div>
	-->
</div>
