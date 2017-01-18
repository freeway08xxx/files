<form method="post" name="form01" enctype="multipart/form-data">
	<div class="content legacy">

		<div class="row">
			<div class="col-sm-6">
				<div class="list-group">
					<div class="list-group-item">
						<h5>
							<span class="label label-success">1</span> 素材をアップロード
						</h5>

						<input type="file" class="file" name="material_up_file" />

						<div class="btn-area">
							<button type="button" class="btn btn-sm btn-primary" id="material_up">
								アップロード
							</button>
						</div>

					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="list-group">
					<div class="list-group-item">
						<div class="well well-sm pull-right form-inline dl-area">
							<ul class="list-inline">
								<li>
									<a href="/sem/knowledge/download_method.php?action_type=download&edit_id=8932">
										<img src="/sem/new/assets/img/excel.gif" border="0"> フォント例
									</a>
								</li>
							</ul>
						</div>

						<h5>
							<span class="label label-success">2</span> フォントをアップロード
						</h5>

						<input type="file" class="file" name="font_up_file" />

						<div class="btn-area">
							<button type="button" class="btn btn-sm btn-primary" id="font_up">
								アップロード
							</button>
						</div>

					</div>
				</div>
			</div>
		</div>

		<!-- BulkUP -->
		<div class="list-group">
			<div class="list-group-item">
				<div class="well well-sm pull-right form-inline dl-area">
					<ul class="list-inline">
						<li>
							<a href="/sem/new/segmentbanner/create/bulk_format_dl">
								<img src="/sem/new/assets/img/excel.gif" border="0"> フォーマット
							</a>
						</li>
						<li>
							<a href="/sem/new/segmentbanner/create/bulk_example_dl">
								<img src="/sem/new/assets/img/excel.gif" border="0"> 設定例
							</a>
						</li>
						<li>
							<a href="/sem/knowledge/download_method.php?action_type=download&edit_id=8948">
								<img src="/sem/new/assets/img/excel.gif" border="0"> 色コード例
							</a>
						</li>
					</ul>
				</div>

				<h5>
					<span class="label label-success">3</span> バルクファイルをアップロード
				</h5>

				<input type="file" class="file" name="bulk_up_file" />

				<div class="btn-area">
					<button type="button" class="btn btn-sm btn-primary" id="bulk_up">
						アップロード
					</button>
				</div>
			</div>

			<div class="list-group-item banner-preview">
				<span class="label label-default">バナープレビュー</span>
				<small>バルクのX座標とY座標の設定目安確認用</small>

				<div class="area-wrap clearfix">
					<div class="banner-area">
						<div class="preview-img" id="preview_img"></div>
					</div>

					<div class="param-area">
						X座標：
						<select name="x_value">
							<? for ($i = 0; $i < 4; $i++) { ?>
								<option value="<?= $i * 100 ?>"><?= $i * 100 ?></option>
							<? } ?>
						</select>
						&nbsp;
						<img title="サンプルバナー [ 400 x 120 ]に対して、X座標とY座標の指定した場合の目安の位置です。プレビューにカーソルを合わすと表示します。\n※SAMPLE TEXTの文字サイズは 20 です。"
							src="/sem/new/assets/img/help.png" />
						<br />
						Y座標：
						<select name="y_value">
							<? for ($i = 1; $i < 7; $i++) { ?>
								<option value="<?= $i * 20 ?>"><?= $i * 20 ?></option>
							<? } ?>
						</select>
					</div>
				</div>

			</div>
		</div>

		<!-- 説明文 -->
		<div class="panel panel-warning segmentbanner-description">
			<div class="panel-heading">
				<h5><strong>バナー生成手順</strong></h5>
				<ol class="desc-list">
					<li>素材をアップロードします。<br>
						<small>※既に必要な素材をアップロードしている場合は、省略してください。</small>
					</li>
					<li>フォントをアップロードします。<br>
						<small>※既に必要なフォントをアップロードしている場合は、省略してください。</small>
					</li>
					<li>バルクファイルをアップロードします。</li>
					<li>バナー生成が完了しましたら、お知らせ通知で結果が届きます。</li>
				</ol>

			</div>
		</div>




		<input type="hidden" name="action_type" />

	</div>
</form>
