<div class="col-sm-12">
	<!--  -->
	<div class="block-title clearfix">
		<h3 class="title">Modal</h3>
	</div>

	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="text sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<div>
								<button type="button" class="btn btn-primary pull-left" ng-click="openModal.apiSave()">確認</button>
								<button type="button" class="btn btn-success pull-left" ng-click="openModal.custom()">完了</button>
								<button type="button" class="btn btn-danger pull-left" ng-click="openModal.apiError()">エラー</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /block -->

	<div class="block-title clearfix">
		<h3 class="title">ボタン</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<!--
						type=[edit,delete] 無しの場合はボタン表示されず
						size=[lg,sm,xs] 無しの場合はデフォルトサイズで表示
					-->
					<ss-button type="save" size="sm" ng-click="btn.edit()">登録</ss-button>
					<ss-button type="edit" size="sm" ng-click="btn.edit()">編集</ss-button>
					<ss-button type="delete" size="xs" ng-click="btn.delete()"></ss-button>
					<ss-button type="download" size="xs" ng-click="">ダウンロード</ss-button>
				</div>
			</div>
		</div>
	</div>

	<!--  -->
	<div class="block-title clearfix">
		<h3 class="title">クライアント選択コンボ</h3>
	</div>

	<button type="button" class="btn" ng-click="clearCombobox()">１つめだけ選択クリア</button>

	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="text sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<div>
								<h5>1つめ</h5>
								<div ss-client-combobox="clientComboboxConfig" ng-model="clientCombobox"></div>
							</div>
							<div>
								<h5>2つめ</h5>
								<div ss-client-combobox="clientComboboxConfig2" ng-model="clientCombobox2"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /block -->

	<!--  -->
	<div class="block-title clearfix">
		<h3 class="title">トランジションエフェクト(要 ng-animate)</h3>
	</div>

	<div class="row">
		<div class="col-sm-6">

			<button type="button"
			 class="btn btn-default btn-sm bottom_10px"
			 ng-click="trans.is_show = !trans.is_show">
				フェードイン / アウト
			</button>

			<div ng-show="!trans.is_show" class="transition">
				<div class="well well-sm">
					コンテンツ1
				</div>
			</div>

			<div ng-show="trans.is_show" class="transition">
				<div class="alert alert-info">
					コンテンツ2
				</div>
			</div>

		</div>
	</div>
	<!-- /block -->

</div>
