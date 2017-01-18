<div class="tskt-top" ng-controller="TsktTopCtrl as tsktTop">
	<tabset class="tskt-tabset" >
		<tab heading="タスク">
			<div class="tskt-top-content">
				<?= $view_task; ?>
			</div>
		</tab>
		<tab heading="プロセス">
			<div class="tskt-top-content">
				<?= $view_process; ?>
			</div>
		</tab>
	</tabset>
	<?= $view_detail; ?>
</div>


