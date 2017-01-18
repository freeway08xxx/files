<div ng-controller="ReportTemplateCtrl">
	<h5 class="title">テンプレート</h5>

	<div class="templatelist-wrap" id="templateList" ng-show="template_list">
		<table class="template-list table table-hover table-bordered">
			<tr class="" ng-repeat="item in template_list">
				<td ng-click="applyTemplate($index)" ng-class="{active: isSelected($index)}">
					<div class="title-row clearfix">
						<div class="name pull-left">{{item.template_name}}</div>
						<div class="pull-right">
							<button type="button" class="btn btn-xs btn-default" ng-click="info($event)">
								<span class="glyphicon glyphicon-info-sign" popover-html-unsafe="<span class='label label-default'>更新日</span><br>{{item.datetime}} <br><span class='label label-default'>更新者</span><br>{{item.user_name}}<br><span class='label label-default'>メモ</span><br>{{item.template_memo}}" popover-placement="left"></span>
							</button>
							<button type="button" class="btn btn-xs btn-danger" ng-click="delete($event, $index)"><span class="glyphicon glyphicon-trash"></span></button>
						</div>
					</div>
					<span class="info-panel label label-default"><span class="glyphicon glyphicon-search"></span> {{item.report_type.label}}</span>
					<span class="info-panel label label-default"><span class="glyphicon glyphicon-th"></span> {{item.summary_type.label}}</span>
					<span class="info-panel label label-default"><span class="glyphicon glyphicon-time"></span> {{item.report_term.label}}</span>
				</td>
			</tr>
		</table>
	</div>

	<p ng-show="!template_list">テンプレートが存在しません。</p>
</div>
