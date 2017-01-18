<div class="block-title clearfix">
	<h4 class="title">掲載更新履歴</h4>
</div>

<div>
	<div ng-show="eagleHistory.loading" id="loading" >
		<p><img style="width: 30px;" src="/sem/new/assets/img/ajax-loader.gif" > 読み込み中です</p>
	</div>
<!--
<pagination boundary-links="true" total-items="eagleHistory.data.length" max-size="5" items-per-page="eagleHistory.rowsPerPage" ng-model="eagleHistory.page" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
<div class="eagle-history-table" ui-grid="eagleHistory" external-scopes="$scope" ui-grid-pagination></div>
-->
<div class="eagle-history-table" ui-grid="eagleHistory" external-scopes="$scope"></div>
<!--
	<table ss-data-table="eagleHistory.overrideOptions"
		aa-data="eagleHistory.data"
		ao-columns="eagleHistory.columns"
		ao-column-defs="eagleHistory.columnDefs"
		fn-row-callback="eagleHistory.filesRowCallback"
		class="file-list-table table table-striped table-bordered table-hover "
		>
		<thead>
			<tr class="table-label">
				<th>ID</th>
				<th>状況</th>
				<th>クライアント名</th>
				<th>更新対象</th>
				<th>補足</th>
				<th>作成者</th>
				<th>登録日時</th>
				<th>更新日時</th>
				<th>更新・DL</th>
			</tr>
		</thead>
	</table>
-->
</div>
