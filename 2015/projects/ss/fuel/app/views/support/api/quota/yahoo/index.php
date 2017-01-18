<div class="block-title clearfix">
	<h4 class="title">Yahoo!使用量</h4>
</div>

<div>
	<div ng-show="quotas.loading" id="loading" >
		<p><img style="width: 30px;" src="/sem/new/assets/img/ajax-loader.gif" > 読み込み中です</p>
	</div>
<div class="quotas-table" ui-grid="quotas" external-scopes="$scope"></div>
