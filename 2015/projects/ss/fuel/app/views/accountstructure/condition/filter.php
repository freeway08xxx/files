<div class="container">
	<!-- サマリー種別 -->
	<div class="row">
		<div class="col-sm-12">
			<!-- フィルタリング設定 -->
			<div class="form-block list-group" ng-controller="FormFilterCtrl">
				<!-- フィルタリング設定 -->
				<div class="row">
					<div class="col-sm-3">
						<div class="block-title clearfix">
							<h4 class="title">
								フィルタ項目
								<button type="button" class="btn btn-xs btn-info left_10px"
									ng-click="addFilter()">
									<i class="glyphicon glyphicon-plus"></i> フィルタを追加
								</button>
							</h4>
						</div>
					</div>
					<div class="col-sm-9">
						<div class="block-title clearfix">
							<p class="text-danger">※複数フィルタリングする場合、同一の項目に対してのフィルタリングは不可</p>
						</div>
					</div>
				</div>
				<div class="controls list-group-item">
					<div class="filter-input"
						ng-repeat="filter in param.filters | limitTo:<?= count($GLOBALS["filter_item_list"]); ?>">
						<div class="row">
							<div class="col-sm-4">
								<h5 class="control-label">
									{{$index + 1}} 項目名 <span class="label label-warning"> <i
										class="icon-white icon-warning-sign"></i>必須
									</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'campaign' || param.filters[$index].filter_item === 'ad_group'
									|| param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'
									|| param.filters[$index].filter_item === 'negative_keyword' || param.filters[$index].filter_item === 't_userlist'
									|| param.filters[$index].filter_item === 't_placement'">
								<h5 class="control-label">フィルタ範囲</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_location'">
								<h5 class="control-label">
									地域 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_schedule'">
								<h5 class="control-label">
									曜日 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_gender'">
								<h5 class="control-label">
									性別 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_age'">
								<h5 class="control-label" ng-show="btnFilterYdn()">
									YDN年齢 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_age'">
								<h5 class="control-label" ng-show="btnFilterGoogle()">
									Google年齢 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'campaign'">
								<h5 class="control-label">キャンペーン名入力</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'ad_group'">
								<h5 class="control-label">広告グループ名入力</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'ad'">
								<h5 class="control-label">広告名入力</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'keyword'">
								<h5 class="control-label">キーワード名入力</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'negative_keyword'">
								<h5 class="control-label">
									キーワード名入力 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_userlist'">
								<h5 class="control-label">
									ユーザーリスト名入力 <span class="label label-warning">必須</span>
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 't_placement'">
								<h5 class="control-label">
									URL入力 <span class="label label-warning">必須</span>
								</h5>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<!-- サマリ別項目 -->
								<div class="list-group-item">
									<select class="form-control form-select" name="filter_item[]"
										ng-model="param.filters[$index].filter_item">
										<? foreach ($GLOBALS["filter_item_list"] as $key => $value) { ?>
										<option value="<?= $key; ?>">
											<?= $value; ?>
										</option>
										<? } ?>
									</select>
								</div>
								</br>
								<ul class="list-inline">
									<button type="button" class="btn btn-xs btn-success"
										ng-click="clearFilter($index)">
										<i class="glyphicon glyphicon-refresh"></i> クリア
									</button>
									<button type="button" class="btn btn-xs btn-danger"
										ng-click="deleteFilter($index)" ng-show="$index > 0">
										<i class="glyphicon glyphicon-minus-sign"></i> 削除
									</button>
								</ul>
							</div>
							<!-- フィルタ項目制御 -->
							<div class="col-sm-7 list-group-item"
								ng-if="param.filters[$index].filter_item === 'campaign' || param.filters[$index].filter_item === 'ad_group'
								|| param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'
								|| param.filters[$index].filter_item === 'negative_keyword' || param.filters[$index].filter_item === 't_userlist'
								|| param.filters[$index].filter_item === 't_placement'">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<!-- フィルタ範囲 -->
											<div class="col-sm-5">
												<select class="form-control form-select"
													name="filter_cond[]"
													ng-model="param.filters[$index].filter_cond">
													<? foreach ($GLOBALS["account_structure_filter_cond_list"] as $key => $value) { ?>
													<option value="<?= $key; ?>">
														<?= $value; ?>
													</option>
													<? } ?>
												</select>
											</div>
											<!-- フィルタテキストエリア -->
											<div class="col-sm-7">
												<textarea class="form-control filter-textarea" type="text"
													name="filter_text[]"
													ng-model="param.filters[$index].filter_text"
													ng-if="param.filters[$index].filter_item === 'campaign' || param.filters[$index].filter_item === 'ad_group'
													|| param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'
													|| param.filters[$index].filter_item === 'negative_keyword' || param.filters[$index].filter_item === 't_userlist'
													|| param.filters[$index].filter_item === 't_placement'"
													value="" placeholder="改行で複数検索可能"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- 地域 -->
							<div class="col-sm-2"
								ng-if="param.filters[$index].filter_item == 't_location'">
								<select class="form-control form-select" name="filter_cond[]"
									ng-model="param.filters[$index].t_location">
									<? foreach ($GLOBALS["account_structure_criterion_translation_prefecture"] as $value) { ?>
									<option value="<?= $value; ?>">
										<?= $value; ?>
									</option>
									<? } ?>
								</select></br>
							</div>
							<!-- 曜日 -->
							<div class="col-sm-8"
								ng-if="param.filters[$index].filter_item == 't_schedule'">
								<div class="row">
									<div class="col-sm-6"
										ng-if="param.filters[$index].filter_item == 't_schedule'">
										<div class="form-group">
											<div class="btn-group-xs report-type">
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.MONDAY" btn-checkbox>月曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.TUESDAY" btn-checkbox>火曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.WEDNESDAY" btn-checkbox>水曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.THURSDAY" btn-checkbox>木曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.FRIDAY" btn-checkbox>金曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.SATURDAY" btn-checkbox>土曜</label>
												<label class="btn btn-xs btn-default"
													ng-model="param.filters[$index].t_days.SUNDAY" btn-checkbox>日曜</label></br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- 性別 -->
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item == 't_gender'">
								<div class="form-group">
									<div class="btn-group-xs report-type">
										<label class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_gender.ST_MALE" btn-checkbox>男性</label>
										<label class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_gender.ST_FEMALE" btn-checkbox>女性</label>
										<label class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_gender.ST_UNKNOWN"
											btn-checkbox>不明</label>
									</div>
									</br>
								</div>
							</div>
							<!-- YDN年齢 -->
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item == 't_age'">
								<div class="form-group" ng-show="btnFilterYdn()">
									<div class="btn-group-xs report-type">
										<label class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE12_14"
											btn-checkbox>12歳～14歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE15_17"
											btn-checkbox>15歳～17歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE18_19"
											btn-checkbox>18歳～19歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE20_21"
											btn-checkbox>20歳～21歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE22_29"
											btn-checkbox>22歳～29歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE30_39"
											btn-checkbox>30歳～39歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE40_49"
											btn-checkbox>40歳～49歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE50_59"
											btn-checkbox>50歳～59歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE60_69"
											btn-checkbox>60歳～69歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_RANGE70_UL"
											btn-checkbox>70歳以上&nbsp;&nbsp;&nbsp;&nbsp;</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_ydn.GT_UNKNOWN"
											btn-checkbox>年齢不明&nbsp;&nbsp;&nbsp;&nbsp;</label>
									</div>
									</br>
								</div>
							</div>
							<!-- GOOGLE年齢 -->
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item == 't_age'">
								<div class="form-group" ng-show="btnFilterGoogle()">
									<div class="btn-group-xs report-type">
										<label class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.18to24"
											btn-checkbox>18歳～24歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.25to34"
											btn-checkbox>25歳～34歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.35to44"
											btn-checkbox>35歳～44歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.45to54"
											btn-checkbox>45歳～54歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.55to64"
											btn-checkbox>55歳～64歳</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.65ormore"
											btn-checkbox>65歳以上&nbsp;&nbsp;&nbsp;&nbsp;</label> <label
											class="btn btn-xs btn-default"
											ng-model="param.filters[$index].t_age_google.Undetermined"
											btn-checkbox>年齢不明&nbsp;&nbsp;&nbsp;&nbsp;</label>
									</div>
									</br>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4"
								ng-if="param.filters[$index].filter_item == 'ad' || param.filters[$index].filter_item == 'keyword'">
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'">
								<h5 class="control-label">フィルタ範囲</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'ad'">
								<h5 class="control-label">
									リンクURL
								</h5>
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item === 'keyword'">
								<h5 class="control-label">
									カスタムURL
								</h5>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4"
								ng-if="param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'"></div>
							<div class="col-sm-7 list-group-item"
								ng-if="param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<!-- URLフィルタ範囲 -->
											<div class="col-sm-5">
												<select class="form-control form-select"
													name="filter_cond_url[]"
													ng-model="param.filters[$index].filter_cond_url">
													<? foreach ($GLOBALS["account_structure_filter_cond_list"] as $key => $value) { ?>
													<option value="<?= $key; ?>">
														<?= $value; ?>
													</option>
													<? } ?>
												</select>
											</div>
											<!-- リンクURLとカスタムURLフィルタテキストエリア -->
											<div class="col-sm-7">
												<textarea class="form-control filter-textarea" type="text"
													name="filter_text[]"
													ng-model="param.filters[$index].filter_url"
													value="" placeholder="改行で複数検索可能"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4"
								ng-if="param.filters[$index].filter_item == 'campaign' || param.filters[$index].filter_item == 'ad_group' || param.filters[$index].filter_item == 'ad' || param.filters[$index].filter_item == 'keyword'">
							</div>
							<div class="col-sm-3"
								ng-if="param.filters[$index].filter_item == 'campaign' || param.filters[$index].filter_item == 'ad_group' || param.filters[$index].filter_item == 'ad' || param.filters[$index].filter_item == 'keyword'">
								<h5 class="control-label">ステータス</h5>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4"></div>
							<div class="col-sm-7"
								ng-if="param.filters[$index].filter_item === 'campaign' || param.filters[$index].filter_item === 'ad_group' || param.filters[$index].filter_item === 'ad' || param.filters[$index].filter_item === 'keyword'">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<!-- ステータス -->
											<div class="col-sm-5 list-group-item"
												ng-if="param.filters[$index].filter_item === 'campaign' || param.filters[$index].filter_item === 'ad_group' || param.filters[$index].filter_item == 'ad' || param.filters[$index].filter_item == 'keyword'">
												<select class="form-control form-select"
													name="filter_cond[]"
													ng-model="param.filters[$index].filter_status">
													<? foreach ($GLOBALS["account_structure_status_list"] as $key => $value) { ?>
													<option value="<?= $key; ?>">
														<?= $value; ?>
													</option>
													<? } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						</br>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- {{param.filters}} -->
<!-- {{clientCombobox.accounts}} -->
