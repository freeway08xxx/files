<div class="tab-pane active falcon" ng-controller="FalconCommonCtrl as common">

	<div class="form-container row {{mainTab}}">
		<div class="col-xs-4 col-sm-3">
		</div>

		<div class="col-xs-8 col-sm-9 over-contents">
			<div class="form-block list-group client-combobox-wrap inline-client
			 {{common.getReportTab()}} {{common.models.summary_type}}">
				<div class="controls list-group-item">
					<div ss-client-combobox="common.clientComboboxConfig"
					 ng-model="common.models.client"></div>
				</div>
			</div>
		</div>
	</div>

	<div ng-view class="transition"></div>
</div>