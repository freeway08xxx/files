<div id="tskt-taskDetail" ng-controller="TsktTopTaskDetailCtrl as tsktTopTaskDetail" class="tskt-top-task-detail">
	<div class="list-group">
		<div class="list-group-item transition" ng-show="tsktTopTaskDetail.models.isView">
			<div class="row tasks-detail-head">
				<div class="col-md-12">
					<h4>
					
						<span ng-if="!(tsktTopTaskDetail.models.isEditable)" class="transition">
							<span class="label label-success">{{tsktTopTaskDetail.models.task.statusName}}</span> {{tsktTopTaskDetail.models.task.task_name}}
							<small>{{tsktTopTaskDetail.models.task.task_limit_datetime | moment:'YYYY.M.D HH:mm'}}まで</small>
						</span>
						<span ng-if="(tsktTopTaskDetail.models.isEditable)" class="form-inline transition">
							<span class="label label-success">
								{{tsktTopTaskDetail.models.task.statusName}}
							</span> 
							<small style="margin-left:5px;">タスク名 </small>
							<input style="width:300px;" type="text" class="form-control" name="text_normal" ng-model="tsktTopTaskDetail.models.editModel.task_name" placeholder="通常テキスト"/>
							<small style="margin-left:5px;">期限 </small>
							<div tskt-datepicker ng-model="tsktTopTaskDetail.models.editModel.task_limit_datetime" option="tsktTopTaskDetail.models.datepicker"></div>
						</span>

						<span class="pull-right">
							<span ng-if="( !tsktTopTaskDetail.models.isEditable  && tsktTopTaskDetail.models.task.task_status == 1 )" class="transition">
								<button ng-click="tsktTopTaskDetail.bindAction.clickRemoveTask()" class="btn btn-danger btn-sm">タスク削除</button>
								<button ng-click="tsktTopTaskDetail.bindAction.clickEditButton()" class="btn btn-info btn-sm">編集</button>
							</span>
							<span ng-if="tsktTopTaskDetail.models.isEditable" class="transition">
								<button ng-click="tsktTopTaskDetail.bindAction.clickCancelButton()" class="btn btn-default btn-sm">キャンセル</button>
								<button ng-click="tsktTopTaskDetail.bindAction.clickSaveButton()" class="btn btn-primary btn-sm">登録</button>
							</span>
						</span>
					</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<table class="table">
						<tr>
							<th style="width:50%;">タスクID</th>
							<td>{{tsktTopTaskDetail.models.task.id}}</td>
						</tr>
						<tr>
							<th style="width:50%;">クライアント</th>
							<td>{{tsktTopTaskDetail.models.task.client_name}}</td>
						</tr>
						<tr>
							<th style="width:50%;">カテゴリ</th>
							<td>{{tsktTopTaskDetail.models.task.category_name}}</td>
						</tr>
						<tr>
							<th style="width:50%;">種別</th>
							<td>
								<span>{{tsktTopTaskDetail.models.task.typeName}}</span>
								<span class="pull-right" ng-if="tsktTopTaskDetail.models.task.job_type == '2'">
									<button ng-click="tsktTopTaskDetail.bindAction.openRoutineSetting()" class="btn btn-xs btn-info">定例設定</button>
								<span>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table class="table">
						<tr>
							<th style="width:20%;" >タスク詳細</th>
							<td>
								<span class="transition" ng-if="!(tsktTopTaskDetail.models.isEditable)" ng-bind-html="tsktTopTaskDetail.models.task.task_description | noHTML | newlines"></span>
								<span class="transition" ng-if="(tsktTopTaskDetail.models.isEditable)"><textarea class="form-control" rows="5" ng-model="tsktTopTaskDetail.models.editModel.task_description"></textarea></span>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="row clearfix">
				<div class="col-md-12">
					<tabset>
						<tab heading="プロセス">
							<div class="transition" ng-show="!(tsktTopTaskDetail.models.isEditable)">
								<div ss-table-rc="tsktTopTaskDetail.models.processListTable" external-scopes="tsktTopTaskDetail"></div>
							</div>
							<div class="transition" ng-show="(tsktTopTaskDetail.models.isEditable)">
								<button class="btn btn-info btn-sm" ng-click="tsktTopTaskDetail.bindAction.clickAddProcess()">
									<i class="glyphicon glyphicon-plus"></i> 追加
								</button>
								<div ss-table-rc="tsktTopTaskDetail.models.processEditableTable" external-scopes="tsktTopTaskDetail"></div>
							</div>
						</tab>
						<tab heading="アカウント">
							<div class="transition" ng-show="!(tsktTopTaskDetail.models.isEditable)">
								<div ss-table-rc="tsktTopTaskDetail.models.accountListTable" external-scopes="tsktTopTaskDetail"></div>
							</div>
							<div class="transition" ng-show="(tsktTopTaskDetail.models.isEditable)">
								<div ss-client-combobox="tsktTopTaskDetail.models.clientComboboxConfig" ng-model="tsktTopTaskDetail.models.clientCombobox"></div>
							</div>
						</tab>
						<tab heading="タスクファイル">
							<!--<div ng-show="!(tsktTopTaskDetail.models.isEditable)">-->
								<div ss-table-rc="tsktTopTaskDetail.models.fileListTable" external-scopes="tsktTopTaskDetail"></div>
							<!--</div>-->
							<div class="transition" ng-show="(tsktTopTaskDetail.models.isEditable)">
								<h5>追加ファイル</h5>
								<span><ss-input-file ng-model="tsktTopTaskDetail.models.editModel.file" button-value="'選択'"></ss-input-file></span>
							</div>
						</tab>
						<tab heading="ARUJO">
							<div class="transition" ng-show="!(tsktTopTaskDetail.models.isEditable)">
								<div ss-table-rc="tsktTopTaskDetail.models.arujoListTable" external-scopes="tsktTopTaskDetail"></div>
							</div>
							<div class="transition" ng-show="(tsktTopTaskDetail.models.isEditable)">
								<div ss-table-rc="tsktTopTaskDetail.models.arujoEditableTable" external-scopes="tsktTopTaskDetail"></div>
							</div>
						</tab>
  					</tabset>
				</div>
			</div>
		</div>
		<!-- //list-group-item -->
	</div>
</div>
