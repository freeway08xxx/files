<div class="tab-pane active mypage">
	<ng-view></ng-view>
</div>

<!-- modal ng-Template -->
<script type="text/ng-template" id="progress.html">
	<div class="modal-header">通信中...</div>
	<div class="modal-body">
		<progressbar class="progress-striped active" animate="false" value="100"></progressbar>
	</div>
</script>