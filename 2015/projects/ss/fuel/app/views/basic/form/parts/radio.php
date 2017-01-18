<h5 class="control-label">通常 <span class="label label-warning">必須</span></h5>

<div>
	<input value="PC" name="device" type="radio" id="button1" ng-model="models.form.radio.normal" /><label for="button1">PC</label>
	<input value="SP" name="device" type="radio" id="button2" ng-model="models.form.radio.normal" /><label for="button2">SP</label>
</div>

<h5 class="control-label">ボタン</h5>
<div class="btn-group">
	<label class="btn btn-default" name="device" ng-model="models.form.radio.button" btn-radio="'PC'">PC</label>
	<label class="btn btn-default" name="device" ng-model="models.form.radio.button" btn-radio="'SP'">SP</label>
</div>

