<div ng-controller="TsktTaskSaveCtrl as tsktTaskSave" class="tskt-task-save">
	<div class="form-container">
	
	<div id="message-field" class="message-field">
		<div ng-if="tsktTaskSave.models.errors" class="alert alert-danger" role="alert">
			<div ng-repeat="error in tsktTaskSave.models.errors">
  				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  				<span class="sr-only">Error:</span>
				{{error.message}}
			</div>
		</div>
	</div>
		<div class="row">
			<div class="col-lg-12">
				<h4 class="title">タスク情報</h4>
				<div class="form-block list-group">
					<div class="controls list-group-item clearfix">
						<div class="row">

							<!-- 1段目左 -->
							<div class="col-lg-6">
								<!-- カテゴリ -->
								<div class="label-item">
									<label class="control-label">カテゴリ <span class="label label-warning">必須</span></label>
									<!-- <span style="color:red;">必須項目です</span> -->
									<select name="select_normal" class="form-control" 
										ng-model="tsktTaskSave.models.category"
										ng-options="value.id as value.name for (key,value) in appConst.category"
										ng-change="tsktTaskSave.services.bindAction.changeCategory()"
									><option value="" disabled>-- 未選択 --</option></select>
								</div>

								<!-- タスクマスタ -->
								<div class="label-item">
									<label class="control-label">タスクマスタ <span class="label label-warning">必須</span></label>
									<!-- <span style="color:red;">必須項目です</span> -->
									<select name="select_normal" class="form-control" 
										ng-model="tsktTaskSave.models.taskMaster"
										ng-options="value.id as value.task_name for value in tsktTaskSave.master.taskMaster"
										ng-change="tsktTaskSave.services.bindAction.changeMaster()"
									><option value="" disabled>-- 未選択 --</option></select>
								</div>

								<!-- タスク名 -->
								<div class="label-item">
									<label class="control-label">タスク名 <span class="label label-warning">必須</span></label>
									<!-- <span style="color:red;">必須項目です</span> -->
									<input type="text" class="form-control" name="text_normal" ng-model="tsktTaskSave.models.taskName" placeholder="タスク名"/>
								</div>
							</div>
							<!-- /1段目左 -->

							<!-- 1段目右 -->
							<div class="col-lg-6">
								<div ss-client-combobox="tsktTaskSave.clientComboboxConfig" ng-model="tsktTaskSave.clientCombobox"></div>
							</div>
							<!-- /1段目右 -->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 2段目 -->
		<div class="row">
			<div class="col-lg-12">
				<h4 class="title">タスク設定</h4>
				<div class="form-block list-group">
					<div class="controls list-group-item clearfix">
						<div class="row">
							<div class="col-lg-6">
								<!-- 種別 -->
								<div class="label-item">
									<label class="control-label">業務種別 <span class="label label-warning">必須</span></label>
									<select class="form-control" 
										ng-model="tsktTaskSave.models.jobType"
										ng-options="key as value for (key,value) in appConst.job.type"
										ng-change="tsktTaskSave.services.bindAction.changeJobType()"
									></select>
								</div>

								<!-- スポット日付 -->
								<div class="label-item">
									<label class="control-label">期限 <span class="label label-warning">必須</span></label><br/>
									<div tskt-datepicker ng-model="tsktTaskSave.models.limit" option="tsktTaskSave.master.datepicker"></div>
								</div>

								<!-- スポット日付 -->
								<div class="label-item transition" ng-if="tsktTaskSave.models.jobType == 2" ng-init="tsktTaskSave.models.routine.frequency='1'">
									<label class="control-label">頻度 <span class="label label-warning">必須</span></label><br/>
										<div class="form-inline">
											<select class="form-control" 
												ng-model="tsktTaskSave.models.routine.frequency"
												ng-options="key as value for (key,value) in appConst.job.frequency"
											></select>

											<span ng-show="tsktTaskSave.models.routine.frequency == 1">
												<div class="btn-group">
													<label class="btn btn-default" ng-model="tsktTaskSave.models.routine.days[key]" btn-checkbox ng-repeat="(key,value) in appConst.job.dayOfWeek">{{value}}</label/>
												</div>
												<label ng-init="tsktTaskSave.models.routine.holidaySkip=true">
													<input type="checkbox" ng-model="tsktTaskSave.models.routine.holidaySkip"> 祝日スキップ
												</label>
											</span>

											<span ng-show="tsktTaskSave.models.routine.frequency == 2" ng-init="tsktTaskSave.models.routine.date=1;tsktTaskSave.models.routine.dailyUnit='1'">
												<select class="form-control" 
													ng-model="tsktTaskSave.models.routine.date"
													ng-options="n for n in [] | range:1:31"
												></select>
												<select class="form-control" 
													ng-model="tsktTaskSave.models.routine.dailyUnit"
													ng-options="key as value for (key,value) in appConst.job.dailyUnit"
												></select>
											</span>

											<span ng-show="tsktTaskSave.models.routine.frequency == 3">
												<div class="btn-group">
													<label class="btn btn-default" ng-model="tsktTaskSave.models.routine.weekNumber[n]" btn-checkbox ng-repeat="n in [] | range:1:6">第{{n}}</label>
												</div>
												<div class="btn-group">
													<label class="btn btn-default" ng-model="tsktTaskSave.models.routine.days[key]" btn-checkbox ng-repeat="(key,value) in appConst.job.dayOfWeek">{{value}}</label/>
												</div>
												<label ng-init="tsktTaskSave.models.routine.holidaySkip=true">
													<input type="checkbox" ng-model="tsktTaskSave.models.routine.holidaySkip"> 祝日スキップ
												</label>
											</span>
										</div>
								</div>

								<!-- ファイル -->
								<div class="label-item">
									<label class="control-label">追加ファイル</label>
									<ss-input-file ng-model="tsktTaskSave.models.file" button-value="'選択'">
								</div>

							</div>
							<div class="col-lg-6">
								<div class="label-item">
									<label class="control-label">詳細</label>
									<textarea ng-model="tsktTaskSave.models.description" class="form-control" rows="6"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /2段目 -->

		<!-- 3段目 -->
		<div class="row transition">
			<div class="col-lg-12">
				<h4 class="control-label">
					ARUJO
					<button ng-if="(tsktTaskSave.arujoMasterTable.data || tsktTaskSave.arujoMasterTable.data.length >= 0)" class="btn btn-info btn-xs" ng-click="tsktTaskSave.services.bindAction.clickAddArujo()">
						追加
					</button>
				</h4>
				<div class="form-block list-group">
					<div class="controls list-group-item clearfix">
						<div>
							<div class="tskt-table" ss-table-rc="tsktTaskSave.arujoMasterTable" external-scopes="tsktTaskSave"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /3段目 -->
		
		<div class="row transition">
			<div class="col-lg-12">
				<h4 class="control-label">
					プロセス
					<button ng-if="(tsktTaskSave.processListTable.data || tsktTaskSave.processListTable.data.length >= 0)" class="btn btn-info btn-xs" ng-click="tsktTaskSave.services.bindAction.clickAddProcessList()">
						追加
					</button>
				</h4>
				<div class="form-block list-group">
					<div class="controls list-group-item clearfix">
						<div class="row">
							<div class="col-lg-12">
								<div ss-table-rc="tsktTaskSave.processListTable" external-scopes="tsktTaskSave"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /row -->



	<!-- 登録 ボタン-->
	<div class="btn-area">
		<button type="submit" class="btn btn-primary" ng-disabled="false" ng-click="tsktTaskSave.services.bindAction.submit()">
			<span ng-show="!tsktTaskSave.master.isSubmit" class="glyphicon glyphicon-cloud-download"></span>
			<img ng-show="tsktTaskSave.master.isSubmit" ng-src="/sem/new/assets/img/ajax-loader.gif" >
			登録	
		</button>
	</div>
<!-- -->
</div>
