<!-- 通常テキスト -->
<h5 class="control-label">通常 <span class="label label-warning">必須</span></h5>
<input type="text" class="form-control" name="text_normal" ng-model="models.form.text.normal" placeholder="通常テキスト"/>

<!-- 右アイコンん付きテキスト -->
<h5 class="control-label">右アイコン</h5>
<div class="input-group">
	<div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
	<input type="text" class="form-control" name="text_left" ng-model="models.form.text.left" placeholder="右アイコン" />
</div>

<!-- 左アイコン付きテキスト -->
<h5 class="control-label">左アイコン</h5>
<div class="input-group">
	<input type="text" class="form-control" name="text_right" ng-model="models.form.text.right" placeholder="左アイコン" />
	<div class="input-group-addon"><span class="glyphicon glyphicon-search"></span></div>
</div>

<!-- 日付付きテキスト -->
<h5 class="control-label">日付入力</h5>
<div class="input-group">
	<input type="text" class="form-control" name="text_date"  ng-model="models.form.text.date" placeholder="日付テキスト"
		is-open="datepicker.opened" min-date="datepicker.minDate" max-date="datepicker.maxDate" datepicker-popup="{{datepicker.format}}" datepicker-options="datepicker.dateOptions" />
	<span class="input-group-btn">
		<button type="button" class="btn btn-default" ng-click="datepicker.open($event)"><i class="glyphicon glyphicon-calendar"></i></button>
	</span>
</div>
