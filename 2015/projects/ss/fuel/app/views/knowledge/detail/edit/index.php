<div id="knowledge-main-content"class="row tab-content"  style="display: none">
<div class="tab-pane active" >
<div class="detail edit" ng-controller="fileEditCtr" ng-init="init(<?= Input::param("file_id") ?>)">
	<!-- 以下エラーメッセージの表示 -->
	<?
	if(Session::get_flash("errors")){
		foreach (Session::get_flash("errors") as $error) {
	?>
		<div class="error-msg alert alert-danger"><?= $error ?></div>
	<?
		}
	}
	?>

	<form name="fileEditForm" method="POST" action="/sem/new/knowledge/detail/update" enctype="multipart/form-data" novalidate >
		<div>
			<div class="file-title-block form-group row" >
				<div class="col-xs-6" ng-class="{ 'has-error': fileEditForm.title.$invalid }">
					<input type="text" class="form-control" name="title"
						 ng-model="model.file.file.title" required placeholder="資料タイトル" />
					<p class="help-block" ng-if="fileEditForm.title.$invalid">資料タイトルは必須です</p>
				</div>
				<div class="col-xs-6 text-right">
					<div class="" ng-class="{ 'has-error': fileEditForm.knowledge_file.$invalid }">
							<div class="input-group">
								<input type="file" style="display:none" id="form_knowledge_file" name="knowledge_file"  ng-model="model.file.file.filename" required up-file>
								<input type="text" class="form-control" id="selectedFile" readonly ng-model="model.file.file.filename">
								<span class="input-group-btn">
							 		<button id="selectFile" class="btn btn-default" type="button">ファイル選択</button>
							 	</span>
							</div>
					</div>
				</div>
			</div>

			<div class="description-block form-group">

				<div class="description panel panel-default">
					<div class="panel-heading">詳細</div>
					<div class="panel-body" >
						<textarea name="description" class="form-control" 
							ng-model="model.file.file.description" placeholder="資料詳細">
						</textarea>
					</div>
				</div>
			</div>

			<div class="file-config-block form-group">
				<table class="detail-info-table table table-bordered table-condensed">
					<tr >
						<th ng-class="{ 'has-error': !model.purposes.isSelected }">用途
							<p class="help-block" ng-if="!model.purposes.isSelected">１つ以上選択してください</p>
						</th>
						<td colspan="3" class="category-info">
							<div class="row" ng-cloak>
								<div class="col-xs-12">
								<span class="label label-info" ng-repeat="(key,value) in model.file.purpose_all | filter:{selected:true}:true" style="margin:5px;display: inline-block;font-size:100%;">
									<purpose-bread-crumb purpose-id=value.id purpose-all=model.file.purpose_all></purpose-bread-crumb>
								</span>
								</div>
							</div>
							<hr/>
								
							<div class="row">
								<!-- 大項目 -->
								<div class="col-xs-4">
									<select class="form-control" 
										ng-model="model.purposes.hierarchy.high"
										ng-options="value.name for value in (model.file.purpose_all|filter:{parent_purpose_master_id:null}:true)"
									><option value="" disabled>-- 未選択 --</option></select>
								</div>

								<!-- 中項目 -->
								<div class="col-xs-4" ng-show="model.purposes.hierarchy.high.id != 0" ng-cloak>
									<select class="form-control" 
										ng-model="model.purposes.hierarchy.middle"
										ng-options="value.name for value in (model.file.purpose_all|filter:{parent_purpose_master_id:model.purposes.hierarchy.high.id}:true)"
									><option value="" disabled>-- 未選択 --</option></select>
								</div>
								<div class="col-xs-4" ng-cloak>
									<label 
										class="btn btn-default" 
										ng-repeat="purpose in model.file.purpose_all|filter:{parent_purpose_master_id:model.purposes.hierarchy.middle.id}:true"
										ng-model="purpose.selected" 
										btn-checkbox>
										{{purpose.name}}
									</label>
									<input type="hidden" name="file_purpose[]" ng-model="value.id" ng-value="value.id"
										ng-repeat="(key,value) in model.file.purpose_all | filter:{selected:true}:true" required>
								</div>
							</div>	

						</td>
					</tr>
					<tr ng-class="{ 'has-error': !model.form.isFreeText }" >
						<th>
							検索用タグ <!-- 旧 検索ワード？ -->
							<p class="help-block" ng-if="!model.form.isFreeText">１つ以上設定してください</p>
						</th>
						<td colspan="3" class="tag">
							<div >
								<input type="text" id="form_freetext_for_search" class="form-control"><? //if(!empty($file)) echo $file['freetext_for_search']; ?>
							</div>	
						</td>
					</tr>
					<tr ng-class="{ 'has-error': !model.form.roleSelected }">
						<th>
							権限
							<p class="help-block" ng-if="!model.form.roleSelected">１つ以上選択してください</p>
						</th>
						<td colspan="3" class="role" >
								<label 
									class="btn btn-default" 
									ng-repeat="role in model.file.role_all"
									ng-model="role.selected" 
									btn-checkbox
									ng-cloak
								>{{role.role_name}}</label>
								<input type="hidden" name="file_role[]" ng-model="value.id" ng-value="value.id"
									ng-repeat="(key,value) in model.file.role_all | filter:{selected:true}:true">
						</td>
					</tr>
					<tr ng-class="{ 'has-error': (!model.file.file.search_active_flg && model.file.file.search_active_flg != 0)}">
						<th>
							公開範囲
							<p class="help-block" ng-if="(!model.file.file.search_active_flg && model.file.file.search_active_flg != 0)">１つ以上選択してください</p>
						</th>
						<td>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default" ng-class="{active: model.file.file.search_active_flg == 1}" ng-click="model.file.file.search_active_flg=1">
									<input name="search_active_flg" type="radio" value=1 ng-model="model.file.file.search_active_flg" required>公開
								</label>
								<label class="btn btn-default" ng-class="{active: model.file.file.search_active_flg == 0}" ng-click="model.file.file.search_active_flg=0">
									<input name="search_active_flg" type="radio" value=0 ng-model="model.file.file.search_active_flg" required>未公開
								</label>
							</div>
						</td>
						<th>ファイル更新日</th>
						<td ng-cloak>{{model.file.file.file_update_date}}</td>
					</tr>
				</table>
			</div>

			<input type="hidden" name="type_section" ng-value="typeSection" >
			<input type="hidden" name="file_id" ng-value="model.file.file.id" >

			<div >
				<button type="submit" class="btn btn-success" ng-disabled="(fileEditForm.$invalid || !model.form.roleSelected || !model.form.isFreeText || !model.purposes.isSelected)">登録</button>
			</div>
		</div>
	</form>
</div>
</div>
</div>
