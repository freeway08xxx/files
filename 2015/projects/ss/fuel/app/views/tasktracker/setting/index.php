<div class="tskt-setting" ng-controller="TsktSettingCtrl as tsktSetting">
	<div class="clearfix">
		<ul class="nav nav-tabs">
			<li ng-repeat="category in tsktSetting.models.master.category" ng-class="{active: tsktSetting.models.current.category.id == category.id}">
				<a ui-sref="setting.task({categoryId:category.id})">{{category.name}}</a>
			</li>
		</ul>
	</div>
	<div class="tab-content">
		<div class="active tab-pane form-container clearfix" ng-show="tsktSetting.models.master.category">
			<div class="task-client">
				<div class="row clearfix">
					<div class="col-lg-6">
						<h4>
							<span class="transition" ng-if="tsktSetting.models.clientCombobox.client">
								{{tsktSetting.models.clientCombobox.client.display_name}}
								<button class="btn btn-primary btn-xs" ng-click="tsktSetting.services.openClientSelect()">変更</button>
								<button class="btn btn-danger btn-xs" ng-click="tsktSetting.services.releaseClientSelect()">解除</button>
							</span>
							<span class="transition" ng-if="!tsktSetting.models.clientCombobox.client">
								クライアント指定なし
								<button class="btn btn-info btn-xs" ng-click="tsktSetting.services.openClientSelect()">指定</button>
							</span>
						</h4>
						<div collapse="!tsktSetting.models.isOpenClientSelect">
							<div style="margin:5px;" ss-client-combobox="tsktSetting.models.clientComboboxConfig" ng-model="tsktSetting.models.clientCombobox"></div> 
						</div>
						




						<!--
						<h4> {{tsktSetting.models.clientCombobox.client.display_name}} </h4>
						<button class="btn btn-default btn-xs" ng-click="tsktSetting.models.selectClient = !tsktSetting.models.selectClient">
							<span ng-if="!tsktSetting.models.selectClient">クライアントを指定する</span>
							<span ng-if="tsktSetting.models.selectClient">クライアント指定を解除する</span>
						</button>
						<div collapse="!tsktSetting.models.selectClient">
							<div style="margin:5px;" ss-client-combobox="tsktSetting.models.clientComboboxConfig" ng-model="tsktSetting.models.clientCombobox"></div> 
						</div>
						-->

					</div>
				</div>
			</div>

			<div ui-view class="transition clearfix"></div>
		</div>
	</div>
</div>
