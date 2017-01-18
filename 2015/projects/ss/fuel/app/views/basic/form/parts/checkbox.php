
<!-- 通常のcheckbox -->
<h5 class="control-label">通常 <span class="label label-warning">必須</span></h5>
<label ng-repeat="sport in models.master.sports">
	<input type="checkbox" checklist-model="models.form.checkbox.normal" checklist-value="sport"> {{sport.name}}
</label>

<!-- ボタン化したcheckbox -->
<h5 class="control-label">ボタン </h5>
<div class="btn-group">
	<label 
		class="btn btn-default" 
		ng-repeat="sport in models.master.sports" 
		ng-model="models.form.checkbox.button[sport.id]"
		btn-checkbox 
		btn-checkbox-true="sport" 
		btn-checkbox-false=null
		>{{sport.name}}</label>
</div>
