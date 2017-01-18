<div class="list-group crawl_setting_area">
	<div class="list-group-item">
		<legend>自動実行設定</legend>

			<? if ($class_list) {
				foreach ($class_list as $item) { ?>
					<ul class="list-inline">
						<li>
							<label class="label label-default media-label">
								<i class="glyphicon glyphicon-pencil"></i> <?=$item['name']?>
							</label>

						</li>
						<li>
							<div class="checkbox">
								<label class="">
									<input type="checkbox" id="check_pc_<?=$item['id']?>" name="check_pc_<?=$item['id']?>" value="1"
										<? if ($item['device_1_crawl_type']) {
											echo "checked";
										} ?>
									>
									<span></span> PC 自動実行
								</label>
							</div>
						</li>
						<li>
							<div class="checkbox">
								<label class="">
									<input type="checkbox" name="check_sp_<?=$item['id']?>" value="1"
										<? if ($item['device_2_crawl_type']) {
											echo "checked";
										} ?>
									>
									<span></span> スマホ 自動実行
								</label>
							</div>
						</li>
						<li>
							<?
								$device_crawl_day = $item['device_1_crawl_day'];
								if (is_null($device_crawl_day)) $device_crawl_day = $item['device_2_crawl_day'];
							?>
							自動実行日
							<label class="selectbox inline" for="select_<?=$item['id']?>">
								<select name="select_<?=$item['id']?>">
									<option value=""<? if ($device_crawl_day === "") {
										echo " selected";
									} ?>>毎日実行</option>
									<option value="0"<? if ($device_crawl_day === "0") {
										echo " selected";
									} ?>>日曜日実行</option>
									<option value="1"<? if ($device_crawl_day === "1") {
										echo " selected";
									} ?>>月曜日実行</option>
									<option value="2"<? if ($device_crawl_day === "2") {
										echo " selected";
									} ?>>火曜日実行</option>
									<option value="3"<? if ($device_crawl_day === "3") {
										echo " selected";
									} ?>>水曜日実行</option>
									<option value="4"<? if ($device_crawl_day === "4") {
										echo " selected";
									} ?>>木曜日実行</option>
									<option value="5"<? if ($device_crawl_day === "5") {
										echo " selected";
									} ?>>金曜日実行</option>
									<option value="6"<? if ($device_crawl_day === "6") {
										echo " selected";
									} ?>>土曜日実行</option>
								</select>
								<span></span>
							</label>
						</li>
					</ul>
				<? }
			} ?>

		<div class="btn-area">
			<a href="#" class="btn btn-sm btn-primary" id="submit_btn">設定登録</a>
		</div>
	</div>

</div>

