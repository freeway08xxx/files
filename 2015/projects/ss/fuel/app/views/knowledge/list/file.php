	<div ng-show="fileTable.loading" id="loading" >
		
		<p><img style="width: 30px;" src="/sem/new/assets/img/ajax-loader.gif" > 読み込み中です</p>
	</div>
	<p ng-hide="fileTable.loading" ng-cloak>
		<span ng-show="fileTable.data.length > 0">{{fileTable.data.length}}件見つかりました。</span>
		<span ng-hide="fileTable.data.length > 0">見つかりませんでした。</span>
	</p>
	<table my-data-table="fileTable.overrideOptions"
        aa-data="fileTable.data"
        ao-columns="fileTable.columns"
        ao-column-defs="fileTable.columnDefs"
		fn-row-callback="fileTable.filesRowCallback"
		class="file-list-table table table-striped table-bordered table-hover "
        >
		<thead>
				<tr class="table-label">
				<th>ID</th>
				<th>タイトル</th>
				<th>詳細</th>
				<th>登録者</th>
				<th>DL回数</th>
				<th>ファイル変更日</th>
				<th>更新日</th>
				<th>登録日</th>
			</tr>
		</thead>
	</table>
