<div class="mediacost clientmenu">
	<div>
		<h4 class="heading form-inline">レポート 媒体費設定</h4>
		<button type="button" class="btn btn-info btn-sm left_10px"
		 ng-show="!mediacost.models.is_regist_mode"
		 ng-click="mediacost.methods.initRegistMode()">
			新規登録
		</button>
	</div>


	<div ng-show="mediacost.models.is_regist_mode">
		<?= $view_form ?>
	</div>

	<div ng-show="!mediacost.models.is_regist_mode">
		<?= $view_table ?>
	</div>

	<div class="msg-area">
		<div class="alert alert-success" ng-show="mediacost.models.is_show_msg.update">
			{{mediacost.config.msg.update}}
			<button class="pull-right btn btn-link close-icon" ng-click="mediacost.methods.closeMsg('update')">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>

		<div class="alert alert-danger" ng-show="mediacost.models.is_show_msg.error">
			{{mediacost.config.msg.error}}
			<button class="pull-right btn btn-link close-icon" ng-click="mediacost.methods.closeMsg('error')">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>

		<div class="alert alert-danger" ng-show="mediacost.models.is_show_msg.invalid">
			{{mediacost.models.msg}}
			<button class="pull-right btn btn-link close-icon" ng-click="mediacost.methods.closeMsg('invalid')">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>
	</div>


	<?= View::forge('client/mediacost/attention') ?>
</div>