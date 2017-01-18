<div ng-controller="EagleUpdateCpcCtrl">
<hr />



<accordion close-others="true">
	<accordion-group class="cpc bulk" is-disabled="true" is-open="settings.cpc.saveType == 1" ng-click="settings.cpc.saveType = 1">
		<accordion-heading>
			バルクリストから変更する <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.open, 'glyphicon-chevron-right': !status.open}"></i>
		</accordion-heading>

		<!-- filter area -->
		<accordion class="status-filter" close-others="model">
			<p class="control-label"><span class="step">1</span> ダウンロード対象を絞り込む</p>
			<div class="row">
		
				<!-- filter block -->
				<div class="col-md-6 form-group filter-campaign">
					<accordion-group is-disabled="true" is-open="true">
						<accordion-heading>
							<div class="group-title">
								<span class="glyphicon glyphicon-search"></span> 絞込: <strong>キャンペーン</strong>
							</div>
						</accordion-heading>
						<span class="option">
							<label class="option-item"><input type="checkbox" ng-model="filter.campaign.isIdOnly"/> IDのみで絞込む</label>
						</span>
		
						<div class="row">
							<div class="col-sm-6">
								<div class="input-label">対象に含めるキャンペーン</div>
								<div class="btn-group filter-option">
									<label class="btn btn-default btn-xs" ng-model="filter.campaign.search.type" btn-radio="'1'">AND</label>
									<label class="btn btn-default btn-xs" ng-model="filter.campaign.search.type" btn-radio="'2'">OR</label>
								</div>
								<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.campaign.search.list"></textarea>
							</div>
							<div class="col-sm-6 exclude">
								<div class="input-label">除外するキャンペーン</div>
								<div class="btn-group filter-option">
									<label class="btn btn-default btn-xs" ng-model="filter.campaign.except.type" btn-radio="'1'">AND</label>
									<label class="btn btn-default btn-xs" ng-model="filter.campaign.except.type" btn-radio="'2'">OR</label>
								</div>
								<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.campaign.except.list"></textarea>
							</div>
						</div>
					</accordion-group>
				</div>
				<!-- /filter block -->
		
				<!-- filter block -->
				<div class="col-md-6 form-group filter-adg">
					<accordion-group is-disabled="true" is-open="true">
						<accordion-heading>
							<div class="group-title">
								<span class="glyphicon glyphicon-search"></span> 絞込: <strong>広告グループ</strong>
							</div>
						</accordion-heading>
						<span class="option">
							<label class="option-item"><input type="checkbox" /> IDのみで絞込む</label>
						</span>
		
						<div class="row">
							<div class="col-sm-6">
								<div class="input-label">対象に含める広告グループ</div>
								<div class="btn-group filter-option">
									<label class="btn btn-default btn-xs" ng-model="filter.adgroup.search.type" btn-radio="'1'">AND</label>
									<label class="btn btn-default btn-xs" ng-model="filter.adgroup.search.type" btn-radio="'2'">OR</label>
								</div>
								<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.adgroup.search.list"></textarea>
							</div>
							<div class="col-sm-6 exclude">
								<div class="input-label">除外する広告グループ</div>
								<div class="btn-group filter-option">
									<label class="btn btn-default btn-xs" ng-model="filter.adgroup.except.type" btn-radio="'1'">AND</label>
									<label class="btn btn-default btn-xs" ng-model="filter.adgroup.except.type" btn-radio="'2'">OR</label>
								</div>
								<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.adgroup.except.list"></textarea>
							</div>
						</div>
					</accordion-group>
				</div>
				<!-- /filter block -->
		
			</div>
		</accordion>
		<!-- /filter area -->
		
		<div class="clearfix">
			<div class="pull-left">
				<button type="button" class="btn btn-info" ng-click="dlFilterFile()">
					<span class="glyphicon glyphicon-download-alt"></span>
					上記内容で絞り込んだリストをダウンロード
					<span class="label label-default">CSV</span>
				</button>
			</div>
		</div>

		<hr/>

		<!-- option -->
		<div class="row option-area">
			<p class="control-label"><span class="step">2</span> [CPC/MBA/デフォルトCPC]を変更</p>
			<div class="col-xs-12">
				<div class="btn-group">
					<label class="btn btn-xs" name="device" ng-class="activeButton(settings.cpc.cpcMba == 'CPC')" ng-model="settings.cpc.cpcMba" btn-radio="'CPC'">CPC指定</label>
					<label class="btn btn-xs" name="device" ng-class="activeButton(settings.cpc.cpcMba == 'MBA')" ng-model="settings.cpc.cpcMba" btn-radio="'MBA'">MBA指定</label>
					<label class="btn btn-xs" name="device" ng-class="activeButton(settings.cpc.cpcMba == 'DefaultCPC')" ng-model="settings.cpc.cpcMba" btn-radio="'DefaultCPC'">デフォルトCPC</label>
				</div>
				<div class="btn-group" ng-if="(settings.cpc.cpcMba == 'CPC')">
					<label class="btn btn-xs btn-default" ng-model="settings.cpc.device.pc" btn-checkbox >PC</label>
					<label class="btn btn-xs btn-default" ng-model="settings.cpc.device.sp" btn-checkbox >SP</label>
				</div>
			</div>
		</div>
		<!-- -->

		<div class="form-group">
			<textarea class="form-control" rows="4" placeholder="{{getUpdatePlaceholder()}}" ng-model="cpc.updateData"></textarea>
			<span class="help-block">上記項目を1行ずつ入力してください。各フィールドはタブで区切ってください。</span>
		</div>

		<div class="btn-area">
			<button type="button" class="btn btn-info" ng-click="dlConfirmFile()">
				<span class="glyphicon glyphicon-download-alt"></span>
				上記変更内容を確認する	
				<span class="label label-default">CSV</span>
			</button>
			<button type="button" class="btn btn-primary" ng-click="updateCpc()">
				<span ng-show="!isSubmit" class="glyphicon glyphicon-cloud-upload"></span>
				<img ng-show="isSubmit" ng-src="/sem/new/assets/img/ajax-loader.gif" >
				上記内容で変更を適用する
			</button>
			<span class="help-block">
				<span class="glyphicon glyphicon-envelope"></span>
				更新完了後、メールにてお知らせします。
			</span>
		</div>

	</accordion-group>
	<accordion-group class="cpc even" is-disabled="true" is-open="settings.cpc.saveType == 2" ng-click="settings.cpc.saveType = 2">
		<accordion-heading>
			一律で一括変更する <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.open, 'glyphicon-chevron-right': !status.open}"></i>
		</accordion-heading>

		<!-- option -->
		<div class="row option-area even">
			<div class="col-sm-12">
				<div class="btn-group">
					<label class="btn btn-xs" name="device" ng-class="activeButton(settings.cpc.cpcMba == 'CPC')" ng-model="settings.cpc.cpcMba" btn-radio="'CPC'" >CPC指定</label>
					<label class="btn btn-xs" name="device" ng-class="activeButton(settings.cpc.cpcMba == 'DefaultCPC')" ng-model="settings.cpc.cpcMba" btn-radio="'DefaultCPC'" >デフォルトCPC</label>
				</div>
				<div class="btn-group" ng-if="(settings.cpc.cpcMba == 'CPC')">
					<label class="btn btn-xs btn-default" ng-model="settings.cpc.device.pc" btn-checkbox >PC</label>
					<label class="btn btn-xs btn-default" ng-model="settings.cpc.device.sp" btn-checkbox >SP</label>
				</div>
			</div>
		</div>
		<!-- -->

		<div class="form-group">
			<label class="control-label">一律で</label>

			<input type="text" class="form-control change-value" ng-model="cpc.bulkValue"/>

			<div class="btn-group even-option" ng-init="settings.cpc.unit = 'amount'">
				<label class="btn btn-default btn-xs" name="unit" ng-class="activeButton(settings.cpc.unit == 'amount')" ng-model="settings.cpc.unit" btn-radio="'amount'">円</label>
				<label class="btn btn-default btn-xs" name="unit" ng-class="activeButton(settings.cpc.unit == 'percent')" ng-model="settings.cpc.unit" btn-radio="'percent'">%</label>
			</div>

			<div class="btn-group even-option" ng-init="settings.cpc.ud = 'up'">
				<label class="btn btn-default btn-xs" name="ud" ng-class="activeButton(settings.cpc.ud == 'up')" ng-model="settings.cpc.ud" btn-radio="'up'">上げる</label>
				<label class="btn btn-default btn-xs" name="ud" ng-class="activeButton(settings.cpc.ud == 'down')" ng-model="settings.cpc.ud" btn-radio="'down'">下げる</label>
			</div>
		</div>

		<div class="btn-area">
			<button type="button" class="btn btn-info" ng-click="dlConfirmFile()">
				<span class="glyphicon glyphicon-download-alt"></span>
				上記変更内容を確認する	
				<span class="label label-default">CSV</span>
			</button>
			<button type="button" class="btn btn-primary" ng-click="updateCpc()">
				<span ng-show="!isSubmit" class="glyphicon glyphicon-cloud-upload"></span>
				<img ng-show="isSubmit" ng-src="/sem/new/assets/img/ajax-loader.gif" >
				上記内容で変更を適用する
			</button>
			<span class="help-block">
				<span class="glyphicon glyphicon-envelope"></span>
				更新完了後、メールにてお知らせします。
			</span>
		</div>
	</accordion-group>
</accordion>


</div>
