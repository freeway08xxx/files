<div class="form-container clearfix list">

	<div class="block-title clearfix position">
		<h4 class="title">作成済みレポート一覧</h4>
		<small>（最新10件のみ）</small>
	</div>

	<div ng-show="!list.models.is_set_client">
		クライアントを選択してください。
	</div>

	<div ng-show="list.models.is_set_client">

		<div ng-show="!list.models.data">
			レポート作成履歴はありません
		</div>

		<div class="panel panel-default table-wrap" ng-show="list.models.data">
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th class="text-center">No.</th>
						<th>レポート名</th>
						<th class="status text-center">ステータス</th>
						<th>使用テンプレート</th>
						<th class="text-center">出力時間（分）</th>
						<th class="text-center">集計期間</th>
						<th class="text-center">作成者</th>
						<th class="text-center">開始日時</th>
						<th class="text-center">完了日時</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="item in list.models.data" data-index="$index">
						<td class="text-center">{{$index + 1}}</td>
						<td>
							<a ng-show="item.status_id === '1'"
							ng-click="list.download(item.id)" ng-bind="item.report_name"></a>
							<span ng-show="item.status_id !== '1'" ng-bind="item.report_name"></span>
						</td>
						<td class="status text-center">
							<span class="{{common.config.report.report_export_status[item.status_id].label}}">
								{{common.config.report.report_export_status[item.status_id].name}}
							</span>
						</td>
						<td class="">{{item.template_name}}</td>
						<td class="datetime text-center">{{item.export_time}}</td>
						<td class="text-center">{{common.config.report.report_term.all[item.report_term]}}</td>
						<td class="text-center">{{item.user_name}}</td>
						<td class="datetime text-right">{{item.created_at}}</td>
						<td class="datetime text-right">{{item.updated_at}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- for Download Report -->
<form name="dlform" id="dl"></form>