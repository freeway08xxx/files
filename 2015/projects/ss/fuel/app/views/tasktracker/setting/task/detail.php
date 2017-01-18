<div class="tskt-setting-task-detail" ng-controller="TsktSettingTaskDetailCtrl as tsktSettingTaskDetail">
	<div ui-view class="transition">
		<!--
		<div class="row">
			<ul class="breadcrumb">
				<li ><a ui-sref="setting.task({categoryId:tsktSetting.models.params.categoryId})">タスク一覧</a></li>
				<li class="active">{{tsktSettingTaskDetail.models.task.task_name}}</li>
			</ul>
		</div>
		-->
		
		<h4> タスク 詳細 </h4>
		<div class="list-group">
			<div class="list-group-item">
				<div class="title">
					<h4>{{tsktSettingTaskDetail.models.task.task_name}}</h4>
				</div><hr/>
				<div class="content">
					<div class="content-item"> 
						<h5>
							プロセス 
							<span class=""><button style="margin-bottom:5px;" class="btn btn-xs btn-info" ng-click="tsktSettingTaskDetail.services.openAddProcess()">追加</button></span>
						</h5>
						
						<div class="main-table">
							<div ss-table-rc="tsktSettingTaskDetail.table.process" external-scopes="tsktSettingTaskDetail"></div>
						</div>
					</div>
	
					<div class="content-item"> 
						<h5>
							ARUJO 
							<span class=""><button style="margin-bottom:5px;" class="btn btn-xs btn-info" ng-click="tsktSettingTaskDetail.services.openAddArujo()">追加</button></span>
						</h5>
						
						
						<div class="main-table">
							<div ss-table-rc="tsktSettingTaskDetail.table.arujo" external-scopes="tsktSettingTaskDetail"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
