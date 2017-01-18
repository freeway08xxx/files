<div ng-show="baseCtrl.settings.tab == 'keyword'" keyword-template ng-controller="MypageKeywordCtrl as keywordCtrl" class="transition">
	<div class="row">
		<div class="col-xs-12">
			<div class="block-title clearfix">
				<h4 class="title pull-left"><i class="glyphicon glyphicon-list-alt"></i> 実績キーワード</h4>
				<div class="summary-nav pull-left">
					<span class="term">{{keywordCtrl.models.term[0] | moment:'YYYY年M月D日'}}</span>
				</div>
			</div>

			<div class="panel panel-default">
	  					<div class="panel-body option-area text-right">
					<button type="button" class="btn btn-sm btn-primary" ng-click="register()"><i class="glyphicon glyphicon-edit"></i> キーワード登録</button>
				</div>

				<table class="table table-striped table-hover table-condensed table-keyword">
						<thead>
						<tr class="table-label">
							<th rowspan="2" class="name end">クライアント名</th>
							<th rowspan="2" colspan="1" class="account end">アカウントID</th>
							<th rowspan="2" colspan="1" class="keyword end">キーワード</th>
							<th rowspan="2" colspan="1" class="match_type end">マッチタイプ</th>
							<th colspan="8" class="keyword_result end">
								前日実績{{keywordCtrl.diff_visible}}
								<button type="button" class="btn btn-xs btn-link toggle-diff" ng-click="keywordCtrl.invisible_diff=!keywordCtrl.invisible_diff;">
									<span ng-if="keywordCtrl.invisible_diff">前々日比を表示</span>
									<span ng-if="!keywordCtrl.invisible_diff">前々日比を非表示</span>
								</button>
							</th>
						</tr>
						<tr>
							<th colspan="2" class="result sub-index">Imp</th>
							<th colspan="2" class="result sub-index">Click</th>
							<th colspan="2" class="result sub-index">Cost</th>
							<th colspan="2" class="result sub-index end">CVs</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in keywordCtrl.models.google.keyword_data">
							<td class="name end">{{item.client_name}}</td>
							<td class="account end">{{item.account_id}}</td>
							<td class="keyword end">{{item.keyword}}</td>
							<td class="match_type end">{{item.match_type | to_japanese}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_imp | label_class:'keyword'}}">{{item.diff_imp | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result">{{item.imp | number:0 | format:"no_yen"}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_click | label_class:'keyword'}}">{{item.diff_click | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result">{{item.click | number:0 | format:"no_yen"}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_cost | label_class:'keyword'}}">{{item.diff_cost | number:0 | format:"diff"}}</span></td>
							<td class="result">{{item.cost | number:0 | format}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_conv | label_class:'keyword'}}">{{item.diff_conv | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result end">{{item.conv | number:0 | format:"no_yen"}}</td>
						</tr>

						<tr ng-repeat="item in keywordCtrl.models.yahoo.keyword_data">
							<td class="name end">{{item.client_name}}</td>
							<td class="account end">{{item.account_id}}</td>
							<td class="keyword end">{{item.keyword}}</td>
							<td class="match_type end">{{item.match_type | to_japanese}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_imp | label_class:'keyword'}}">{{item.diff_imp | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result">{{item.imp | number:0 | format:"no_yen"}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_click | label_class:'keyword'}}">{{item.diff_click | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result">{{item.click | number:0 | format:"no_yen"}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_cost | label_class:'keyword'}}">{{item.diff_cost | number:0 | format:"diff"}}</span></td>
							<td class="result">{{item.cost | number:0 | format}}</td>

							<td class="result"><span ng-class="{invisible: keywordCtrl.invisible_diff}" class="{{item.diff_conv | label_class:'keyword'}}">{{item.diff_conv | number:0 | format:"diff_no_yen"}}</span></td>
							<td class="result end">{{item.conv | number:0 | format:"no_yen"}}</td>
						</tr>
						<tr ng-if="keywordCtrl.models.isDataEmpty"><td class="error_msg" colspan="13">表示するデータがありません</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<!-- キーワード登録フォーム -->
	<div class="keyword-form transition" ng-if="is_show_edit">
		<form name="keyword_form" ng-submit="send()" method="post">
			<div class="block-title clearfix">
				<h4 class="title pull-left"><i class="glyphicon glyphicon-edit"></i> キーワード登録フォーム</h4>
			</div>

			<div class="msg-area" ng-show="error_msg">
				<div class="alert alert-danger">{{error_msg}}</div>
			</div>

			<div class="panel panel-default">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<th></th>
							<th>クライアント名</th>
							<th>マッチタイプ</th>
							<th>キーワード</th>
						</tr>
						<tr ng-repeat="item in models.google.my_keywords">
							<td class="keyword_num"><span class="label label-default">{{$index + 1}}</span></td>
							<td>
								<div class="row">
									<div class="col-xs-12">
											<select ng-options="clients as clients.name for clients in models.google.my_clients track by clients.id" ng-model="models.google.my_keywords[$index + 1].client_info" class="form-control input-sm">
											<option value="">-- 未選択 --</option>
										</select>
									</div>
								</div>
							</td>
							<td class="match_type">
								<label><input value="EXACT" name="match_type_{{$index + 1}}" type="radio" ng-model="models.google.my_keywords[$index + 1].match_type">
									<span class="match_type-text">完全一致</span>
								</label>
								<label><input value="BROAD" name="match_type_{{$index + 1}}" type="radio" ng-model="models.google.my_keywords[$index + 1].match_type">
									<span class="match_type-text">部分一致</span>
								</label>
								<label><input value="PHRASE" name="match_type_{{$index + 1}}" type="radio" ng-model="models.google.my_keywords[$index + 1].match_type">
									<span class="match_type-text">フレーズ一致</span>
								</label>
							</td>
							<td>
								<div class="row">
									<div class="col-xs-12">
										<input type="text" class="form-control input-sm" value="" ng-model="models.google.my_keywords[$index + 1].keyword">
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="text-right">
				<button type="button" class="btn btn-sm btn-default" ng-click="close()">閉じる</button>
				<button type="submit" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-upload"></i> 登録内容を送信</button>
			</div>
		</form>
	</div>
</div>
