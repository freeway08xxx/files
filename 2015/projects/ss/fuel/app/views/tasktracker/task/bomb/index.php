<div class="modal-body">
	<button type="button" class="close" ng-click="close()">×</button>
	<div ng-if="bombs">
		<h5>BOMB一覧</h5>
		<table class="table">
			<tr>
				<th>登録日</th>
				<th>登録者</th>
				<th>詳細</th>
			</tr>
			<tr ng-repeat="bomb in bombs">
				<td>{{bomb.created_at}}</td>
				<td>{{bomb.user_name}}</td>
				<td ng-bind-html="bomb.bomb_description | noHTML | newlines"></td>
			</tr>
		</table>
	</div>
	<div>
		<h5>BOMB登録</h5>
		<textarea class="form-control" rows="5" ng-model="bomb_description"></textarea>
		<button class="btn btn-warning" ng-click="save()">
			<i class="glyphicon glyphicon-exclamation-sign"></i> BOMB登録 
		</button>
	</div>
</div>
