<form name="discountform" id="discount_form" action="" method="post" ng-controller="RegistFormCtrl">
	<!-- 値引設定 -->
	<div class="block-title clearfix">
		<div class="pull-right">
			<button type="button" class="btn btn-sm btn-info" ng-click="download()">設定シートをダウンロード</button>
		</div>

		<h4 class="title">値引設定を登録</h4>
	</div>

	<div class="form-block list-group">
		<div ss-termdate="termdate_config" ng-model="params.termdate"></div>

		<div class="controls list-group-item">
			<a class="pull-right" rel="tooltip" data-toggle="tooltip" data-html="true" data-placement="left" title="<?= EXPLAIN_DISCOUNT_SHEET; ?>">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
			<h5 class="control-label">設定シート <span class="label label-warning">必須</span></h5>

			<div class="input-upload">
				<input file-upload type="file" name="upload_file" id="upload_file" />
				<input type="hidden" name="file_dataurl" value="" />
			</div>
		</div>
	</div>

	<div class="validate-msg alert alert-warning">
		<p class="" ng-show="discountform.from_date.$error.required">期間を設定してください。</p>
		<p class="">{{msg}}</p>
	</div>

	<div class="btn-area clearfix">
		<button type="button" class="btn btn-sm btn-primary" ng-click="submit()" ng-disabled="discountform.$invalid">設定シートをアップロード</button>
	</div>
</form>