<div class="add-mediacost form-container">

	<form id="add_mediacost" name="add_mediacost" method="POST">

		<div class="list-group form-block">

			<div class="list-group-item controls target-type">
				<span class="control-label">設定単位</span>
				<select class="form-control input-sm" ng-model="mediacost.models.add.target_type">
					<option value="1">媒体</option>
					<option value="2">アカウント</option>
				</select>
			</div>

			<div class="list-group-item controls target-media" ng-show="mediacost.models.add.target_type === '1'">
				<span class="control-label">媒体</span>
				<select class="form-control input-sm" ng-model="mediacost.models.add.media_id">
					<option value="">--</option>
					<? foreach (FalconConst::$falcon_target_media_list as $media_id => $media_name){?>
						<option value="<?=$media_id?>"><?=$media_name?></option>
					<? } ?>
				</select>
			</div>

			<div class="list-group-item controls" ng-show="mediacost.models.add.target_type === '2'"
			ng-controller="clMediaCostAccountListCtrl as form_account">
			<!-- Account List -->
				<div class="account-list" ss-client-combobox="form_account.clientComboboxConfig"
				 ng-model="mediacost.models.client"></div>
			</div>

			<div class="list-group-item controls">
				<span class="control-label">媒体費</span>
				<div class="input-group media-cost-input">
					<input type="number" class="form-control input-sm"
					 min="0" max="100" required
					 ng-model="mediacost.models.add.cost">
					<span class="input-group-addon">%</span>
				</div>
			</div>
		</div>

		<div class="btn-area">
			<button type="button" class="btn btn-primary btn-sm" ng-click="mediacost.methods.save(add_mediacost)">
				登録
			</button>　
			<button type="button" class="btn btn-default btn-sm" ng-click="mediacost.methods.cancel()">
				キャンセル
			</button>
		</div>

	</form>
</div>
