<div class="modal-body">
	<button type="button" class="close" ng-click="close()">×</button>
	<h4>ARUJO 追加</h4>
	<div>
		<div class="label-item">
			<label>ARUJO名</label>
			<input type="text" class="form-control" name="text_normal" ng-model="models.name" placeholder="名前を入力"/>
		</div>
		<div class="label-item">
			<label>媒体</label>
			<select name="select_normal" class="form-control" 
				ng-model="models.media_id"
				ng-options="key as value for (key,value) in appConst.media_list"
			><option value="" disabled>-- 未選択 --</option></select>
		</div>
		<div class="label-item">
			<label>値</label>
			<input type="text" class="form-control" name="text_normal" ng-model="models.value" placeholder="値を入力"/>
		</div>
		<div class="label-item">
			<label>説明</label>
			<textarea class="form-control" ng-model="models.arujo_description" placeholder="説明を入力" rows="4"></textarea>
		</div>
		<button class="btn btn-primary" ng-click="submit()">登録</button>
	</div>
</div>


