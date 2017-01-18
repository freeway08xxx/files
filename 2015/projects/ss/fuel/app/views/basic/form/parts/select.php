
<!-- 通常SelectBox -->
<h5 class="control-label">通常 <span class="label label-warning">必須</span></h5>
<!--必須の警告文 -->
<span style="color:red;">必須項目です</span>
<select name="select_normal" class="form-control" 
	ng-model="models.form.select.normal"
	ng-options="value.id as value.name for value in models.master.subject"
><option value="" disabled>-- 未選択 --</option></select>

<h5 class="control-label">検索可能選択(※推奨)</h5>
<div ui-select ng-disabled="disabled" ng-model="models.form.select.search" theme="select2" ng-disabled="disabled" style="width:100%;">
	<ui-select-match placeholder="科目を選択">{{$select.selected.name}}</ui-select-match>
	<ui-select-choices repeat="value.id as value in models.master.subject | filter:$select.search">
		<div ng-bind-html="value.name | highlight: $select.search"></div>
	</ui-select-choices>
</div>

<!-- 複数選択可能のSelectBox -->
<h5 class="control-label">マルチ選択</h5>
<select name="select_multi" class="form-control" 
	ng-model="models.form.select.multi" 
	ng-options="value.id as value.name for value in models.master.subject"
	multiple 
><option value="" disabled>-- 未選択 --</option>
</select>


<!-- 複数選択可能の検索select -->
<h5 class="control-label" ng-init="models.master.subject=[]" >検索可能マルチ選択</h5>
<div ui-select multiple ng-model="models.form.select.search_multi" theme="select2" ng-disabled="disabled" style="width: 100%;">
	<ui-select-match placeholder="科目を選択">{{$item.name}}</ui-select-match>
	<ui-select-choices repeat="value.id as value in models.master.subject | filter:$select.search">
	  <div ng-bind-html="value.name | highlight: $select.search"></div>
	</ui-select-choices>
</div>
