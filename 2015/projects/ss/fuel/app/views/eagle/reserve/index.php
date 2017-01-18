<form name="eagleReserveFrom" id="eagleReserveFrom" method="POST" ng-submit="submit()" >
<div class="block-title clearfix">
	<h4 class="title">対象アカウントの掲載内容を更新</h4>
</div>

<div class="row form-container">
	<div class="col-lg-6">

		<div class="list-group">
			<div class="form-container list-group-item clearfix" id="">
				<ss-client-combobox ng-model="clientCombobox"></ss-client-combobox>
			</div>
		</div>

	</div>
	<div class="col-lg-6"></div>
</div><!-- /row -->

<!-- -->
<div class="btn-area">
	<button type="submit" class="btn btn-primary" ng-disabled="!(clientCombobox.accounts.length > 0) || isSubmit">
		<span ng-show="!isSubmit" class="glyphicon glyphicon-cloud-download"></span>
		<img ng-show="isSubmit" ng-src="/sem/new/assets/img/ajax-loader.gif" >
		最新の掲載内容を取得して続行
	</button>
	<label for="eagle_is_sync">同期スキップ:</label> <input id="eagle_is_sync"type="checkbox" ng-model="isSyncSkip">
	<span class="help-block">
		<span class="glyphicon glyphicon-envelope"></span>
		選択したアカウントの同期完了後、メールでお知らせします。
	</span>
</div>
<!-- -->
</form>
