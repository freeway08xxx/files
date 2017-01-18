/* Directives */
var directives = angular.module('axis.directives', []);

/**
 * テーブルの生成、管理 
 */
directives.directive('setTables', [ '$compile','axisConst','tableFactoryService', function($compile,axisConst,tableFactoryService){
	return {
		scope: false,
		replace: false,
		restrict: 'AE',
		link: function (scope, $elm, $attrs) {

			scope.setTables = function(){
				scope.table = {
					settings : {
						//デバイス切り換え
						device : "all_devices"
					},
					//テーブル構成するエレメント
					elem : {
						report_elem_list     : {},
						summary_elem_list    : {},
						all_elem_list        : {},
						date_list            : [],
						selected_ext_cv_list : {},
						all_ext_cv_list      : {},
						device_list          : {}
					},
					//結果表示画面,テンプレート用
					views : {
						report_elem_list  : {},
						summary_elem_list : {},
						ext_cv_list       : {},
						filter_items     : {
							campaign_deliver :{}
						}
					}
				}

				scope.async_data.report_elem_list = tableFactoryService.withoutKeyByStr(scope.async_data.report_elem_list,'ext_');
				var base_elem_list                = _.omit(scope.async_data.summary_elem_list ,'media_name','report_date','term_view');
				var report_format                 = scope.params.report_format;

				//サマリタイプがメディアの時はmedia_idを差し込み
				if (scope.params.summary_type == 'media' ) base_elem_list = $.extend(true, base_elem_list, {'media_id':'媒体'});

				scope.table.elem.report_elem_list   = scope.async_data.report_elem_list; 
				scope.table.elem.summary_elem_list  = (report_format =='summary') ? _.omit(base_elem_list , 'indicate','all') : $.extend(true,base_elem_list, axisConst.depth_indicate_cell);
				scope.table.elem.all_elem_list      = $.extend(true,{},scope.table.elem.summary_elem_list,scope.table.elem.report_elem_list);
				scope.table.elem.device_list        = _.keys(scope.async_data[report_format]["device"]); 
				scope.table.elem.date_list          = (report_format =='term_compare') ? tableFactoryService.arrUnion(_.values(scope.async_data.add_cell), _.values(scope.async_data.diff)) : scope.async_data.add_cell;
				scope.table.views.summary_elem_list = tableFactoryService.objOrder(_.omit(angular.copy(scope.table.elem.summary_elem_list) , 'indicate','all'));
				scope.table.views.report_elem_list  = tableFactoryService.objOrder( scope.async_data.report_elem_list ); 

				_.each(scope.table.elem.device_list, function(device){
					var template  = '<div class="table-'+device+'" ng-show="table.settings.device === '+"'"+device+"'"+'" cell-hide>';
						template += '<table class="table-hover"><thead set-sort-items="'+device+'"><tr set-table-heads="'+device+'"></tr>';
						template += '<tr set-table-add-heads="'+device+'"></tr></thead><tbody set-table-bodys="'+device+'"></tbody></table>';
						template += '<div set-table-pagenation="'+device+'" class="clearfix"></div></div>';

					$(".set-"+report_format+"-tables").append($compile(template)(scope));
				});
			}
		}
	}
}]);



/**
 * テーブルヘッド生成 
 */
