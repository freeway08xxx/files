<div class="clearfix">
	<ul class="nav nav-tabs">
		<li class="tab-form" ng-class="{active: settings.tab == 'status'}"><a href="" ng-click="settings.tab='status'">ステータス変更</a></li>
		<li class="tab-report" ng-class="{active: settings.tab == 'cpc'}"><a href="" ng-click="settings.tab='cpc'">CPC変更</a></li>
	</ul>
</div>

<div class="tab-content">
	<div class="block-title clearfix">
		<!--<h4 class="title">対象情報の更新</h4>-->
	</div>

	<!-- アラート -->
	<div>
	  <alert ng-repeat="alert in alerts" type="{{alert.type}}" close="alerts.splice($index, 1);"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> {{alert.msg}}</alert>
	</div>

	<!-- client & account information -->
	<div class="row target-account">
		<div class="col-sm-5 client">
			<div class="panel panel-default">
				<div class="panel-heading">
					クライアント
				</div>
				<div class="panel-body">
					{{eagle.client_name}}
				</div>
			</div>
		</div>
	
		<div class="col-sm-7 account">
			<div class="panel panel-default">
				<div class="panel-heading">
					アカウント
				</div>
				<ul class="list-group account">
					<li class="list-group-item" ng-repeat="eagle_account in eagle_accounts">
						<span class="label id">{{eagle_account.account_id}}</span>
						<img ng-if="!!eagle_account.icon" class="icon-media" ng-src={{eagle_account.icon}}><span ng-if="!eagle_account.icon">{{eagle_account.media_name}}</span>
							{{eagle_account.account_name}}
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /client & account information -->
	<div class="tab-pane form-container clearfix" ng-class="{active: settings.tab == 'status'}" id="form">
		<?= $status ?>
	</div>
	<!-- 以下は FormCtrl -->
	<div class="tab-pane" ng-class="{active: settings.tab == 'cpc'}" id="list">
		<?= $cpc ?>
	</div>
</div>




