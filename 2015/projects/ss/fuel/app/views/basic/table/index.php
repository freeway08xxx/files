<div ng-init="init()">
	<h4> FORM 登録履歴 </h4>
	<button class="btn btn-info btn-xs" ng-click="SsTemplateTable.clickGetTableRow()">レコード追加</button>
	<button class="btn btn-info btn-xs" ng-click="SsTemplateTable.clickDeleteTableRow()">レコード削除</button>
	<button class="btn btn-info btn-xs" ng-click="SsTemplateTable.clickTableLoading()">ローディング中</button>
	<button class="btn btn-info btn-xs" ng-click="SsTemplateTable.clickTableHideAge()">FORM内容表示</button>
	<div ss-table="SsTemplateTable.ssTable" external-scopes="SsTemplateTable.ssTable.external"></div>
</div>

<ss-graph model="graph"></ss-graph>
