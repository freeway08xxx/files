<!-- Markdown -->
<div class="col-sm-12 markdown_area" markdown>
	<div class="block-title clearfix">
		<h3 class="title">Markdown</h3>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-block list-group">
				<div class="panel">
					<div class="controls list-group-item">
						<div class="row bottom_10px">
							<div class="col-md-6 js-file-input file-input">
								<p>本文</p>
								<textarea class="form-control" rows="30" ng-model="markdown.data.before" ng-change="markdown.actions.onChange()"></textarea>
								<div class="dragover_area" ng-show="markdown.actions.isDragover"><p><i class="glyphicon glyphicon-picture"></i> ここに画像をドロップ</p></div>
							</div>
							<div class="col-md-6 preview_area">
								<p class="pull-left">プレビュー</p>
								<button type="button" class="btn btn-xs btn-default capture_switch" ng-click="markdown.actions.isShowCapture=!markdown.actions.isShowCapture;markdown.actions.onChange();">
									<span ng-show="markdown.actions.isShowCapture">キャプチャ表示</span>
									<span ng-hide="markdown.actions.isShowCapture">キャプチャ非表示</span>
								</button>

								<div class="form-control" ng-bind-html="markdown.data.after"></div>
							</div>
						</div>
						<ss-button type="save" size="sm" ng-click="markdown.actions.save()">登録</ss-button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<pre>{{markdown.data.before}}</pre>
	<pre>{{markdown.data.after}}</pre>
</div>

