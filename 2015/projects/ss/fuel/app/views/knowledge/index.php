<div class="tab-pane active" id="search">
	<div class="container-fluid" ng-controller="SectionCtrl">
		<?= $section_navi; ?>
		<ng-view></ng-view>
	</div>
</div>

<!-- モダール用のテンプレート -->
<script type="text/ng-template" id="progress.html">
		<div class="modal-header">通信中...</div>
	<div class="modal-body">
		<!-- ストライプ模様のバーをグルグルさせる。伸びるアニメーションは無し。
		進捗は（サーバ側の進捗をクライアント側で知る術はないので）100%に固定 -->
	<progressbar class="progress-striped active" animate="false" value="100"></progressbar>
	</div>
</script>
