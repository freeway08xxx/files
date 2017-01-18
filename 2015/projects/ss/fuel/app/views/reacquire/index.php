<div class="form-container clearfix">
	<form name="ReacquireForm" ng-controller="ReacquireMainCtrl">
		<div class="block-title clearfix">
			<div class="row" ng-show="errorMessage">
				<div class="col-sm-6 col-sm-offset-3">
					<div class="alert alert-danger" role="alert">
						{{errorMessage}}
					</div>
				</div>
			</div>
		</div>

		<div class="form-block list-group">
			<div class="row">
				<div class="col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2">
					<div class="form-block list-group">
						<div class="row">
							<div class="col-sm-12">
								<div class="controls list-group-item">
									<div ss-client-combobox="clientComboboxConfig" ng-model="clientCombobox"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-block list-group">
						<div class="row">
							<div class="col-sm-6">
								<div class="controls list-group-item">
									<div class="form-group">
										<label>種別</label>
										<select class="form-control" ng-model="reportType" ng-required="true" ng-options="reportTypeElement as reportTypeElement.label for reportTypeElement in reportTypeElements"></select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="controls list-group-item">
									<div class="form-group">
										<label>差分</label>
										<div class="form-group">
											<div class="btn-group">
												<label class="btn btn-sm btn-default" ng-model="differenceExists" btn-radio="false"><i class="glyphicon glyphicon-ok"></i> 全アカウント表示</label>
												<label class="btn btn-sm btn-default" ng-model="differenceExists" btn-radio="true"><i class="glyphicon glyphicon-remove"></i> 差分ありのみ表示</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-block list-group">
						<div class="row">
							<div class="col-sm-12">
								<div ss-termdate="termdateConfig" ng-model="termdate"></div>
							</div>
						</div>
					</div>

					<div class="form-block list-group">
						<div class="row">
							<div class="col-sm-10 col-sm-offset-1">
								<div class="col-sm-6">
									<div class="text-center">
										<button type="submit" class="btn btn-success" ng-click="getDifferenceData()">選択したアカウントの差分を表示</button>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="text-center">
										<button type="submit" class="btn btn-primary" ng-disabled="!differenceData.length" ng-click="submit()">選択したアカウントを再取得する</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-block list-group" ng-show="differenceData.length">
			<div class="row">
				<div class="col-sm-12">
					<div class="text-center">
						<button class="btn btn-default" ng-disabled="currentPage === 0" ng-click="currentPage = currentPage - 1">前へ</button>
						{{currentPage + 1}} / {{numberOfPages()}}
						<button class="btn btn-default" ng-disabled="currentPage >= differenceData.length / pageSize - 1" ng-click="currentPage = currentPage + 1">次へ</button>
					</div>
				</div>
			</div>
		</div>

		<div class="form-block list-group" ng-show="differenceData.length">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-block list-group">
						<table class="table table-striped table-bordered table-hover difference">
							<thead>
								<tr class="table-label">
									<th><input type="checkbox" ng-click="checkToggleAll()" ng-model="isChecked"></th>
									<th>メディア</th>
									<th>アカウントID</th>
									<th>アカウント名</th>
									<th>レポート種別</th>
									<th>表示回数</th>
									<th>クリック数</th>
									<th>金額</th>
									<th>媒体CV</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat-start="differenceDatum in differenceData | offsetFrom:currentPage*pageSize | limitTo:pageSize">
									<td rowspan="3"><input type="checkbox" ng-model="differenceDatum.is_checked"></td>
									<td rowspan="3">{{differenceDatum.media_name}}</td>
									<td rowspan="3">{{differenceDatum.account_id}}</td>
									<td rowspan="3">{{differenceDatum.account_name}}</td>
									<td>{{differenceDatum.report_list[0].report_type}}</td>
									<td>{{differenceDatum.report_list[0].report.imp}}</td>
									<td>{{differenceDatum.report_list[0].report.click}}</td>
									<td>{{differenceDatum.report_list[0].report.cost}}</td>
									<td>{{differenceDatum.report_list[0].report.conv}}</td>
								</tr>
								<tr >
									<td>{{differenceDatum.report_list[1].report_type}}</td>
									<td>{{differenceDatum.report_list[1].report.imp}}</td>
									<td>{{differenceDatum.report_list[1].report.click}}</td>
									<td>{{differenceDatum.report_list[1].report.cost}}</td>
									<td>{{differenceDatum.report_list[1].report.conv}}</td>
								</tr>
								<tr ng-repeat-end>
									<td>{{differenceDatum.report_list[2].report_type}}</td>
									<td ng-class="(differenceDatum.report_list[2].report.imp !== 0) ? 'danger' : ''">{{differenceDatum.report_list[2].report.imp}}</td>
									<td ng-class="(differenceDatum.report_list[2].report.click !== 0) ? 'danger' : ''">{{differenceDatum.report_list[2].report.click}}</td>
									<td ng-class="(differenceDatum.report_list[2].report.cost !== 0) ? 'danger' : ''">{{differenceDatum.report_list[2].report.cost}}</td>
									<td ng-class="(differenceDatum.report_list[2].report.conv !== 0) ? 'danger' : ''">{{differenceDatum.report_list[2].report.conv}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="form-block list-group" ng-show="differenceData.length">
			<div class="row">
				<div class="col-sm-12">
					<div class="text-center">
						<button class="btn btn-default" ng-disabled="currentPage === 0" ng-click="currentPage = currentPage - 1">前へ</button>
						{{currentPage + 1}} / {{numberOfPages()}}
						<button class="btn btn-default" ng-disabled="currentPage >= differenceData.length / pageSize - 1" ng-click="currentPage = currentPage + 1">次へ</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- modal ng-Template -->
<script type="text/ng-template" id="progress.html">
	<div class="modal-header">通信中...</div>
	<div class="modal-body">
		<progressbar class="progress-striped active" animate="false" value="100"></progressbar>
	</div>
</script>