directives.directive('setTableHeads', [ '$compile','axisConst','tableFactoryService','utilFactoryService', function($compile,axisConst,tableFactoryService,utilFactoryService){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			var format          = scope.params.report_format;
			var device          = $attrs.setTableHeads;
			var add_elem_list   = {};
			var head_elem_list  = (format === 'summary') ? angular.copy(scope.table.elem.all_elem_list) : angular.copy(scope.table.elem.summary_elem_list);

			 if(format === 'daily' || format === 'term_compare'){
				//日別推移 日付format
				_.each(scope.table.elem.date_list , function(key){
					add_elem_list[key] = tableFactoryService.getFormat(format,key);
				});
				$.extend(true,head_elem_list,add_elem_list);
			}

			//外部CV追加セル
			if(scope.params.ext_cv_list.length > 0){
				_.each(scope.params.ext_cv_list, function(value,i){
					scope.table.elem.selected_ext_cv_list['ext_cv_'+i] = value['cv_display'];
				});

				//サマリーはテーブルヘッドに追加
				if(format === 'summary') $.extend(true,head_elem_list,axisConst.extCvSumDisplay,scope.table.elem.selected_ext_cv_list);
			}


			//テーブルヘッドセル生成
			_.each(head_elem_list, function(value,key){
				var viewName     = tableFactoryService.getViewName(format,value,scope);
				var sort         = (key === 'indicate' || key === 'all' || key.indexOf('term') >-1 || key.indexOf('date') >-1 || key.indexOf('diff') >-1) ? 'sort no_sort' : 'sort';
				var tableSpan    = tableFactoryService.getCell(format,key);
				var cellName     = tableFactoryService.getCellName(format,key,_.keys(scope.table.elem.selected_ext_cv_list));
				var className    = (cellName.indexOf('parent_excv_cell') != -1) ? cellName+' parent_excv_cell text-center':cellName;
				var cellHideName = (utilFactoryService.isNumber(cellName.charAt(0)) || cellName.match(/[^0-9a-zA-Z_]+/)!==null ) ? '':'.'+cellName;
				scope.isShow.basic[cellName] = true;


				var template     = '<th rowspan="'+tableSpan.rowspan+'" colspan="'+tableSpan.colspan+'" class="'+className+'" ng-show="isShow.basic'+cellHideName+'">'
					template    += '<button ng-click="sort.'+device+'.isDesc=!sort.'+device+'.isDesc;orderBy('+"'"+device+"','"+key+"'"+')" '
					template    += 'class="'+sort+'" ng-class="activeClass('+"'"+device+"','"+key+"'"+')">'+viewName+'</button></th>';

				$elm.append($compile(template)(scope));
			});
		}
	}
}]);


/**
 * 外部cv用テーブルヘッド追加
 */
directives.directive('setTableAddHeads', [ '$compile','tableFactoryService', function($compile,tableFactoryService){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			var device   = $attrs.setTableAddHeads;
			var format   = scope.params.report_format;
			var addHead  = ['ext_cv_all','ext_cvr_all','ext_cpa_all'];
			var ext_list = {};

			if(scope.params.ext_cv_list.length == 0 )  return false;

			if(scope.params.ext_cv_list.length > 0){
				ext_list['ext_cv_all']  = "外部CV合計 (CV)";
				ext_list['ext_cvr_all'] = "外部CV合計 (CVR)";
				ext_list['ext_cpa_all'] = "外部CV合計 (CPA)";
			};

			_.each(_.values(scope.table.elem.selected_ext_cv_list), function(value,i){
				addHead.push('ext_cv_'+i,'ext_cvr_'+i,'ext_cpa_'+i);

				ext_list['ext_cv_'+i]  =  value+ "(CV)";
				ext_list['ext_cvr_'+i] =  value+ "(CVR)";
				ext_list['ext_cpa_'+i] =  value+ "(CPA)";
			});

			scope.table.views.report_elem_list = tableFactoryService.objOrder($.extend(true,{},scope.table.elem.report_elem_list ,ext_list)); 
			scope.table.views.ext_cv_list      = tableFactoryService.objOrder(ext_list);
			scope.table.elem.all_ext_cv_list   = angular.copy(ext_list);

			_.each(addHead, function(value){
				scope.isShow.basic[value] = true;

				if (format =='summary'){
					if(value.indexOf('cv')  !== -1) var viewName = 'CV';
					if(value.indexOf('cvr') !== -1) var viewName = 'CVR';
					if(value.indexOf('cpa') !== -1) var viewName = 'CPA';

					var template = '<th class="ext_cv"  ng-show="isShow.basic.'+value+'"><button ng-click="sort.'+device+'.isDesc=!sort.'+device+'.isDesc;orderBy('+"'"+device+"','"+value+"'"+');" class="sort" ng-class="activeClass('+"'"+device+"','"+value+"'"+')">'+viewName+'</button></th>';
					$elm.append($compile(template)(scope));
				}
			});
		}
	}
}]);



/**
 * テーブルボディ生成 
 */
