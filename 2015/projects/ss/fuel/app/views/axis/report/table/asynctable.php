<div class="async_table">
	<table class="table table-striped table-hover table-condensed table-bordered">
		<thead>
			<tr>
				<th>No.</th>
				<th>媒体名</th>
				<th>アカウントID</th>
				<th>アカウント名</th>
				<th>広告掲載方式</th>
				<th>Imp</th>
				<th>Click</th>
				<th>CTR</th>
				<th>CPC</th>
				<th>Cost</th>
				<th>Rank</th>
				<th>Revenue</th>
				<th>ROAS</th>
				<th>CVs</th>
				<th>CVR</th>
				<th>CPA</th>
				<th>TotalEstCV</th>
			</tr>
		</thead>

		<tbody>
			<tr ng-repeat="item in async_data.summary">
				<td>{{$index}}</td>
				<td>{{item.campaign_deliver}}</td>
				<td>{{item.account_id}}</td>
				<td>{{item.account_name}}</td>
				<td>{{item.campaign_deliver}}</td>
				<td>{{item.imp}}</td>
				<td>{{item.click}}</td>
				<td>{{item.ctr}}</td>
				<td>{{item.cpc}}</td>
				<td>{{item.cost}}</td>
				<td>{{item.rank}}</td>
				<td>{{item.revenue}}</td>
				<td>{{item.roas}}</td>
				<td>{{item.cvs}}</td>
				<td>{{item.cvr}}</td>
				<td>{{item.cpa}}</td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>



<style>
.async_table{
	clear: both;
	width: 2000px;
	padding-top:30px
}
.async_table table{
	font-size: 10px;
	overflow-x: scroll;
	margin-top: 30px;
}


</style>