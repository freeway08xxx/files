<h4 class="bottom_20px">キャンペーン設定</h4>

<!-- escape nav-tab css setting -->
<ul class="nav nav-tabs hidden"></ul>

<ul class="nav nav-tabs setting-type">
	<li ng-class="{active: cp.isTypeExclude()}">
		<a ng-click="cp.setType('exclusion')"
		ng-disabled="report.models.cp_status.exclusion.is_dissbled">
			キャンペーンフィルタ設定
		</a>
	</li>
	<li ng-class="{active: !cp.isTypeExclude()}"
	id="attr_tab"
	 ng-click="cp.setType('attribute')">
		<a>デバイス・プロダクト属性設定</a>
	</li>
</ul>

<div class="form-block list-group menu-block"
ng-show="!cp.isTypeExclude() || cp.isTypeExcludeAndTemplateSelected()">

	<div class="controls list-group-item list-status"
	ng-show="cp.isTypeExclude()">
		<div class="control-label form-inline right_10px">設定対象</div>

		<div class="btn-group form-inline">
			<label class="btn btn-xs btn-default"
			ng-model="cp.models.menu.exclude.status"
			ng-click="" btn-radio="2">
				すべて
			</label>
			<label class="btn btn-xs btn-default"
			ng-model="cp.models.menu.exclude.status"
			ng-click="" btn-radio="0">
				除外設定済のみ
			</label>
			<label class="btn btn-xs btn-default"
			ng-model="cp.models.menu.exclude.status"
			ng-click="" btn-radio="1">
				包括設定済のみ
			</label>
		</div>
	</div>

	<div class="controls list-group-item list-status"
	ng-show="!cp.isTypeExclude()">
		<div class="control-label form-inline right_10px">設定対象</div>

		<div class="form-inline checkbox"
		ng-show="!cp.isTypeExclude()">
			<label>
				<input type="checkbox" name=""
			ng-model="cp.models.menu.attr.is_unset_only" value=""> 未設定のみ
			</label>
		</div>
	</div>

	<div class="controls list-group-item option">
		<a class="toggle-collapse"
		ng-click="cp.models.menu.is_collapse_filtertext = !cp.models.menu.is_collapse_filtertext">
			<span class="glyphicon glyphicon-filter"></span>
			キャンペーン名/IDで絞り込む
		</a>
		<div class="top_10px" collapse="cp.models.menu.is_collapse_filtertext">
			<div class="row">
				<div class="col-xs-8">

					<textarea name="" class="form-control filter-text"
					ng-model="cp.models.menu.filter.text"
					placeholder="キャンペーン名またはキャンペーンID / 改行で複数入力"></textarea>
				</div>
				<div class="col-xs-4">
					<div class="checkbox bottom_20px">
						<label class="right_10px">
							<input type="checkbox"
							ng-model="cp.models.menu.filter.is_like" ng-true-value="1">
							部分一致
						</label>
						<label>
							<input type="checkbox"
							ng-model="cp.models.menu.filter.is_except" ng-true-value="1">
							除外
						</label>
					</div>

					<div class="btn-group">
						<label class="btn btn-xs btn-default"
						ng-model="cp.models.menu.filter.type"
						ng-click="" btn-radio="1">
							AND
						</label>
						<label class="btn btn-xs btn-default"
						ng-model="cp.models.menu.filter.type"
						ng-click="" btn-radio="0">
							OR
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="regist-type"
ng-show="!cp.isTypeExclude() || cp.isTypeExcludeAndTemplateSelected()">
	<div class="btn-group report-type form-inline">
		<label class="btn btn-sm btn-default"
		id="view_setting"
		ng-model="cp.models.menu.regist_type"
		ng-click="cp.getTargetSettingList()" btn-radio="'display'">
			<span class="glyphicon glyphicon-list-alt"></span>
			画面から設定
		</label>
		<label class="btn btn-sm btn-default"
		ng-model="cp.models.menu.regist_type"
		ng-click="" btn-radio="'csv'">
			<span class="glyphicon glyphicon-save"></span>
			CSVで設定
		</label>
	</div>
</div>
<!-- / Menu Block -->

<!-- MSG if Exclude And Template Not Selected -->
<div class="msg-unavailable"
ng-hide="!cp.isTypeExclude() || cp.isTypeExcludeAndTemplateSelected()">
	キャンペーンフィルタ設定は選択中のテンプレートに対して適用されます。<br>
	キャンペーンフィルタを有効にするには、テンプレートを選択してください。
</div>
<!-- / -->

