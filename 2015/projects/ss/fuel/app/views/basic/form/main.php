<div class="col-sm-12">

	<!-- テキスト入力 -->
	<div class="block-title clearfix">
		<h3 class="title">input[text]</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" data-placement="left" title="text sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<?= $parts_text ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<pre>{{models.form.text}}</pre>
		<br/>
		<br/>
		※ datepickerを利用する場合は、定数の設定とイベントアクションを作成する必要がある
	</div>

	<hr/>

	<!-- input[selectbox] -->
	<div class="block-title clearfix">
		<h3 class="title">input[select]</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" rel="tooltip" data-toggle="tooltip" data-html="true" data-placement="left" title="select sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>

							<?= $parts_select ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<pre>{{models.form.select}}</pre>
		<br/>
		<br/>
		※ 検索可能のプルダウンはオブジェクトが入る<br/>
		※ 複数選択はidの配列になる<br/>
	</div>

	<hr/>

	<!-- input[checkbox] -->
	<div class="block-title clearfix">
		<h3 class="title">input[checkbox]</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" rel="tooltip" data-toggle="tooltip" data-html="true" data-placement="left" title="checkbox sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<?= $parts_checkbox ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<pre>{{models.form.checkbox}}</pre>
		<br/>
		<br/>
		※ 通常のチェックボックスは配列になるのに対し、<br/>
		※ ボタン型値のチェックボックスはIdをkeyとしたオブジェクトになる<br/>
		※ またボタン型のチェックボックスで利用するng-modelは初期化する必要がある
	</div>

	<hr/>

	<!-- input[radio] -->
	<div class="block-title clearfix">
		<h3 class="title">input[radio]</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" rel="tooltip" data-toggle="tooltip" data-html="true" data-placement="left" title="radio sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<?= $parts_radio ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<pre>{{models.form.radio}}</pre>
		<br/>
		<br/>
		※ 指定した文字を入れることが可能<br/>
	</div>

	<!-- input[radio] -->
	<div class="block-title clearfix">
		<h3 class="title">input[file]</h3>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-block list-group">
				<div class="controls list-group-item">
					<div class="row">
						<div class="col-sm-12">
							<a ss-tooltip class="pull-right" rel="tooltip" data-toggle="tooltip" data-html="true" data-placement="left" title="file sampleだよー">
								<span class="glyphicon glyphicon-question-sign"></span>
							</a>
							<?= $parts_file ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<pre>{{models.form.file}}</pre>
		<div>
		※ ファイルのアップを非同期で行う場合はオリジナルのdirectiveと、<br/>
		※ $httpクラスを利用した際の設定を変更する必要がある<br/>
		</div>
	</div>

	<hr/>

	<!-- 依頼登録内容 end -->
	<div class="form-action">
		<button type="submit" class="btn btn-primary pull-left" ng-disabled="ssTmplForm.$invalid">登録する</button>
	</div>
</div>
