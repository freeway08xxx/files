<div class="modal-body">
	<button type="button" class="close" ng-click="close()">×</button>
	<h4>定例設定</h4>
	<div>
		<span>
			自動更新終了日：
		</span>
		<div tskt-datepicker ng-model="models.datepicker" option="models.datepickerConfig"></div>

		<button class="btn btn-primary" ng-click="clickSubmit()">登録</button>

	</div>
</div>


