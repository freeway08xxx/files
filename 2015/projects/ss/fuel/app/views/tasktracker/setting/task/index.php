<div ng-controller="TsktSettingTaskCtrl as tsktSettingTask">
	<div ui-view class="transition">
		<h4> タスク一覧 
			<span class=""><button style="margin-bottom:5px;" class="btn btn-xs btn-info" ng-click="tsktSettingTask.services.addTask()">追加</button></span>
		</h4>
		<div>
			<div class="main-table">
				<div ss-table-rc="tsktSettingTask.taskMasterTable" external-scopes="tsktSettingTask"></div>
			</div>
		</div>
	</div>
</div>
