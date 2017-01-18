<div class="user-resource" >
	<div class="modal-header">
		<button type="button" class="close" ng-click="cancel()">×</button>
		<h4 class="modal-title"><span class="glyphicon glyphicon-calendar"></span>スケジュール一覧 [{{user.user_name}}] </h4>
	</div>
	<div class="modal-body">
		<div ui-calendar="calendarOptions" ng-model="eventSources">
	</div>
</div>
