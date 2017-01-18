<div ng-controller="EagleUpdateStatusCtrl">
<hr />



<!-- target option block -->
<div class="filter-target">
	<p class="control-label"><span class="step">1</span> 変更対象を選択</p>
	<div class="btn-group">
		<label class="btn" name="{{component.id}}" ng-repeat="component in components" ng-class="isFilterTypeActive(component.id)" ng-model="settings.status.component" btn-radio="component.id">{{component.name}}</label>
	</div>
</div>
<!-- /target option block -->

<hr />

<!-- filter area -->
<accordion class="status-filter" close-others="model">
	<p class="control-label"><span class="step">2</span> ダウンロード対象を絞り込む</p>
	<div class="row">

		<!-- filter block -->
		<div class="col-md-6 form-group filter-campaign">
			<accordion-group is-open="isComponentOpen('campaign')" is-disabled="true">
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
			<accordion-group is-open="isComponentOpen('adgroup')" is-disabled="true">
				<accordion-heading>
					<div class="group-title">
						<span class="glyphicon glyphicon-search"></span> 絞込: <strong>広告グループ</strong>
					</div>
				</accordion-heading>
				<span class="option">
					<label class="option-item"><input type="checkbox" ng-model="filter.adgroup.isIdOnly"/> IDのみで絞込む</label>
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
	<div class="row">

		<!-- filter block -->
		<div class="col-md-6 form-group filter-kw">
			<accordion-group is-open="isComponentOpen('keyword')" is-disabled="true">
				<accordion-heading>
					<div class="group-title">
						<span class="glyphicon glyphicon-search"></span> 絞込: <strong>キーワード</strong>
					</div>
				</accordion-heading>
				<span class="option">
					<label class="option-item"><input type="checkbox" ng-model="filter.keyword.isIdOnly" /> IDのみで絞込む</label>
				</span>

				<div class="row">
					<div class="col-sm-6">
						<div class="input-label">対象に含めるキーワード</div>
						<div class="btn-group filter-option">
							<label class="btn btn-default btn-xs" ng-model="filter.keyword.search.type" btn-radio="'1'">AND</label>
							<label class="btn btn-default btn-xs" ng-model="filter.keyword.search.type" btn-radio="'2'">OR</label>
						</div>
						<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.keyword.search.list"></textarea>
					</div>
					<div class="col-sm-6 exclude">
						<div class="input-label">除外するキーワード</div>
						<div class="btn-group filter-option">
							<label class="btn btn-default btn-xs" ng-model="filter.keyword.except.type" btn-radio="'1'">AND</label>
							<label class="btn btn-default btn-xs" ng-model="filter.keyword.except.type" btn-radio="'2'">OR</label>
						</div>
						<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力"  ng-model="filter.keyword.except.list"></textarea>
					</div>
				</div>
			</accordion-group>
		</div>
		<!-- /filter block -->

		<!-- filter block -->
		<div class="col-md-6 form-group filter-ad">
			<accordion-group is-open="isComponentOpen('ad')" is-disabled="true">
				<accordion-heading>
					<div class="group-title">
						<span class="glyphicon glyphicon-search"></span> 絞込: <strong>広告</strong>
					</div>
				</accordion-heading>
				<span class="option">
					<label class="option-item">検索対象:
						<div class="btn-group">
							<label class="btn btn-default btn-xs" ng-model="filter.ad.pattern.1" btn-checkbox>広告ID</label>
							<label class="btn btn-default btn-xs" ng-model="filter.ad.pattern.2" btn-checkbox>広告名</label>
							<label class="btn btn-default btn-xs" ng-model="filter.ad.pattern.3" btn-checkbox>タイトル</label>
							<label class="btn btn-default btn-xs" ng-model="filter.ad.pattern.4" btn-checkbox>説明文1,2</label>
						</div>
					</label>
				</span>

				<div class="row">
					<div class="col-sm-6">
						<div class="input-label">対象に含める広告</div>
						<div class="btn-group filter-option">
							<label class="btn btn-default btn-xs" ng-model="filter.ad.search.type" btn-radio="'1'">AND</label>
							<label class="btn btn-default btn-xs" ng-model="filter.ad.search.type" btn-radio="'2'">OR</label>
						</div>
						<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.ad.search.list"></textarea>
					</div>
					<div class="col-sm-6 exclude">
						<div class="input-label">除外する広告</div>
						<div class="btn-group filter-option">
							<label class="btn btn-default btn-xs" ng-model="filter.ad.except.type" btn-radio="'1'">AND</label>
							<label class="btn btn-default btn-xs" ng-model="filter.ad.except.type" btn-radio="'2'">OR</label>
						</div>
						<textarea class="form-control" rows="4" placeholder="改行区切りで複数入力" ng-model="filter.ad.except.list"></textarea>
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
		<button type="submit" class="btn btn-info" ng-click="dlFilterFile()">
			<span class="glyphicon glyphicon-download-alt"></span>
			上記内容で絞り込んだリストをダウンロード
			<span class="label label-default">CSV</span>
		</button>
	</div>
</div>


<hr/>


<!-- target option block -->
<div class="filter-target">
	<p class="control-label"><span class="step">3</span> ステータスを変更</p>
	<div class="row">
		<div class="col-sm-6">

			<h5 class="control-label">一括設定</h5>
			<div class="btn-group">
				<label class="btn btn-default" name="device" ng-model="settings.status.updateActive" btn-radio="true">ON</label>
				<label class="btn btn-default" name="device" ng-model="settings.status.updateActive" btn-radio="false">OFF</label>
			</div>
			<textarea class="form-control" name="update_data" rows="4" placeholder="更新コード (改行により複数指定可能)" ng-model="updateData"></textarea>
			<div class="clearfix">
				<div class="pull-left">
					<button type="button" class="btn btn-info" ng-click="dlConfirmFile()">
						<span class="glyphicon glyphicon-download-alt"></span>
						上記変更内容を確認する	
						<span class="label label-default">CSV</span>
					</button>
					<button type="submit" class="btn btn-primary" ng-click="updateStatus()">
						<span ng-show="!isSubmit" class="glyphicon glyphicon-cloud-upload"></span>
						<img ng-show="isSubmit" ng-src="/sem/new/assets/img/ajax-loader.gif" >
						上記内容で変更を適用する
					</button>
				</div>
			</div>
			<span class="help-block">
				<span class="glyphicon glyphicon-envelope"></span>
				更新完了後、メールにてお知らせします。
			</span>
		</div>
	</div>
</div>
<!-- /target option block -->



</div>
