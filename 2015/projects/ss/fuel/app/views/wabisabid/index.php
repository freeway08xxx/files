<div class="tab-pane active" ng-controller="WabiSabidBaseCtrl as baseCtrl">

	<div class="form-container row">
		<div class="col-sm-12" ng-class="{active_account: display_type == 'form'}">

			<!-- メッセージ -->
			<p class="alert alert-danger" ng-show="message" ng-cloak>{{message}}</p>

			<!-- クライアント選択 -->
			<div class="form-block list-group client-combobox-wrap inline-client">
				<div class="controls list-group-item">
					<div ss-client-combobox="clientComboboxConfig" ng-model="clientCombobox"></div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<ng-view></ng-view>
		</div>
	</div>
	
</div>