directives.directive('setTableBodys', [ '$compile','axisConst','tableFactoryService', function($compile,axisConst,tableFactoryService){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			var template        = '';
			var templateSetting = '';
			var templateBody    = '';
			var templateClose   = '';
			var device          = $attrs.setTableBodys;
			var format          = scope.params.report_format;
			var directiveName   = (format === 'daily') ? 'set-table-daily-cell':'set-table-term_compare-cell';
			var body_elem_list  = (format === 'summary') ? scope.table.elem.all_elem_list : _.omit(scope.table.elem.all_elem_list,'indicate','all');
			var rowspan         = function (){
				return Object.keys(scope.table.elem.report_elem_list).length + Object.keys(scope.table.elem.all_ext_cv_list).length;
			};
			scope.initCell();

			/**
			 * テーブルボディ生成 指標が横のタイプ
			 */
			if(format==='summary'){
				templateSetting = '<tr ng-repeat="summary in '+device+'_filtered = (async_data.summary.device.'+device+' | filter:reportFilters ) | orderBy:order.'+device+' | customOrderBy | offset: pagination.device.'+device+'.offset | limitTo: pagination.limit" class="table-label {{summary.account_name | addSumClass}}">';


				_.each(body_elem_list, function(value,key){
					var number_format = (axisConst.number_format.indexOf(key) >= 0 ) ? '| number ':'';

					switch (key){
						case 'media_id':
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><span set-media-icon="{{summary.media_id}}"></span> {{summary.media_id | replaceSumTitle:summary.account_name:"'+key+'"}}</td>';break;

						case 'account_id':
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><span set-media-icon="{{summary.media_id}}"></span> <span>{{summary.'+key+' | format}}</span></td>';break;

						case 'category_big_name':
						case 'category_middle_name':
							var replaceSumTitle = (key==='category_big_name') ? '| replaceSumTitle:summary.account_name' : '' ;
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><a class="'+key+'" ng-click="tableActions.moveGranularity($event)">';
							templateBody += '{{summary.'+key+' | format '+replaceSumTitle+'}}</a></td>'; 
							break;

						//画像あり
						case 'image_name':
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><a class="{{summary.'+key+' | addImageClass}}" '
							templateBody += 'popover-html-unsafe="<img src='+"{{summary."+key+"}}"+'>" popover-trigger="mouseenter" popover-append-to-body="true">'
							templateBody += '<span class="image-file"><img hide-dead-link ng-src="{{summary.'+key+' | showImage}}"></span>';
							templateBody += '<span class="image-text">{{summary.image_title | format}}</span>';
							templateBody += '</a></td>';
							break;

						case 'account_name':
						case 'campaign_name':
						case 'ad_group_name':
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><a class="'+key+'" data-info="{{summary.info}}" ng-click="tableActions.moveGranularity($event)">';
							templateBody += '{{summary.'+key+' | format}}</a></td>'; break;

						//URLがつくもの
						case 'link_url':
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'"><a class="'+key+'" href="{{summary.'+key+'}}" target="_brank">';
							templateBody += '{{summary.'+key+' | format}}</a></td>'; break;

						default:
							templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'">{{summary.'+key+' '+number_format+' | format:"'+key+'" }}</td>'; break;
					}
				});

				/**
				 * 外部cv用テーブルbody追加 指標が横
				 */
				var addBody = function(){
					var res = '';
					if(scope.params.ext_cv_list.length > 0){
						_.each(scope.table.elem.all_ext_cv_list, function(val,key){
							res += '<td class="ext_cv" ng-show="isShow.basic.'+key+'">{{summary.'+key + '| number | format:"'+key+'"}}</td>';
						});
					}
					return res;
				}

				templateClose = '</tr>';
				template = templateSetting + templateBody + addBody() + templateClose;


			/**
			 * テーブルボディ生成 指標が縦のタイプ
			 */
			}else if(format==='daily' || format==='term_compare'){

				templateSetting = '<tr ng-repeat-start="'+format+' in '+device+'_filtered = (async_data.'+format+'.device.'+device+' | filter:async_data.search) | orderBy:order.'+device+' | customOrderBy | offset: pagination.device.'+device+'.offset | limitTo: pagination.limit" '+directiveName+'="'+scope.table.views.report_elem_list[0]['key']+'" class="{{'+format+'.account_name | addSumClass}}">';

				var indicates       = _.union(scope.option_arr , _.keys(axisConst.commonValues))


				_.each(body_elem_list, function(value,key){
					var number_format = (axisConst.number_format.indexOf(key) >= 0 ) ? '| number ':'';

					switch (key){
						case 'media_id':
							templateBody += '<td class="account_id {{'+format+'.account_name | addSumClass}}" rowspan="'+rowspan()+'">'
							templateBody += '<span set-media-icon="{{'+format+'.media_id}}"></span>{{'+format+'.media_id | replaceSumTitle:'+format+'.account_name:"'+key+'"}}</td>'; break;

						case 'account_id':
							templateBody += '<td class="account_id {{'+format+'.account_name | addSumClass}}" rowspan="'+rowspan()+'" ng-show="isShow.basic.'+key+'">'
							templateBody += '<span set-media-icon="{{'+format+'.media_id}}"></span> <span>{{'+format+'.account_id | format}}</span></td>'; break;


						//カテゴリ
						case 'category_big_name':
						case 'category_middle_name':
							var replaceSumTitle = (key==='category_big_name') ? '| replaceSumTitle:'+format+'.account_name' : '' ;
							templateBody += '<td rowspan="'+rowspan()+'" class="'+key+'" ng-show="isShow.basic.'+key+'">'
							templateBody += '<a class="'+key+'" ng-click="tableActions.moveGranularity($event)">{{'+format+'.'+key+' | format '+replaceSumTitle+'}}</a></td>'; break;

						//粒度リンクがつく
						case 'account_name':
						case 'campaign_name':
						case 'ad_group_name':
							templateBody += '<td rowspan="'+rowspan()+'" class="'+key+'" ng-show="isShow.basic.'+key+'">'
							templateBody += '<a class="'+key+'" data-info="{{'+format+'.info}}" ng-click="tableActions.moveGranularity($event)">{{'+format+'.'+key+' | format}}</a></td>'; break;

						//URLがつく
						case 'link_url':
							templateBody += '<td rowspan="'+rowspan()+'" class="'+key+'" ng-show="isShow.basic.'+key+'">'
							templateBody += '<a href="{{'+format+'.'+key+'}}">{{'+format+'.'+key+' | format}}</a></td>'; break;

						//画像あり
						case 'image_name':
							templateBody += '<td rowspan="'+rowspan()+'" class="'+key+'" ng-show="isShow.basic.'+key+'"><a class="{{'+format+'.'+key+' | addImageClass}}" ';
							templateBody += 'popover-html-unsafe="<img src='+'{{'+format+'.'+key+'}}'+'>" popover-trigger="mouseenter" popover-append-to-body="true">'
							templateBody += '<span class="image-file"><img hide-dead-link ng-src="{{'+format+'.'+key+' | showImage}}"></span><span class="image-text">{{'+format+'.image_title  | format}}</span></a></td>';break;

						default:
							//１番目始めの指標
							if(scope.table.views.report_elem_list[0]['key'] === key){
								templateBody += '<th class="indicate" ng-show="isShow.basic.'+key+'">'+value+'</th>'
								templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'">{{'+format+'.'+key+' '+number_format+'| format:"'+key+'" }}</td></tr>'; break;
							//2番目以降の指標
							}else if( _.indexOf(indicates,key) >=0 ){
								templateBody += '<tr class="'+key+' {{'+format+'.account_name | addSumClass}}" '+directiveName+'="'+key+'">'
								templateBody += '<th class="indicate" ng-show="isShow.basic.'+key+'">'+value+'</th>'
								templateBody += '<td class="'+key+'" ng-show="isShow.basic.'+key+'">' + '<span>{{'+format+'.'+key+' '+number_format+'| format:"'+key+'" }}</span></td></tr>';
								break;
							//指標以外
							}else{
								templateBody += '<td class="'+key+'" rowspan="'+rowspan()+'" ng-show="isShow.basic.'+key+'">{{'+format+'.'+key+' '+number_format+'| format:"'+key+'"}}</td>';
								break;
							}

					}
				});

				/**
				 * 外部cv用テーブルbody追加 指標が縦
				 */
				var addBody = function(){
					var res = '<tr ng-repeat-end></tr>';
					if(scope.params.ext_cv_list.length > 0){
						var res = '';
						_.each(scope.table.views.ext_cv_list, function(elm,i){
							var last_repeat =  ( i  == scope.table.views.ext_cv_list.length -1) ? ' ng-repeat-end' : '';
							res += '<tr' +last_repeat+' '+directiveName+'="'+elm['key']+'" class="{{'+format+'.account_name | addSumClass}}">'
							res += '<td class="indicate" ng-show="isShow.basic.'+elm['key']+'">'+elm['value']+'</td>' 
							res += '<td ng-show="isShow.basic.'+elm['key']+'">{{'+format+'.'+elm['key']+' | format:"'+elm['key']+'"}}</td></tr>';
						});
					}
					return res;
				}

				template = templateSetting + templateBody + addBody();
			}

			/**
			 * 共通コンパイル
			 */
			$elm.append($compile(template)(scope));
		}
	}
}]);



