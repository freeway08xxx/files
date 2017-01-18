<div class="row information">
	<div class="panel panel-default clearfix">
		<div class="title-col">
			<span class="glyphicon glyphicon-info-sign"></span>
		</div>
		<ul class="list-group info-list">
			<li class="list-group-item" ng-repeat="list in baseCtrl.models.info">
				 <span class="list-group-item-date">{{list.created_at | moment:'YYYY.M.D'}}</span>{{list.text}}
			</li>
		</ul>
	</div>
</div>