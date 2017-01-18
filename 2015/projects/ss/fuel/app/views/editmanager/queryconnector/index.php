<div id="contentsApp" class="queryconnector content legacy" ng-app="qc">
	<!-- フォームTop -->
	<form ng-controller="FormController" method="post" name="form01" enctype="multipart/form-data">

		<h5>実行モード</h5>
		<div class="list-group">
			<div class="list-group-item">
				<div class="radio" ng-repeat="exec_mode in mode_list">
					<label>
						<input type="radio" ng-model=checked ng-value=exec_mode.value id=exec_mode.value name="exec_type">{{exec_mode.name}}
					</label>
				</div>

				<div class="top_10px">
					<button type="button" class="btn btn-xs btn-default" ng-click="OpenOptionEdit()">
						オプション
					</button>
				</div>
			</div>

			<div class="list-group-item" ng-show="OptionEdit">
				<p>形態素解析された接尾辞・接頭辞を下記に入力された語句へ「名詞」として置換します。</p>
				<textarea class="form-control replace-words" name="replace_words"></textarea>
			</div>
		</div>


		<h5>素材QueryUP  <span class="em">※CSV形式</span></h5>
		<div class="list-group">
			<div class="list-group-item">
				<a href="/sem/new/knowledge/detail/download?file_id=207">
					<img src="/sem/new/assets/img/excel.gif" border="0"> フォーマットDL
				</a>
				<div class="form-group">
					<input type="file" name="query_up_file" />
				</div>
			</div>
		</div>

		<button type="button" class="btn btn-sm btn-primary" id="query_connect">
			実行
		</button>

		<input type="hidden" name="action_type" value="<?= $action_type; ?>" />
	</form>
</div>