/**
 * 日付セル 自動生成
 */
directives.directive('setTableDailyCell', [ '$compile',function($compile){
	return {
		scope: false,
		replace: true,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {

			//テーブルセル生成
			if(typeof scope.table.elem.date_list !='undefined'){
				var indicate = $attrs.setTableDailyCell;

				_.each(scope.table.elem.date_list, function(date){
					var template  = '<td class="'+indicate+'" ng-show="isShow.basic.'+indicate+'">'
					    template += '{{daily.'+date+'.'+indicate+' | number | format:"'+indicate+'" }}</td>'

					$elm.append($compile(template)(scope));
				})
			}
		}
	}
}]);


/**
 * 期間比較セル 自動生成
 */
directives.directive('setTableTermCompareCell', ['$compile',function($compile){
	return {
		scope: false,
		replace: true,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {

			if(typeof scope.table.elem.date_list !='undefined'){
				var indicate = $attrs.setTableTermCompareCell;
				//テーブルセル生成
				_.each(scope.table.elem.date_list, function(term){

					var percentage = indicate+"_percentage";
					var value      = 'term_compare.'+term+'.'+indicate;
					var template   = '<td class="'+indicate+'" ng-show="isShow.basic.'+indicate+'">';
					    template  += '{{'+value+' | number | format:"'+indicate+'"}}<span class="{{'+value+' | label_class}}" ng-show="{{term_compare.'+term+'.'+percentage+'>=0}}">';
					    template  += '{{term_compare.'+term+'.'+percentage+' | number:2 | format}}%</span></td>'

					$elm.append($compile(template)(scope));
				})
			}
		}
	}
}]);


