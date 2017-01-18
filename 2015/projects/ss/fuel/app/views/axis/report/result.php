<div class="async_table" ng-show="async_data">

	<!-- サマリー -->
	<table class="table table-striped table-hover table-condensed table-bordered table-summary" ng-show="params.report_type==='summary'">
		<thead sort-items>
			<tr set-tables></tr>
		</thead>

		<tbody>
			<tr class="table-label" ng-repeat="item in filtered = (async_data.summary  | filter:async_data.search)
			| offset: pagination.offset
			| limitTo: pagination.limit
			| orderBy:order"
			>
				<td class="no">{{$index + 1}}</td>
				<td class="media_id"><span set-media-icon="{{item.media_id}}"></span></td>
				<td>{{item.account_id}}</td>
				<td><a href="/sem/new/axis/report/display?{{item.link}}">{{item.account_name}}</a></td>
				<td>{{item.campaign_deliver}}</td>
				<td class="detail">{{item.imp | number:0}}</td>
				<td class="detail">{{item.click | number:0}}</td>
				<td class="detail">{{item.ctr | number:2}}%</td>
				<td class="detail">¥{{item.cpc| number:0}}</td>
				<td class="detail">¥{{item.cost| number:0}}</td>
				<td class="detail">{{item.rank| number:2}}</td>
				<td class="detail">¥{{item.roas| number:0}}</td>
				<td class="detail">{{item.conv| number:0}}</td>
				<td class="detail">{{item.cvr| number:0}}%</td>
				<td class="detail">¥{{item.cpa| number:0}}</td>
				<td class="detail"></td>
			</tr>
		</tbody>
	</table>




	<!-- デイリー -->
	<table class="table table-striped table-hover table-condensed table-bordered table-daily" ng-show="params.report_type==='daily'">
		<thead sort-items>
			<tr class="head-daily"></tr>
		</thead>

		<tbody>
			<tr class="table-label" ng-repeat="item in filtered = (async_data.daily  | filter:async_data.search)
			| offset: pagination.offset
			| limitTo: pagination.limit
			| orderBy:order"
			>
				<td class="no">{{$index + 1}}</td>
				<td class="media_id"><span set-media-icon="{{item.media_id}}"></span></td>
				<td>{{item.account_id}}</td>
				<td><a href="/sem/new/axis/report/display?{{item.link}}">{{item.account_name}}</a></td>
				<td>{{item.campaign_deliver}}</td>
				<td class="detail">{{item.imp | number:0}}</td>
				<td class="detail">{{item.click | number:0}}</td>
				<td class="detail">{{item.ctr | number:2}}%</td>
				<td class="detail">¥{{item.cpc| number:0}}</td>
				<td class="detail">¥{{item.cost| number:0}}</td>
				<td class="detail">{{item.rank| number:2}}</td>
				<td class="detail">¥{{item.roas| number:0}}</td>
				<td class="detail">{{item.conv| number:0}}</td>
				<td class="detail">{{item.cvr| number:0}}%</td>
				<td class="detail">¥{{item.cpa| number:0}}</td>
				<td class="detail"></td>
			</tr>
		</tbody>
	</table>


















	<!--ページング-->
	<div class="clearfix">
		<pagination total-items="filtered.length" ng-model="pagination.currentPage" max-size="pagination.maxSize" 
		class="pagination-sm" boundary-links="true" rotate="false" num-pages="pagination.numPages" items-per-page="pagination.limit"
		previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
	</div>
</div>


