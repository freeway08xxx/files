<div class="form-block list-group">
	<div class="select-box controls list-group-item">

		<div class="header-area clearfix">
			<h4 class="pull-left">
				Excelシート設定
				<small class="left_10px right_10px">レポート種別：
					<span ng-bind="sheet.models.active_report_type"></span>
				</small>

				<small>デバイス別出力：
					<span ng-show="report.isSetDeviceType()">有り</span>
					<span ng-show="!report.isSetDeviceType()">なし</span>
				</small>
				</h4>

			<div class="option-area pull-right">
				<button type="button" class="btn btn-xs btn-default"
				 ng-click="sheet.openCustomFormatModal()">
					<span class="glyphicon glyphicon-new-window"></span>
					カスタムフォーマットを使用
				</button>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-6">
				<span class="glyphicon glyphicon-transfer icon-trans"></span>

				<div class="clearfix">
					<h5 class="sub-header pull-left">選択可能シート</h5>
					<button type="button" class="btn btn-xs btn-default pull-right" ng-click="sheet.addAll()">全選択</button>
				</div>

				<ul class="list-group available-list">
					<li class="option list-group-item clearfix"
						ng-repeat="item in sheet.models.available track by $index"
						ng-click="sheet.add($index)"
						ng-show="item.is_show">
						<span class="name">{{item.name}}</span>
						<button type="button" class="btn btn-xs btn-default pull-right"
							ng-click="sheet.preview($event)"
							popover-html-unsafe="<img src='/sem/new/assets/img/falcon/{{sheet.getReportType()}}/{{sheet.models.device}}/{{item.key}}.png' width='400px' class='text-right pull-right' />"
							popover-trigger="mouseenter"
							popover-placement="left"
							popover-append-to-body="true">

							<span class="glyphicon glyphicon-picture"></span>
							プレビュー
						</button>
					</li>
				</ul>
			</div>

			<div class="col-xs-6">
				<div class="clearfix">
					<h5 class="sub-header pull-left">出力するシート</h5>
					<button type="button" class="btn btn-xs btn-default pull-right" ng-click="sheet.clear()">全解除</button>
				</div>

				<ul class="list-group selected-list">
					<li class="option list-group-item clearfix"
						ng-repeat="item in sheet.models.selected track by $index"
						ng-click="sheet.delete($index)"
						ng-show="item.is_show">
						<span class="name">{{item.name}}</span>
						<button type="button" class="btn btn-xs btn-default pull-right"
							ng-click="sheet.preview($event)"
							popover-html-unsafe="<img src='/sem/new/assets/img/falcon/{{sheet.getReportType()}}/{{sheet.models.device}}/{{item.key}}.png' width='300px' class='text-right pull-right' />"
							popover-trigger="mouseenter"
							popover-placement="left"
							popover-append-to-body="true">

							<span class="glyphicon glyphicon-picture"></span>
							プレビュー
						</button>
					</li>
				</ul>
			</div>
		</div>

	</div>
</div>