/**
 * ページネーション生成 
 */
directives.directive('setTablePagenation', [ '$compile','axisConst', function($compile,axisConst){
	return {
		controller: ['$scope','axisConst', function($scope,axisConst) {
			$scope.pagination = {
				limit       : axisConst.pagination["limit"],
				maxSize     : axisConst.pagination["maxSize"],
				device      : {}
			};

			_.each(axisConst.pagination.device, function(obj,device){
				$scope.pagination.device[device] = {
						currentPage : axisConst.pagination.device[device]["currentPage"],
						offset      : axisConst.pagination.device[device]["offset"]
				}
			});

			$scope.$watch(function () {
				_.each(axisConst.pagination.device, function(obj,device){
					$scope.pagination.device[device].offset = ($scope.pagination.device[device].currentPage - 1) * $scope.pagination.limit;
				});
			});

		}],
		scope: false,
		replace: true,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			//ページネーション生成
			var template = '<pagination total-items="'+$attrs.setTablePagenation+'_filtered.length" ng-model="pagination.device.'+$attrs.setTablePagenation+'.currentPage" max-size="pagination.maxSize" class="pagination-sm" boundary-links="true" rotate="false" items-per-page="pagination.limit" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>';
			$elm.replaceWith($compile(template)(scope));
		}
	}
}]);



/**
 * テーブル機能 ソート
 */

directives.directive('setSortItems', function() {
	return {
		//scope定義
		controller: ['$scope','axisConst', function($scope,axisConst) {
			$scope.order      = {};
			$scope.sort       = {};
			_.each($scope.table.elem.device_list, function(device){
				$scope.sort[device] = {
					place  : axisConst.sortItems.place['account_id'],
					isDesc : axisConst.sortItems.isDesc[1],
				}
			});
		}],
		restrict: 'A',
		scope:false,
		link: function (scope, $elm, $attrs) {

			scope.orderBy = function (device,place,filterd) {
				scope.order[device] = getOrder(device,place,filterd);
			};

			//ソートの選定
			var getOrder = function(device,place,filterd){

				//現在のscope.sort.placeとクリックしたplaceが同じ場合のみtoggle処理
				var str = (scope.sort[device].isDesc && scope.sort[device].place==place) ? '-'  : '';
				scope.sort[device].isDesc = (scope.sort[device].isDesc && scope.sort[device].place == place) ? true : false;
				scope.sort[device].place  = place;

				return str + scope.sort[device].place;
			};

			//activeクラスの付与
			scope.activeClass = function(device,place){
				if(typeof scope.sort[device] !=='undefined') {
					if(scope.sort[device].place  === place){
						return (!scope.sort[device].isDesc) ? {'sort-asc' : true}: {'sort-desc': true};
					}
				}
			};
		}
	};
});



/**
 * テーブル機能 項目,値でフィルタ
 */
