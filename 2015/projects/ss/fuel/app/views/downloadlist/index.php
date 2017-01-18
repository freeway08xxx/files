<div class="row contents-area tab-content" ng-controller="DownloadListBaseCtrl as baseCtrl">
	<!--tab-->
	<div class="tab-pane active">
		<div class="row clearfix">
			<ul class="nav nav-tabs">
				<li class="tab-download" ng-class="{active: baseCtrl.settings.tab == 'download'}"><a href="" ng-click="baseCtrl.settings.tab = 'download'">ダウンロード一覧</a></li>
				<li class="tab-report" ng-class="{active: baseCtrl.settings.tab == 'report'}"><a href="" ng-click="baseCtrl.settings.tab = 'report'">処理一覧</a></li>
			</ul>
		</div>
	</div>
	<!--/tab-->


	<!-- ダウンロードリスト一覧 -->
	<div class="row tab-pane active transition" ng-show="baseCtrl.settings.tab == 'download'">
		<form method="post" name="form01">
			
			<h4 class="title"><i class="glyphicon glyphicon-download-alt"></i> ダウンロード一覧</h4>

			<div class="clearfix download-list legacy">
				<fieldset class="columns">
					<button type="button" class="btn btn-xs btn-default reload-btn" id="reload">
						再読み込み
					</button>

					<div class="sub-content">
						<table class="report-table table striped display" id="download_list">
							<thead>
								<tr class="table-label">
									<th />
									<th>画面</th>
									<th>作成日時</th>
									<th>ファイル名</th>
									<th>結果DL</th>
								</tr>
							</thead>
							<tbody>
								<? foreach ($download_list as $download) { ?>
									<?
									if (isset($download["out_file_path"])
											&& is_file($download["out_file_path"])
											&& file_exists($download["out_file_path"])) {
									?>
										<tr class="table-label">
											<td>
												<label class="checkbox-label">
													<input type="checkbox" class="download_row" name="download_row" value="<?= $download["id"] ?>" <?= $download_row ?> />
												</label>
											</td>
											<td class=""><?= $download["screen_name"] ?></td>
											<td class=""><?= $download["created_at"] ?></td>
											<td class=""><?= $download["file_name"] ?></td>
											<td class="">
												<a class="btn btn-xs btn-success btn-download" href="/sem/new/downloadlist/export/dl/<?= $download["id"] ?> ?>">ダウンロード</a>
											</td>
										</tr>
									<? } ?>
								<? } ?>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn btn-sm btn-default" id="all_download_select">
						全選択
					</button>
					<button type="button" class="btn btn-sm btn-default right_10px" id="all_download_cancel">
						全解除
					</button>

					<button type="button" class="btn btn-sm btn-info" id="download_bulk_dl">
						一括DL
					</button>
					<button type="button" class="btn btn-sm btn-danger" id="download_delete">
						ファイル削除
					</button>
				</fieldset>
			</div>
			<input type="hidden" name="action_type" />
			<input type="hidden" name="download_select_row" />
		</form>
	</div>
	<!-- /ダウンロードリスト一覧 -->




	<!-- レポートリスト一覧 -->
	<div ng-show="baseCtrl.settings.tab == 'report'" class="transition row tab-pane active reportlist" ng-controller="DownloadListReportCtrl as reportCtrl" sort-items ng-cloak>


		<h4 class="title"><i class="glyphicon glyphicon-file"></i> 処理一覧</h4>
		<div>

			<div class="filter well well-sm" change-activate>
				<ul class="list-inline">
					<li>
						<h6><strong><i class="glyphicon glyphicon-search"></i> 絞り込み検索</strong></h6>
					</li>

					<li>
						<span class="form-inline">担当クライアント</span>

						<select name="client_id" class="form-control input-sm form-inline" ng-model="reportCtrl.search" ng-change="formInactive();">
							<option ng-repeat="item in reportCtrl.my_clients" value="{{item.id}}">{{item.name}}</option>
							<option value="">すべて</option>
						</select>
					</li>


					<li>
						<span class="form-inline">区分 </span>
							<select class="form-control input-sm form-inline"  ng-change="reportCtrl.setTable();" ng-model="reportCtrl.view.displayType">
							<option value="client">クライアント</option>
							<option value="account">アカウント</option>
						</select>
					</li>


					<li><span class="form-inline">フリー検索 </span>
						<label><input id="search" type="text" class="form-control form-text search" ng-model="reportCtrl.search" ng-click="formActive()" ng-change="filter();"></label>
					</li>

					<li class="self">
						<label>
							<input id="my_history" type="checkbox" ng-click="reportCtrl.setTable();filter();">
							自分の処理
						</label>
					</li>

				</ul>
			</div>


			<table class="report-table table table-condensed table-hover table-bordered">
				<thead>
					<tr class="table-label">
						<th>
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('updated_at')" class="sort" ng-class="activeClass('updated_at')">
								<strong>日時</strong>
							</button>
						</th>
						<th class="text-center" ng-hide="reportCtrl.view.displayType =='client'">
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('media_id')" class="sort media_id" ng-class="activeClass('media_id')">
								媒体
							</button>
						</th>
						<th class="text-center" ng-hide="reportCtrl.view.displayType =='client'">
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('account_id')" class="sort" ng-class="activeClass('account_id')">
								アカウントID
							</button>
						</th>
						<th ng-hide="reportCtrl.view.displayType =='client'">
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('account_name')" class="sort text-left" ng-class="activeClass('account_name')">
								アカウント名
							</button>
						</th>
						<th>
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('content')" class="sort text-left" ng-class="activeClass('content')">処理</button></th>
						<th class="status">
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('status')" class="sort text-center" ng-class="activeClass('status')">ステータス</button>
						</th>
						<th>
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('reason')" class="sort text-left" ng-class="activeClass('reason')">エラー理由</button>
						</th>
						<th>
							<button ng-click="reportCtrl.sort.isDesc=!reportCtrl.sort.isDesc;orderBy('created_user')" class="sort text-left" ng-class="activeClass('created_user')">ユーザー</button>
						</th>
					</tr>
				</thead>

				<tbody>
					<tr class="table-label" ng-repeat="item in filtered = (reportCtrl.table  | filter:reportCtrl.search)
					| offset: reportCtrl.pagination.offset
					| limitTo: reportCtrl.pagination.limit
					| orderBy:order"
					>
						<td class="text-center updated_at">{{(item.update_at) ? item.update_at : item.created_at}}</td>
						<td ng-hide="reportCtrl.view.displayType =='client'" class="media text-center"><span set-media-icon="{{item.media_id}}"></span></td>
						<td ng-hide="reportCtrl.view.displayType =='client'" class="text-center">{{item.account_id | emptyResArg:'--'}}</td>
						<td ng-hide="reportCtrl.view.displayType =='client'" class="{{item.account_name | emptyResArg:'text-center'}}">{{item.account_name | emptyResArg:'--'}}</td>
						<td class="content">{{item.content}}</td>
						<td class="status text-center"><span class="label {{item.status | statusClass}}">{{item.status}}</td>
						<td class="error">{{item.reason}}</td>
						<td class="user_name">{{item.user_name}}<span ng-hide="item.user_name">{{item.created_user}}</span></td>
					</tr>
					<tr ng-if="filtered.length===0"><td class="error_msg" colspan="13">表示するデータがありません</td></tr>
				</tbody>
			</table>

			<div class="clearfix">
				<pagination total-items="filtered.length" ng-model="reportCtrl.pagination.currentPage" max-size="reportCtrl.pagination.maxSize" 
				class="pagination-sm" boundary-links="true" rotate="false" num-pages="reportCtrl.pagination.numPages" items-per-page="reportCtrl.pagination.limit"
				previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
			</div>


		</div>
	</div>
	<!-- /レポートリスト一覧 -->


</div>