<!-- Regist Block -->
<div class="regist-block" ng-show="cp.isShowRegistBlock()">

	<div class="msg-area" ng-show="cp.models.msg.text">
		<div class="alert alert-{{cp.models.msg.attr}}">
			{{cp.models.msg.text}}
			<button class="pull-right btn btn-link close-icon" ng-click="cp.closeMsg()">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		</div>
	</div>

	<!-- Table  -->
	<div class="regist-display" ng-show="cp.models.menu.regist_type === 'display'">
		<h5 class="pull-left">画面から設定する</h5>

		<button type="button" class="btn btn-xs btn-default pull-left reload-btn"
		ng-click="cp.getTargetSettingList()">
			<span class="glyphicon glyphicon-refresh"></span>
			再読み込み
		</button>

		<div class="pull-right">
			<span class="form-inline">表示件数</span>
			<select class="form-control input-sm form-inline"
			ng-model="cp.models.regist.display.limit"
			ng-init="cp.models.regist.display.limit = common.config.table.limit[0]"
			ng-options="num for num in common.config.table.limit"></select>
			</select>
		</div>

		<table class="table table-condensed table-hover table-striped setting-table">
			<thead>
				<tr sort-items="cp.models">
					<th class="media-name">
						<button ng-click="sort.methods.toggleIsDesc('media_name'); sort.methods.orderBy('media_name')"
						class="sort" ng-class="sort.methods.getSortClass('media_name')">媒体名</button>
					</th>
					<th class="account-id">
						<button ng-click="sort.methods.toggleIsDesc('account_id'); sort.methods.orderBy('account_id')"
						class="sort" ng-class="sort.methods.getSortClass('account_id')">アカウント</button>
					</th>
					<th class="campaign-name">
						<button ng-click="sort.methods.toggleIsDesc('campaign_id'); sort.methods.orderBy('campaign_id')"
						class="sort" ng-class="sort.methods.getSortClass('campaign_id')">キャンペーン</button>
					</th>
					<th class="status text-center">
						<button ng-click="sort.methods.toggleIsDesc('status'); sort.methods.orderBy('status')"
						class="sort" ng-class="sort.methods.getSortClass('status')">ステータス</button>
					</th>

					<!-- exclude -->
					<th ng-if="cp.isTypeExclude()" class="exclusion-flg-name">
						フィルタ設定<br>
						<select class="form-control input-sm exclusion-flg"
						ng-model="cp.models.regist.display.bulk_option.exclude"
						ng-change="cp.setAll('exclude')"
						ng-options="num as name for (num, name) in common.config.report.campaign_exclude_option">
							<option value="">未設定</option>
						</select>
					</th>
					<!-- / -->

					<!-- attr -->
					<th ng-if="!cp.isTypeExclude()" class="device-name">
						デバイス<br>
						<select class="form-control input-sm"
						ng-model="cp.models.regist.display.bulk_option.device"
						ng-change="cp.setAll('device')"
						ng-options="num as name for (num, name) in common.config.report.campaign_device_type">
							<option value="">未設定</option>
						</select>
					</th>
					<th ng-if="!cp.isTypeExclude()" class="ad-type-name">
						プロダクト
						<select class="form-control input-sm"
						ng-model="cp.models.regist.display.bulk_option.ad_type"
						ng-change="cp.setAll('ad_type')"
						ng-options="num as name for (num, name) in common.config.report.report_ad_type">
							<option value="">未設定</option>
						</select>
					</th>
					<!-- / -->

					<th class="add-user">
						<button ng-click="sort.methods.toggleIsDesc('add_user'); sort.methods.orderBy('add_user')"
						class="sort" ng-class="sort.methods.getSortClass('add_user')">更新者</button>
					</th>
					<th class="add-at text-center">
						<button ng-click="sort.methods.toggleIsDesc('add_at'); sort.methods.orderBy('add_at')"
						class="sort" ng-class="sort.methods.getSortClass('add_at')">更新日時</button>
						</th>
				</tr>
			</thead>

			<tbody>
				<!-- empty info -->
				<tr ng-if="!cp.models.list">
					<td colspan="{{cp.isTypeExclude() && 7 || 8}}">該当の掲載内容が存在しません。</td>
				</tr>
				<!-- / -->

				<tr ng-if="cp.models.list"
				ng-repeat="item in cp.models.list |
							offset: cp.models.regist.display.offset |
							limitTo: cp.models.regist.display.limit">
					<td class="media-name" ng-bind="item.media_name"></td>
					<td class="name">
						<span class="label id">{{item.account_id}}</span><br>
						{{item.account_name}}
					</td>
					<td class="name">
						<span class="label id">{{item.campaign_id}}</span><br>
						{{item.campaign_name}}
					</td>
					<td class="text-center">
						<span ng-bind="item.status"
						class="label {{common.config.report.campaign_status_label[item.status]}}"></span>
					</td>

					<!-- exclude -->
					<td ng-if="cp.isTypeExclude()">
						<select class="form-control input-sm exclusion-flg"
						ng-model="cp.models.list[$index + cp.models.regist.display.offset].exclusion_flg"
						ng-change="cp.addTarget($index + cp.models.regist.display.offset)"
						ng-options="num as name for (num, name) in common.config.report.campaign_exclude_option">
							<option value="">未設定</option>
						</select>
					</td>
					<!-- / -->

					<!-- attr -->
					<td ng-if="!cp.isTypeExclude()">
						<select class="form-control input-sm"
						ng-model="cp.models.list[$index + cp.models.regist.display.offset].device_id"
						ng-change="cp.addTarget($index + cp.models.regist.display.offset)"
						ng-options="num as name for (num, name) in common.config.report.campaign_device_type">
							<option value="">未設定</option>
						</select>
					</td>
					<td ng-if="!cp.isTypeExclude()">
						<select class="form-control input-sm"
						ng-model="cp.models.list[$index + cp.models.regist.display.offset].ad_type_id"
						ng-change="cp.addTarget($index + cp.models.regist.display.offset)"
						ng-options="num as name for (num, name) in common.config.report.report_ad_type">
							<option value="">未設定</option>
						</select>
					</td>
					<!-- / -->

					<td class="add-user" ng-bind="item.add_user"></td>
					<td class="text-center" ng-bind="item.add_at"></td>
				</tr>
			</tbody>
		</table>

		<div class="clearfix">
			<pagination class="pagination-sm pull-left" boundary-links="true" rotate="false"
			ng-model="cp.models.regist.display.current" num-pages="cp.models.regist.display.num_pages"
			total-items="cp.models.regist.display.total_items" max-size="6" items-per-page="cp.models.regist.display.limit"
			previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>

			<small class="form-inline pull-right">{{cp.models.regist.display.current}} / {{cp.models.regist.display.num_pages}}</small>
		</div>



		<div ng-show="cp.models.list" class="btn-area">
			<button type="button" class="btn btn-sm btn-info" ng-click="cp.save()"
			ng-disabled="">
				上記内容で設定する
			</button>
		</div>
	</div>

	<!-- CSV -->
	<div ng-show="cp.models.menu.regist_type === 'csv'">
		<h5>CSVで設定する</h5>

		<div class="list-group form-block">
			<div class="controls list-group-item step">
				<h5 class="bottom_20px">
					<span class="label label-success">1</span>
					{{cp.isTypeExclude() && 'キャンペーンフィルタ' || 'デバイス・プロダクト属性'}} 設定シートをダウンロード
				</h5>

				<div class="bottom_10px">
					<button type="button" id="dlcsv" class="btn btn-sm btn-default" ng-click="cp.download()"
					ng-disabled="">
						<span class="glyphicon glyphicon-save"></span>
						設定用シートをダウンロード
						<span class="label label-warning left_10px">csv</span>
					</button>
				</div>
			</div>
		</div>

		<div class="list-group form-block" ng-show="cp.models.menu.regist_type === 'csv'">
			<div class="controls list-group-item step">
				<h5>
					<span class="label label-success">2</span>
					 編集済の設定シートをアップロード
				</h5>

				<div class="row bottom_20px">
					<div class="col-md-8">
						<ss-input-file ng-model="cp.models.regist.csv.file" button-value="'選択'">
					</div>
				</div>

				<div class="btn-area bottom_10px">
					<button type="button" class="btn btn-sm btn-info" ng-click="cp.upload()">
						<span class="glyphicon glyphicon-open"></span>
						シートをアップロードして設定反映
						<span class="label label-default left_10px">csv</span>
					</button>
				</div>
			</div>
		</div>
	</div>

