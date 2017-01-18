<div class="form-container clearfix" id="form" ng-init="init()">

		<div class="row clearfix">
			<ul class="nav nav-tabs">
				<li class="tab-form" ng-class="{active: settings.tab == 'tab1'}"><a href="" ng-click="settings.tab='tab1'">タブ１</a></li>
				<li class="tab-report" ng-class="{active: settings.tab == 'tab2'}"><a href="" ng-click="settings.tab='tab2'">タブ２</a></li>
				<li class="tab-report" ng-class="{active: settings.tab == 'tab3'}"><a href="" ng-click="settings.tab='tab3'">タブ３</a></li>
			</ul>
		</div>

		<div class="row tab-content">
			コンテンツ設定<pre>{{settings}}</pre>
			<div class="tab-pane form-container clearfix" ng-class="{active: settings.tab == 'tab1'}" id="form">
				<form name="ssTmplForm" id="ssTmplForm" method="GET" ng-submit="submit()" >
					<?= $form_main ?>
				</form>
			</div>
			<!-- 以下は FormCtrl -->
			<div class="tab-pane" ng-class="{active: settings.tab == 'tab2'}" id="list">
				<!--<form name="ssTmplForm" id="ssTmplForm" action="#" method="POST" >-->
					<?= $form_sub1 ?>
				<!--</form>-->
			</div>
			<div class="tab-pane" ng-class="{active: settings.tab == 'tab3'}" id="markdown">
					<?= $form_sub2 ?>
			</div>
		</div>

</div>