directives.directive('reportFilter', ['axisConst','$timeout','tableFactoryService', function(axisConst,$timeout,tableFactoryService){
	return {
		restrict: 'A',
		scope:false,
		link: function (scope, $elm, $attrs) {

			/**
			 * フィルタ項目生成
			 */
			 scope.setFilterItems = function (data) {
			 	var filterItem = tableFactoryService.getValueByKey(data,'campaign_deliver');
				scope.table.views.filter_items.campaign_deliver = tableFactoryService.objOrder(filterItem)
			 }

			/**
			 * 指標からmin,maxでしぼりこむフィルタ
			 */
			scope.reportFilter = function () {
				if(scope.params.report_format !== 'summary') return false;
				$timeout.cancel(scope.timeid);
				scope.timeid = $timeout(function() {

					var filterd       = [];
					var filter_items  = scope.params.report_filters;
					var target        = angular.copy(scope.master[scope.params.report_format].device[scope.table.settings.device]);
					var item          = [];
					var min           = [];
					var max           = [];

					//条件式生成 指標
					var expression_indicate   = [];
					var expression_media      = [];
					var expression_match_type = [];
					var expression_campaign_deliver = [];

					_.each(filter_items, function(obj,i){
						item[i]       = scope.params.report_filters[i].filter_item;
						min[i]        = scope.params.report_filters[i].filter_min;
						max[i]        = scope.params.report_filters[i].filter_max;

						if(item[i] !==''){
							expression_indicate[i] = '';
							if(obj['filter_min']!=='')                           expression_indicate[i] += 'value[item['+i+']] >= min['+i+']';
							if(obj['filter_min']!=='' && obj['filter_max']!=='') expression_indicate[i] += ' && ';
							if(obj['filter_max']!=='')                           expression_indicate[i] += 'value[item['+i+']] <= max['+i+']';
						}

						_.each(obj['media'], function(value,key){
							if(key != "" && value === true) expression_media.push("value['media_id']  ==  '"+key+"' ")
						});

						_.each(obj['match_type'], function(value,key){
							if(key != "" && value === true) expression_match_type.push("value['match_type'] ==  '"+scope.appConst.filter_match_type_list[key]+"' ")
						});

						if(!_.isEmpty(obj['campaign_deliver'])) expression_campaign_deliver.push("value['campaign_deliver'] ==  '"+obj['campaign_deliver']+"' ")
					});

					if( (expression_indicate.length!==0 && expression_indicate!=='') || expression_media.length!==0 || expression_match_type.length!==0 || expression_campaign_deliver.length!==0){
						expression_indicate   =  (expression_indicate.length!==0 && expression_indicate!='') ? expression_indicate.join().replace(/,/g, ' && ')   : 'true';
						expression_indicate   =  (expression_indicate.slice(-4) == ' && ')                   ? expression_indicate.slice(0, -4)                   : expression_indicate;
						expression_media      =  (expression_media.length!==0)                               ? expression_media.join().replace(/,/g, ' || ')      : 'true';
						expression_match_type =  (expression_match_type.length!==0)                          ? expression_match_type.join().replace(/,/g, ' || ') : 'true';
						expression_campaign_deliver = (expression_campaign_deliver.length!==0)               ? expression_campaign_deliver.join().replace(/,/g, ' || ') : 'true';

						_.each(target, function(value, i) {
							if(value['account_name'] == axisConst.unsorted['account_name']){filterd.push(value)}

							if (eval(expression_indicate) ){
								if (eval(expression_media) ){
									if (eval(expression_match_type) ) {
										if (eval(expression_campaign_deliver) ) filterd.push(value);
									}
								}
							}

						});

						scope.async_data.summary.device[scope.table.settings.device] = filterd;
						$timeout(function() {$('.sum').addClass('hidden')});

					}else{
						$timeout(function() {$('.sum').removeClass('hidden')});
						scope.async_data.summary = angular.copy(scope.master.summary);
					}
				}, 170);
			};

		}
	};
}]);


/**
 * テーブル機能 セル項目の表示、非表示
 */
directives.directive('cellHide', ['tableFactoryService', function(tableFactoryService){
	return {
		//scope定義
		controller: ['$scope','axisConst', function($scope,axisConst) {
			$scope.isShow = {
				basic :{},
			}
			$scope.isShow.basic.all          = true;
			$scope.isShow.basic.account_id   = true;
			$scope.isShow.basic.account_name = true;


			_.each(_.keys($scope.table.elem.report_elem_list), function(key){
				$scope.isShow.basic[key] = true;
			});

			$scope.initCell = function(){
				_.each(_.keys($scope.table.elem.summary_elem_list), function(key){
					$scope.isShow.basic[key] = true;
				});
			}
		}],
		restrict: 'A',
		scope:false,
		link: function (scope, $elm, $attrs) {

			scope.setExtCvColspan = function (e) {
				var ext_elm = $(e.target).data('key').split('_');
				var colspan = tableFactoryService.getExtCvColspan(scope,ext_elm)
				$('.parent_excv_cell_'+ext_elm[2]).attr('colspan',colspan);
				scope.isShow.basic['parent_excv_cell_'+ext_elm[2]] = (colspan == 0) ? false : true;
			};
		}
	};
}]);



