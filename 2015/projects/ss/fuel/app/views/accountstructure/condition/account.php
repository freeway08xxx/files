<div class="row">
	<div class="col-sm-7">
		<div class="form-block list-group">

			<alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</alert>
			<h4 class="title">アカウント設定</h4>
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-12">
						<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="設定内容を取得するクライアントおよびアカウントを選択してください。">
							<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						<div>
							<ss-client-combobox ng-model="clientCombobox"></ss-client-combobox>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-block list-group">
			<h4 class="title">出力条件設定</h4>
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-5">
						<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="CSV,TSVで出力することをお勧めします。データ量にもよりますがEXCEL出力は処理時間を要します。">
							<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						<h5 class="control-label">出力フォーマット <span class="label label-warning">必須</span></h5>
						<div class="btn-group report-type">
							<label class="btn btn-sm btn-default" ng-model="param.format_type" btn-radio="'csv'" >CSV</label>
							<label class="btn btn-sm btn-default" ng-model="param.format_type" btn-radio="'tsv'" >TSV</label>
							<label class="btn btn-sm btn-default" ng-model="param.format_type" btn-radio="'excel'" >EXCEL</label>
						</div>
					</div>
					<div class="col-sm-7" ng-if="param.format_type == 'excel'">
						<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="EXCELの場合のみ,出力項目をシートごとにまとめて1ファイルで生成するか、出力項目単位(キャンペーンや広告グループ等)でファイルに分けて出力するか選択が可能です。当然、1ファイルにまとめると処理時間が増えます。データ量に応じて適宜指定してください。">
							<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						<h5 class="control-label">出力タイプ <span class="label label-warning">必須</span><span class="output-warning">EXCEL出力は処理時間を要します</span></h5>
						<div class="btn-group report-type">
							<label class="btn btn-sm btn-default" ng-model="param.output_type" btn-radio="'all'" >シート単位で1ファイルにまとめる</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_type" btn-radio="'divide'" >項目単位で出力する</label>
						</div>
					</div>
				</div>
			</div>
			<div class="controls list-group-item">
				<div class="row">
					<div class="col-sm-12">
						<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="出力項目を1つ以上選択してください。">
							<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						<h5 class="control-label">出力項目 <span class="label label-warning">必須</span></h5>
						<div class="btn-group report-type">
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.cpn" btn-checkbox>キャンペーン</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.adgroup" btn-checkbox>広告グループ</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.kw" btn-checkbox>KW</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.negativekw" btn-checkbox>ネガティブKW</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.td" btn-checkbox>広告文</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target" btn-checkbox>ターゲット</label>
						</div>

					</div>
					<div class="col-sm-12" ng-if="param.output_kind.target">
						<br>
						<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="出力するターゲットの項目を選択してください。">
							<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						<h5 class="control-label">出力項目(ターゲット) <span class="label label-warning">必須</span></h5>
						<div class="btn-group report-type">
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_area" btn-checkbox>地域</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_schedule" btn-checkbox>スケジュール</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_gender" btn-checkbox>性別</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_age" btn-checkbox>年齢</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_userlist" btn-checkbox>ユーザリスト</label>
							<label class="btn btn-sm btn-default" ng-model="param.output_kind.target_placement" btn-checkbox>プレースメント</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="conditions" value="{{param}}" />
	</div>
	<div class="col-sm-5">
		<div class="form-block list-group">
			<h4 class="title">媒体別取得可能項目</h4>
			<div class="controls list-group-item">
				<a ss-tooltip class="pull-right" data-toggle="tooltip" data-html="true" data-placement="left" title="本機能にて取得可能な媒体別の項目を確認することができます">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
				<h5 class="control-label">出力項目一覧</h5>
				<accordion class="small">
					<accordion-group is-open="status.cpn.open">
					    <accordion-heading>キャンペーン<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.cpn.open, 'glyphicon-chevron-right': !status.cpn.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $campaign ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.adgroup.open">
						<accordion-heading>広告グループ<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.adgroup.open, 'glyphicon-chevron-right': !status.adgroup.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $adgroup ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.kw.open">
					<accordion-heading>キーワード<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.kw.open, 'glyphicon-chevron-right': !status.kw.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $kw ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.ngkw.open">
					<accordion-heading>除外キーワード<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.ngkw.open, 'glyphicon-chevron-right': !status.ngkw.open}"></i></accordion-heading>
							<table class="table table-striped table-hover table-condensed small">
							<?= $negative_kw ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.ad.open">
					<accordion-heading>広告文<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.ad.open, 'glyphicon-chevron-right': !status.ad.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $ad ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.area.open">
					<accordion-heading>ターゲット(地域)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.area.open, 'glyphicon-chevron-right': !status.area.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_area ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.schedule.open">
					<accordion-heading>ターゲット(スケジュール)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.schedule.open, 'glyphicon-chevron-right': !status.schedule.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_schedule ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.gender.open">
					<accordion-heading>ターゲット(性別)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.gender.open, 'glyphicon-chevron-right': !status.gender.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_gender ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.age.open">
					<accordion-heading>ターゲット(年齢)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.age.open, 'glyphicon-chevron-right': !status.age.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_age ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.userlist.open">
					<accordion-heading>ターゲット(ユーザリスト)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.userlist.open, 'glyphicon-chevron-right': !status.userlist.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_userlist ?>
						</table>
					</accordion-group>
					<accordion-group is-open="status.placement.open">
					<accordion-heading>ターゲット(プレースメント)<i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': status.placement.open, 'glyphicon-chevron-right': !status.placement.open}"></i></accordion-heading>
						<table class="table table-striped table-hover table-condensed small">
							<?= $target_placement ?>
						</table>
					</accordion-group>
				</accordion>
			</div>
		</div>
	</div>
</div>
