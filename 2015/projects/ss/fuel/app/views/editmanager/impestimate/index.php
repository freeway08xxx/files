<form method="post" name="form01">
	<div class="clearfix content imp-estimate legacy">

		<div class="list-group">
			<div class="list-group-item">
				<!-- 媒体 -->
				<h5>媒体</h5>
				<?
				if (is_null($imp_estimate_media) || $imp_estimate_media === IMP_ESTIMATE_MEDIA_YAHOO) {
					$imp_estimate_yahoo = "checked";
					$imp_estimate_google = "";
				} else {
					$imp_estimate_yahoo = "";
					$imp_estimate_google = "checked";
				}
				?>
				<div class="radio">
					<label>
						<input type="radio" name="imp_estimate_media" value="<?= IMP_ESTIMATE_MEDIA_YAHOO ?>" <?= $imp_estimate_yahoo ?> />Yahoo!
					</label>
					<label>
						<input type="radio" name="imp_estimate_media" value="<?= IMP_ESTIMATE_MEDIA_GOOGLE ?>" <?= $imp_estimate_google ?> />Google
					</label>
				</div>
			</div>

			<div class="list-group-item">
				<!-- 取得方式 -->
				<h5>取得方式</h5>
				<?
				if (is_null($imp_estimate_get) || $imp_estimate_get === IMP_ESTIMATE_GET_MONTH) {
					$imp_estimate_month = "checked";
					$imp_estimate_year = "";
				} else {
					$imp_estimate_month = "";
					$imp_estimate_year = "checked";
				}
				?>
				<div class="radio">
					<label>
						<input type="radio" name="imp_estimate_get" value="<?= IMP_ESTIMATE_GET_MONTH ?>" <?= $imp_estimate_month ?> />月間予測
					</label>
					<label>
						<input type="radio" name="imp_estimate_get" value="<?= IMP_ESTIMATE_GET_YEAR ?>" <?= $imp_estimate_year ?> />年間推移
					</label>
				</div>
			</div>

			<div class="list-group-item">
				<!-- 検索方式 -->
				<h5>検索方式</h5>
				<?
				if (is_null($imp_estimate_search) || $imp_estimate_search === IMP_ESTIMATE_SEARCH_EXACT) {
					$imp_estimate_exact = "checked";
					$imp_estimate_broad = "";
					$imp_estimate_phrase = "";
				} elseif ($imp_estimate_search === IMP_ESTIMATE_SEARCH_BROAD) {
					$imp_estimate_exact = "";
					$imp_estimate_broad = "checked";
					$imp_estimate_phrase = "";
				} else {
					$imp_estimate_exact = "";
					$imp_estimate_broad = "";
					$imp_estimate_phrase = "checked";
				}
				?>
				<div class="radio">
					<label>
						<input type="radio" name="imp_estimate_search" value="<?= IMP_ESTIMATE_SEARCH_EXACT ?>" <?= $imp_estimate_exact ?> />完全一致
					</label>
					<label>
						<input type="radio" name="imp_estimate_search" value="<?= IMP_ESTIMATE_SEARCH_BROAD ?>" <?= $imp_estimate_broad ?> />部分一致
					</label>
					<label>
						<input type="radio" name="imp_estimate_search" value="<?= IMP_ESTIMATE_SEARCH_PHRASE ?>" <?= $imp_estimate_phrase ?> />フレーズ一致
					</label>
				</div>
			</div>

			<!-- imp_estimate_y_only -->
			<div class="list-group-item imp_estimate_y_only">
				<!-- デバイス -->
				<h5>デバイス</h5>
				<?
				if (is_null($imp_estimate_device) || $imp_estimate_device === IMP_ESTIMATE_DEVICE_PC) {
					$imp_estimate_pc = "checked";
					$imp_estimate_sp = "";
				} else {
					$imp_estimate_pc = "";
					$imp_estimate_sp = "checked";
				}
				?>
				<div class="radio">
					<label>
						<input type="radio" name="imp_estimate_device" value="<?= IMP_ESTIMATE_DEVICE_PC ?>" <?= $imp_estimate_pc ?> />PC
					</label>
					<label>
						<input type="radio" name="imp_estimate_device" value="<?= IMP_ESTIMATE_DEVICE_SP ?>" <?= $imp_estimate_sp ?> />SP
					</label>
				</div>
			</div>

			<div class="list-group-item imp_estimate_y_only">
				<!-- 検索対象 -->
				<h5>検索対象</h5>
				<?
				if (is_null($imp_estimate_obj) || $imp_estimate_obj === IMP_ESTIMATE_OBJ_ALL) {
					$imp_estimate_all = "checked";
					$imp_estimate_yahoo_only = "";
				} else {
					$imp_estimate_all = "";
					$imp_estimate_yahoo_only = "checked";
				}
				?>
				<div class="radio">
					<label>
						<input type="radio" name="imp_estimate_obj" value="<?= IMP_ESTIMATE_OBJ_ALL ?>" <?= $imp_estimate_all ?> />全て
					</label>
					<label>
						<input type="radio" name="imp_estimate_obj" value="<?= IMP_ESTIMATE_OBJ_YAHOO ?>" <?= $imp_estimate_yahoo_only ?> />Yahoo!のみ
					</label>
				</div>
			</div>

			<div class="list-group-item imp_estimate_y_only">
				<!-- 入札価格 -->
				<h5>入札価格</h5>
				<input type="text" class="form-control input-sm form-inline" name="imp_estimate_cpc" value="<?= $imp_estimate_cpc ?>" />
			</div>
			<!-- /imp_estimate_y_only -->

			<div class="list-group-item keywords">
				<!-- キーワード -->
				<h5>キーワード</h5>
				<textarea name="imp_estimate_keyword" class="form-control"
					placeholder="キーワードを改行ごとに入力してください。"><?= $imp_estimate_keyword ?></textarea>
			</div>
		</div>

		<button type="button" class="btn btn-sm btn-primary" id="imp_estimate_get">検索予測数を取得する</button>
	</div>

	<input type="hidden" name="action_type" />
</form>