/**
 * テーブル機能 データの変更 
 */
directives.directive('updateTables', ['axisConst', function(axisConst){
	return {
		scope: false,
		replace: false,
		restrict: 'AE',
		link: function (scope, $elm, $attrs) {

			//粒度移動
			var granularity_order = angular.copy(axisConst.granularity_order);

			scope.tableActions = {

				returnRoughGranularity : function (summary_type) {
					granularityData.name = 'return_all_selected_' + summary_type;
					scope.submit( true , granularityData);
				},

				moveGranularity : function(e){
					granularityData = {
						name                :$(e.target).get(0).className.split(" ")[0],
						category_big_text   :$(e.target).closest('tr').children('.category_big_name').text(),
						category_middle_text:$(e.target).closest('tr').children('.category_middle_name').text(),
						info :{}
					}

					if(typeof $(e.target).data('info') != 'undefined') {
						var info = _.object(granularity_order , $(e.target).data('info').split(':') );
						_.each(info, function(value,key){
							if(typeof value !== 'undefined') granularityData.info[key] = value;
						});
					}

					scope.submit( true , granularityData);
				},

				//データ削除後、画面切り換え
				deleteTables : function (isShowTables) {
					scope.isShowTables = (typeof isShowTables === 'undefined') ? false : isShowTables;
					if(typeof scope.async_data[scope.params.report_format] !=='undefined'){
						var devices = _.keys(scope.async_data[scope.params.report_format]["device"]); 
						_.each(devices, function(device){
							$(".set-"+scope.params.report_format+"-tables").empty();
						});
					}
					scope.params.report_filters = [{'filter_item':'','filter_min':'','filter_max':''}];
					scope.async_data = angular.copy(scope.master);
					scope.sort       = {};
					scope.message    = '';
					scope.graph      = {};
					$('#chart').empty();
					$('#js-controlPositionArea').css('minHeight','initial');
				}
			}
		}
	}
}]);


/**
 * テーブル機能 画像のリンク切れ処理を行う
 */
directives.directive('hideDeadLink', [function(){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm) {
			$elm.error(function() {
				$(this).hide().closest('a').addClass('no-image');
			});
		}
	}
}]);


/**
 * 日別推移のグラフ
 *
 */
