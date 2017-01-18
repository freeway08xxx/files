<div class="modal-body">
	<button type="button" class="close" ng-click="close()">×</button>
	<h4>タスク 追加</h4>
	<div>
		<div class="label-item">
			<label>タスク名</label>
			<input type="text" class="form-control" name="text_normal" ng-model="models.name" placeholder="タスク名を入力"/>
		</div>
		<button class="btn btn-primary" ng-click="submit()">登録</button>
	</div>
</div>