</div>
<!-- / Regist Block -->

<!-- Annotation -->
<div class="alert alert-warning annotation"
ng-click="cp.models.is_collapse_attention = !cp.models.is_collapse_attention">

	<div class="title">
		<strong>
			{{cp.isTypeExclude() && 'キャンペーンフィルタ' || 'デバイス・プロダクト属性'}} について
		 </strong>
	</div>

	<div class="content top_20px" collapse="cp.models.is_collapse_attention">
		<div class="exclusion" ng-show="cp.isTypeExclude()">
			テンプレートごとに、キャンペーン単位でレポート実績のフィルタ（除外・包括）設定を行います。<br>

			<ul class="list-unstyled top_20px bottom_20px">
				<li class="bottom_10px">
					<strong>除外設定</strong><br>
					選択されたアカウントのうち、対象のキャンペーン<strong>を除いて</strong>レポートデータを抽出します。
				</li>
				<li>
					<strong>包括設定</strong><br>
					選択されたアカウントのうち、対象のキャンペーン<strong>のみで</strong>レポートデータを抽出します。
				</li>
			</ul>

			<div class="em">
				※ 事前に出力したいレポートをテンプレート保存しておく必要があります。<br>
				※ 同一アカウント内での除外・包括の両方を設定することはできません。<br>
				<small>（除外・包括の両方が存在する状態で更新された場合、優先的に除外設定のみが登録され、包括設定は設定解除されます。）</small>
			</div>
		</div>

		<div class="attribute" ng-show="!cp.isTypeExclude()">
			選択されたアカウントのうち、対象のキャンペーンに対して属性を設定できます。（デバイス・プロダクト）<br>
			デバイス別出力「無し」の場合に、デバイス・プロダクト別サマリ（推移）シートに反映されます。<br>
			<br>
			<small class="em">※ 属性設定は各クライアントにつき1つのみです。テンプレート別で個別設定はできません。</small>
		</div>
	</div>
</div>