directives.directive('graphDirective', ['axisConst','utilFactoryService','$timeout',function(axisConst,utilFactoryService,$timeout) {
	return {
		controller: ['$scope','axisConst', function($scope,axisConst) {
			$scope.graph = {
				y  : 'imp',
				y2 : 'cost'
			}
			var replace_text_y  = (!_.isUndefined($scope.table)) ? _.propertyOf($scope.table.elem.report_elem_list)($scope.graph.y):'';
			var replace_text_y2 = (!_.isUndefined($scope.table)) ? _.propertyOf($scope.table.elem.report_elem_list)($scope.graph.y2):'';
			$scope.graph_setting = {
				bar: {
					width: {
						ratio: 0.6
					}
				},
				data: {
					json:[],
					types: {
						imp:'line',
						cost:'bar'
					},
					keys: {
						x: 'report_date',
						value: [$scope.graph.y,$scope.graph.y2],
					},
					names: {
						imp: replace_text_y,
						cost: replace_text_y2
					},
					axes: {
						imp: 'y',
						cost:'y2'
					},
					colors :  axisConst.graphColorCode
				},
				color: {
					pattern: ['#CD619B','#1f77b4']
				},
				axis: {
					x: {
						type: 'timeseries',
						tick: {
							multiline: false,
							format: '%Y/%m/%d',
							rotate: 70
						},
						height: 80
					},
					y: {
						label: {
							text:replace_text_y,
							position: 'outer-middle'
						},
						tick: {
							format: function (d) { 
								var format = utilFactoryService.getFormat($scope.graph.y)
								return format['yen'] + d.toLocaleString() + format['percent']; 
							}
						}
					},
					y2: {
						show: true,
						label: {
							text: replace_text_y2,
							position: 'outer-middle'
						},
						tick: {
							format: function (d) { 
								var format = utilFactoryService.getFormat($scope.graph.y2)
								return format['yen'] + d.toLocaleString() + format['percent']; 
							}
						}
					},
				},
				grid: {
					x: {
						show: true
					}
				}
			};
		}],
		restrict: 'A',
		link: function (scope) {

			scope.setGraph = function(graphElm){
				if(scope.params.report_format != 'daily') return false;
				if (!_.isEmpty(graphElm)) {
					scope.graph.y   = graphElm['y']
					scope.graph.y2  = (typeof graphElm['y2'] != 'undefined') ? graphElm['y2'] : '';
				}

				scope.graph_setting.bar.width.ratio    = _actions.getBarWidth();
				scope.graph_setting.data.json          = scope.async_data.daily.graph[scope.table.settings.device];
				var graph_elem_list                    = $.extend(true, {}, scope.table.elem.report_elem_list, scope.table.elem.all_ext_cv_list);
				var replace_text_y                     = _.propertyOf(graph_elem_list)(scope.graph.y);
				var replace_text_y2                    = _.propertyOf(graph_elem_list)(scope.graph.y2);1
				var i = 0
				$.each(scope.graph, function(axis,val){
					scope.graph_setting.data.types[val]        = (i == 0) ? 'line': 'bar';
					scope.graph_setting.data.axes[val]         = axis;
					scope.graph_setting.data.names[val]        = eval('replace_text_' + axis);
					scope.graph_setting.axis[axis].label.text  = eval('replace_text_' + axis);
					if(val.indexOf('ext_') != -1 ) scope.graph_setting.data.colors[val]  = scope.graph_setting.color.pattern[i];
					i++;
				});
				scope.graph_setting.data.keys.value    = [scope.graph.y,scope.graph.y2];
				chart                                  = c3.generate(scope.graph_setting);
				$timeout(function(){_actions.setDisabled()});

			}

			var _actions = {
				getBarWidth : function(){
					var ratio = (scope.async_data.daily.graph[scope.table.settings.device].length  < 6) ? '0.' + scope.async_data.daily.graph[scope.table.settings.device].length :0.6;
					return Number(ratio);
				},
				setDisabled : function(){
					var y  = $('#graph_y').val();
					var y2 = $('#graph_y2').val();
					$('.graph-select option').prop('disabled', false);
					$('#graph_y option:eq('+y2+')').prop('disabled', true);
					$('#graph_y2 option:eq('+y+')').prop('disabled', true);
				}
			}
		}
	};
}]);


/**
 * 追従filterを制御
 *
 * 注:scrollイベントではscopeをbindさせない
 */
directives.directive('controlPositionFixed', ['$timeout',function($timeout){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope) {
			var $elm             = $('#js-offsetPosition');
			var isPositionBottom = false;
			var breakPoint       = 0;
			var timer            = false;

			scope.initControlPositionFixed = function(){
				$elm.hasClass('fixed-base') ? $('.js-ac-nav').on('shown.bs.collapse',function(){_actions.setElmHeight()}):_actions.setElmHeight();
			}

			scope.actions = {
				toggleAccordion : function(){
					_actions.toggleAccordion('click');
				},
				checkPositionBottom : function(){
					_actions.checkPositionBottom();
				}
			}

			var _actions = {
				changePosition : function(position){
					(position == 'fixed') ? $elm.delay(20).addClass('fixed-base fadeInDown') : $elm.delay(20).removeClass('fixed-base fadeInDown');
				},

				toggleAccordion : function(event,status){
					if($elm.hasClass('fixed-base')  && event == 'click') status = 'hide' 
					$('.js-ac-nav').collapse(status);
				},

				checkPositionBottom : function(){
					var scrollHeight   = $(document).height();
					var scrollPosition = $(window).height() + $(window).scrollTop();
					if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
						isPositionBottom = true;
					}
				},
				setElmHeight : function(){
					$timeout(function() {
						var $controlArea = $('#js-controlPositionArea');
						var elmHeight    = $controlArea.outerHeight();
						breakPoint       = $elm.offset().top + elmHeight;
						$controlArea.css('minHeight',elmHeight);
					});
				}
			}

			$(window).scroll(function () {
				if (timer) clearTimeout(timer);
				timer = setTimeout(function() {
					if(!scope.isShowTables) return false;
					if ($(this).scrollTop() > breakPoint) {
						if(isPositionBottom) return false
						_actions.changePosition('fixed');
						_actions.toggleAccordion('scroll','hide')

					} else {
						_actions.changePosition('normal');
						_actions.toggleAccordion('scroll','show');
						isPositionBottom = false;
					}
				}, 170);
			});
		}
	}
}]);

