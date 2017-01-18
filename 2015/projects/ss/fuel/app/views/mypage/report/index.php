<div class="form-container clearfix" ng-controller="MypageBaseCtrl as baseCtrl" id="js-scope_base">

	<!-- information  -->
	<?= $information ?>
	<!-- /information -->


	<!-- report_nav  -->
	<?= $mypage_nav ?>
	<!-- /report_nav -->

	<div class="report-block" ng-init="reportCtrl.init()" ng-controller="MypageReportCtrl as reportCtrl">
		<div class="row summary transition" sort-items ng-show="baseCtrl.settings.tab == 'summary'">
			<div class="col-xs-12">
				<div class="block-title clearfix">
					<h4 class="title pull-left"><i class="glyphicon glyphicon-list-alt"></i> 実績サマリ</h4>
					<div class="summary-nav pull-left">
						<span class="term" ng-show="baseCtrl.isLastMonth">{{baseCtrl.models.report.lastmonth.period.first | moment:'YYYY年M月D日'}}{{baseCtrl.models.report.lastmonth.period.last | moment:'〜D日'}}</span>
						<span class="term" ng-hide="baseCtrl.isLastMonth">{{baseCtrl.models.report.thismonth.period.first | moment:'YYYY年M月D日'}}{{baseCtrl.models.report.thismonth.period.last | moment:'〜D日'}}</span>
                        <button type="button" class="btn btn-xs btn-default js-showEntryGraph" ng-click="baseCtrl.isLastMonth =!baseCtrl.isLastMonth;orderBy(baseCtrl.sortItems.place);" ng-disabled="!baseCtrl.isloaded.lastmonth">
                        	<i ng-if="!baseCtrl.isloaded.lastmonth"><img src="/sem/new/assets/img/icon_loading.gif" alt="loading icon" width="11"></i>
                        	<span ng-hide="baseCtrl.isLastMonth">前月分</span>
                        	<span ng-show="baseCtrl.isLastMonth">今月分</span>
                        </button>

						<div class="block-right">
							<div class="btn-group">
								<label class="btn btn-xs btn-default js-showEntryGraph" ng-model="baseCtrl.sortItems.adType" btn-radio="'search'" ng-click="typeChoice('search');">Search順</label>
								<label class="btn btn-xs btn-default js-showEntryGraph" ng-model="baseCtrl.sortItems.adType" btn-radio="'display'" ng-click="typeChoice('display');">Display順</label>
							</div>

							<button type="button" class="btn btn-xs btn-default" ng-click="reportCtrl.update();" ng-disabled="!baseCtrl.isloaded.lastmonth" >
								<i class="glyphicon glyphicon-repeat"></i>
							</button>
						</div>
					</div>
				</div>
				

				<!--graph-->
				<div class="panel panel-default graph-area" ng-controller="MypageGraphCtrl as graphCtrl">
					<div ng-if="baseCtrl.isloaded.thismonth" graph-directive>
						<div id="chart"></div>
					</div>
				</div> 
				<!--/graph-->

				<div class="panel panel-default">
					<div class="panel-body option-area">
						<div class="row">
							<div class="col-xs-7">
								<button type="button" class="btn btn-link">実績数値</button>
								<button type="button" class="btn btn-default disabled">品質スコア</button>
							</div>
							<div class="col-xs-5 text-right">
								<input type="text" class="form-control" name="search_client" placeholder="クライアント名検索" ng-model="mySearch.search.client_name">
							</div>
						</div>
					</div>

					<table class="table table-striped table-hover table-condensed" check-empty>

						<!--thead-->
						<thead>
							<tr class="table-label">
                                <th colspan="2" rowspan="2" class="name">
                                    <button ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('client_name')" class="sort" ng-class="activeClass('client_name')">クライアント名</button>
                                </th>

                                <th colspan="6" class="result end result-width">
                                	 <button ng-if="!baseCtrl.isLastMonth" type="button" class="btn btn-xs btn-link toggle-diff" ng-click="baseCtrl.diff.visible=!baseCtrl.diff.visible;">
                                	 	<span ng-if="baseCtrl.diff.visible">前日比を非表示</span>
										<span ng-if="!baseCtrl.diff.visible">前日比を表示</span>
                                	 </button>
                                    当月実績
                                </th>
                                
                                <th colspan="3" class="forecast">着地予想</th>
                                <th rowspan="2" class="rate">消化率</th>
                                <th rowspan="2" class="gross-profit">
                                    <button ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('gross_margin')" class="sort" ng-class="activeClass('gross_margin')">当月粗利</button>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="result sub-index">
                                    <button ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('cost')" class="sort" ng-class="activeClass('cost')">Cost</button>
                                </th>  
                                <th colspan="2" class="result sub-index">
                                    <button ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('conv')" class="sort" ng-class="activeClass('conv')">CVs</button>
                                </th>
                                <th colspan="2" class="result sub-index end">
                                    <button ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('cpa')" class="sort" ng-class="activeClass('cpa')">CPA</button>
                                </th>
                                <th class="forecast sub-index">
                                    <button ng-hide="baseCtrl.isLastMonth" ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('forecast_cost')"; class="sort" ng-class="activeClass('forecast_cost')">Cost</button>
                                </th>
                                <th class="forecast sub-index">
                                     <button ng-hide="baseCtrl.isLastMonth" ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('forecast_conv')"; class="sort" ng-class="activeClass('forecast_conv')">CVs</button>
                                </th>
                                <th class="forecast sub-index end">
                                    <button ng-hide="baseCtrl.isLastMonth" ng-click="baseCtrl.sortItems.isDesc=!baseCtrl.sortItems.isDesc;orderBy('forecast_cpa')"; class="sort" ng-class="activeClass('forecast_cpa')">CPA</button>
                                </th>
							</tr>
						</thead>
						<!--/thead-->


						<!--thismonth tbody-->
					    <tbody ng-show="!baseCtrl.isLastMonth && baseCtrl.isloaded.thismonth" class="thismonth">
							<tr ng-repeat-start="item in baseCtrl.models.report.thismonth.results | filter:mySearch | orderBy:order" isloaded-thismonth ng-class="(baseCtrl.sortItems.adType =='search') ? 'active-items' : ''">
									<td rowspan="2" class="name">{{item.search.client_name}}
										<span ng-if="item.search.stop_account_cnt" class="account_alert"><a href="/sem/new/alert/budget/list/{{item.search.client_id}}">停止中のアカウントが<strong> {{item.search.stop_account_cnt}}</strong>件あります</span>
									</td>
									<td class="type small">Search</td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.search.dailyDiff_cost | label_class}}">{{item.search.dailyDiff_cost | number:0 | format:"diff"}}</span></td>
									<td class="result">{{item.search.cost | number:0 | format}}</td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.search.dailyDiff_conv | label_class}}">{{item.search.dailyDiff_conv | number:0 | format:"diff_no_yen"}}</span></td>
									<td class="result">{{item.search.conv | number:0 | format:"no_yen"}} </td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.search.dailyDiff_cpa | label_class}}">{{item.search.dailyDiff_cpa | number:0 | format:"diff"}}</span></td>
									<td class="result end">{{item.search.cpa | number:0| format}} </td>
									<td class="forecast">{{item.search.forecast_cost | number:0 | format}} </td>
									<td class="forecast">{{item.search.forecast_conv | number:0| format:"no_yen"}} </td>
									<td class="forecast end">{{item.search.forecast_cpa | number:0 | format}} </td>
									<td class="rate sparkline"><!-- <progressbar class="progress-striped active" max="100" value="80" type="info"><i>%</i> </progressbar>-->--</td>
									<td class="gross-profit">{{item.search.gross_margin | number:0| format}}</td>  
							</tr>

	 						 <tr ng-repeat-end ng-class="(baseCtrl.sortItems.adType =='display') ? 'active-items':''">
									<td class="type small">Display</td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.display.dailyDiff_cost | label_class}}">{{item.display.dailyDiff_cost | number:0| format:"diff"}} </span></td>
									<td class="result">{{item.display.cost | number:0 | format}} </td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.display.dailyDiff_conv | label_class}}">{{item.display.dailyDiff_conv | number:0 | format:"diff_no_yen"}} </span></td>
									<td class="result">{{item.display.conv | number:0 | format:"no_yen"}} </td>
									<td class="result"><span ng-class="{invisible: !baseCtrl.diff.visible}" class="{{item.display.dailyDiff_cpa | label_class}}">{{item.display.dailyDiff_cpa | number:0| format:"diff"}} </span></td>
									<td class="result end">{{item.display.cpa | number:0| format}} </td>
									<td class="forecast">{{item.display.forecast_cost | number:0| format}} </td>
									<td class="forecast">{{item.display.forecast_conv | number:0| format:"no_yen"}} </td>
									<td class="forecast end">{{item.display.forecast_cpa | number:0| format}} </td>
									<td class="rate sparkline"><!-- <progressbar class="progress-striped active" max="100" value="80" type="success"><i>%</i></progressbar>-->--</td>
									<td class="gross-profit">{{item.display.gross_margin | number:0| format}}</td>  
							</tr>
						</tbody>
						<!--/thismonth /tbody-->


						<!--lastmonth tbody-->
					    <tbody ng-show="baseCtrl.isLastMonth && baseCtrl.isloaded.lastmonth" class="lastmonth">
							<tr ng-repeat-start="item in baseCtrl.models.report.lastmonth.results | filter:mySearch | orderBy:order" isloaded-lastmonth ng-class="(baseCtrl.sortItems.adType =='search') ? 'active-items' : ''">
									<td rowspan="2" class="name">{{item.search.client_name}}
										<span ng-if="item.search.stop_account_cnt" class="account_alert"><a href="/sem/new/alert/budget/list/{{item.search.client_id}}">停止中のアカウントが<strong> {{item.search.stop_account_cnt}}</strong>件あります</span>
									</td>
								 	<td class="type small">Search</td>
									<td colspan="2" class="result">{{item.search.cost | number:0 | format}}</td>
									<td colspan="2" class="result">{{item.search.conv | number:0| format:"no_yen"}}</td>
									<td colspan="2" class="result end">{{item.search.cpa | number:0| format}}</td>
								    <td class="forecast">--</td>
									<td class="forecast">--</td>
									<td class="forecast end">--</td> 
									<td class="rate sparkline"><!-- <progressbar class="progress-striped active" max="100" value="80" type="info"><i>%</i></progressbar> -->--</td>
									<td class="gross-profit">{{item.search.gross_margin | number:0| format}}</td>  
							</tr>

	 						 <tr ng-repeat-end ng-class="(baseCtrl.sortItems.adType =='display') ? 'active-items' : ''"> 
									<td class="type small">Display</td>
									<td colspan="2" class="result">{{item.display.cost | number:0| format}}</td>
								    <td colspan="2" class="result">{{item.display.conv | number:0| format:"no_yen"}}</td>
									<td colspan="2" class="result end">{{item.display.cpa | number:0| format}}</td>
									<td class="forecast">--</td>
									<td class="forecast">--</td>
									<td class="forecast end">--</td>
									<td class="rate sparkline"><!-- <progressbar class="progress-striped active" max="100" value="80" type="success"><i>%</i></progressbar> -->--</td>
									<td class="gross-profit">{{item.display.gross_margin | number:0| format}}</td>  
							</tr> 
						</tbody>
						<!--/lastmonth tbody-->
					</table>

					<div class="loading" ng-hide="(baseCtrl.isLastMonth && baseCtrl.isloaded.lastmonth) || (!baseCtrl.isLastMonth && baseCtrl.isloaded.thismonth)">
						<p><i><img src="/sem/new/assets/img/icon_loading.gif" alt="loading icon" width="25"></i>Now loading....</P>
					</div>

				</div>
			</div>
	</div>


	<!-- keyword  -->
	<?= $keyword ?>
	<!-- /keyword -->



	<!-- /report-block -->
</div>






