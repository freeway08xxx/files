/**
 * テーブルの生成、管理 
 */
directives.directive('setTable', ['$compile','dataSharedService','tableFactoryService', 'paramsFactoryService','quickManageConst','quickManageRestApi',
	function($compile,dataSharedService,tableFactoryService,paramsFactoryService,quickManageConst,quickManageRestApi){
	return {
		/**
		 * レポート作成画面 出力結果部分 コントローラー
		 */
		controller: ['$scope', function($scope) {
			$scope.table = {
				models  : {},
				elm: {
					summary_elm_list :{}, 
					report_elm_list  :{},
					all_elm_list     :{}
				},
				pagination : {
					limit       : quickManageConst.pagination["limit"],
					maxSize     : quickManageConst.pagination["maxSize"],
					currentPage : quickManageConst.pagination["currentPage"],
					offset      : quickManageConst.pagination["offset"]
				},
				order : quickManageConst.sortItems.place['cost'],
				sort  : {
					place  : quickManageConst.sortItems.place['cost'],
					isDesc : quickManageConst.sortItems.isDesc[0],
				},
				actions : {
					set : function (template) {
						$('.table-wrap').append($compile(template)($scope));
						window.scrollTo(0,0);
					},
					delete : function () {
						$scope.settings.is_report_display = false;
						$('.table-wrap').empty();
						$scope.getTemplateList();
					},
					move : function(e){
						var moveParams = (typeof $(e.target).data('move_params') != 'undefined') ? $(e.target).data('move_params'):{};
						var data = paramsFactoryService.getParams($scope,null,moveParams);
						$scope.table.actions.post(data);
					},
					returnBureau : function(e){
						var moveParams = {'summary_type':'bureau','search_user_name':'','bureau_id_list':[]};
						var data = paramsFactoryService.getParams($scope,null,moveParams);
						$scope.table.actions.post(data);
					},
					post : function (data) {
						quickManageRestApi.getReportdata(data).then(function(res) {
							if(!res.error){
								$scope.table.actions.delete();
								dataSharedService.data_list.set(res,$scope);
							}
						});
					},
				}
			};

			$scope.$watch(function () {
				$scope.table.pagination.offset = ($scope.table.pagination.currentPage - 1) * $scope.table.pagination.limit;
			});

			//データ変更
			$scope.$on('changedData', function() {
				$scope.settings.is_report_display = true;
				$scope.master         = dataSharedService.data_list.get();
				$scope.table.models   = angular.copy($scope.master.data);
				$scope.table.elm = {
					summary_elm_list : _.omit($scope.master.elm.summary_elm_list,'forecast'),
					report_elm_list  : $scope.master.elm.report_elm_list,
					all_elm_list     : tableFactoryService.multiExtend([$scope.master.elm.summary_elm_list,$scope.master.elm.report_elm_list])
				};

				$scope.table.actions.create();
			});
		}],
		scope: false,
		replace: false,
		restrict: 'E',
		link: function (scope, $elm, $attrs) {

			//テーブル生成 
			scope.table.actions.create = function(){
				var template  = '<table class="table-hover '+scope.params.report_type+'"><thead set-sort-items><tr set-table-heads></tr></thead>';
					template += '<tbody set-table-bodys></tbody></table>';
				scope.table.actions.set(template);
			}
		}
	}
}]);


/**
 * テーブルヘッド生成 
 */
directives.directive('setTableHeads', ['$compile','quickManageConst','tableFactoryService', function($compile,quickManageConst,tableFactoryService){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			var elm_list    = {};
			scope.date_list = {};

			if(scope.params.report_type === 'summary'){
				elm_list = scope.table.elm.all_elm_list;
			}else{
				scope.date_list = (scope.params.report_type === 'term_compare') ? tableFactoryService.getDateDiffList(scope.table.models[0]['term_compare']) : tableFactoryService.getDateList(scope.table.models[0]['daily']);
				elm_list = tableFactoryService.multiExtend([scope.table.elm.summary_elm_list , scope.date_list]);
				elm_list = tableFactoryService.addBetweenObj( elm_list,'all',{indicate:'指標'});
			}

			//テーブルヘッドセル生成
			_.each(elm_list, function(value,key){
				value           = tableFactoryService.getDayElm(value);
				var sortClass   = (_.has(_.omit(scope.table.elm.summary_elm_list ,'all','indicate') , key) ||  scope.params.report_type === 'summary') ? 'sort' : 'sort no_sort';
				var tmplSetting = tableFactoryService.getTmplSetting(key);
				var template    = '<th class="'+key+' '+tmplSetting.align+'">';
				var retunLink   = (key == 'bureau_name') ? '<a ng-show="params.summary_type!='+"'"+'all'+"'"+' && params.summary_type!='+"'"+'bureau'+"'"+'" class="backlink bureau transition" ng-click="table.actions.returnBureau();">局一覧</a>': '' ;
				template       += '<button ng-click="table.sort.isDesc=!table.sort.isDesc;orderBy('+"'"+key+"'"+')" class="'+sortClass+'" ng-class="activeClass('+"'"+key+"'"+')">'+value+'</button>'+retunLink+'</th>';

				$elm.append($compile(template)(scope));
			});

		}
	}
}]);


/**
 * テーブルボディ生成 
 */
directives.directive('setTableBodys', [ '$compile','quickManageConst','tableFactoryService','quickManageConst', function($compile,quickManageConst,tableFactoryService,quickManageConst){
	return {
		scope: false,
		replace: false,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {
			/**
			 * テーブルボディ生成 指標が横のタイプ
			 */
			if(scope.params.report_type === 'summary'){
				var template = '<tr ng-repeat="summary in filtered = table.models[0].summary | orderBy:table.order | customOrderBy | offset:table.pagination.offset | limitTo:table.pagination.limit" class="{{summary.bureau_name | addSumClass}}">';
				var isFirstOption = true;
				_.each(scope.table.elm.all_elm_list, function(value,key){
					var tmplSetting = tableFactoryService.getTmplSetting(key);
					switch (key){
						//icon表示
						case 'media_name':
							var replaceSum = (scope.params.summary_type === 'all' && isFirstOption) ? '| replaceSum:summary.bureau_name':'';
							template += '<td class="'+tmplSetting.align+'"><span set-media-icon="{{summary.media_id}}"></span>  <span ng-hide="summary.media_id">{{summary.media_id '+replaceSum+'}}</span></td>';
							isFirstOption = false; break;
						//粒度
						case 'bureau_name':
							template += '<td class="'+tmplSetting.align+'"><a data-move_params='+ '{"summary_type":"user","search_user_name":"","bureau_id_list":"{{summary.bureau_id}}"}'+' ng-click="table.actions.move($event)">'
							template += '{{summary.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'" }}</a></td>'; break;
						case 'user_name':
							template += '<td class="'+tmplSetting.align+'"><a data-move_params='+ '{"summary_type":"client","search_user_name":"{{summary.user_name}}","bureau_id_list":""}'+' ng-click="table.actions.move($event)">'
							template += '{{summary.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'" }}</a></td>'; break;

						//'合計'を挿入
						case 'company_name':
							var replaceSum = (scope.params.summary_type === 'company') ? '| replaceSum:summary.bureau_name':'';
							template += '<td class="'+key+' '+tmplSetting.align+'">{{summary.'+key+' '+replaceSum+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;

						//オプション
						case 'customer_class_name':
						case 'business_type_name':
						case 'product_name':
						case 'device_name':
							var replaceSum = (scope.params.summary_type === 'all' && isFirstOption) ? '| replaceSum:summary.bureau_name':'';
							template += '<td class="'+key+' '+replaceSum+' '+tmplSetting.align+'">{{summary.'+key+' '+tmplSetting.numberFilter+' '+replaceSum+' | format:"'+key+'"}}</td>';
							isFirstOption = false; break;
						default:
							template += '<td class="'+key+' '+tmplSetting.align+'">{{summary.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;
					}
				});
				template += '</tr>';
			}

			/**
			 * テーブルボディ生成 指標が縦のタイプ
			 */
			else if(scope.params.report_type === 'daily' || scope.params.report_type === 'term_compare'){
				var report_type = (scope.params.report_type === 'daily')  ? 'daily' : 'term_compare';
				var elm_list    = _.omit(scope.table.elm.all_elm_list,'all' ,'forecast');
				var rowspan     = Object.keys(scope.table.elm.report_elm_list).length + 1;
				var isFirstOption = true;

				if(typeof scope.table.models[0] != 'undefined'){
					var template = '<tr class="{{model.bureau_name | addSumClass}}" ng-repeat-start="model in filtered = table.models[0].' +report_type+ '.data | orderBy:table.order | customOrderBy | offset:table.pagination.offset | limitTo:table.pagination.limit">';


					_.each(elm_list, function(value,key){
						var tmplSetting = tableFactoryService.getTmplSetting(key);

						switch (key){

							//icon表示
							case 'media_name':
							var replaceSum = (scope.params.summary_type === 'all' && isFirstOption) ? '| replaceSum:model.bureau_name':'';
							template += '<td rowspan="'+rowspan+'" class="'+key+' '+tmplSetting.align+'"><span set-media-icon="{{model.media_id}}"></span> <span ng-hide="model.media_id">{{model.media_id '+replaceSum+'}}</span></td>'; 
							isFirstOption = false; break;

							//粒度
							case 'bureau_name':
								template += '<td rowspan="'+rowspan+'" class="'+tmplSetting.align+'"><a data-move_params='+ '{"summary_type":"user","search_user_name":"","bureau_id_list":"{{model.bureau_id}}"}'+' ng-click="table.actions.move($event)">'
								template += '{{model.bureau_name '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;

							case 'user_name':
								template += '<td rowspan="'+rowspan+'" class="'+tmplSetting.align+'"><a data-move_params='+ '{"summary_type":"client","search_user_name":"{{model.user_name}}","bureau_id_list":""}'+' ng-click="table.actions.move($event)">'
								template += '{{model.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;

							//サマリタイプ 'company' の時に'合計'を挿入
							case 'company_name':
								var replaceSum = (scope.params.summary_type === 'company') ? '| replaceSum:model.bureau_name':'';
								template += '<td rowspan="'+rowspan+'" class="'+key+' '+tmplSetting.align+'">{{model.'+key+' '+replaceSum+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;

							//オプション
							case 'customer_class_name':
							case 'business_type_name':
							case 'product_name':
							case 'device_name':
								var replaceSum = (scope.params.summary_type === 'all' && isFirstOption) ? '| replaceSum:model.bureau_name':'';
								template += '<td rowspan="'+rowspan+'" class="'+key+' '+tmplSetting.align+'">{{model.'+key+' '+tmplSetting.numberFilter+' '+replaceSum+' | format:"'+key+'"}}</td>'; 
								isFirstOption = false; break;

							default:
							//指標
							if(_.has(scope.table.elm.report_elm_list,key)){
								template += '<tr class="'+tmplSetting.align+' {{model.bureau_name | addSumClass}}" set-table-'+report_type+'-cell="'+key+'">'
								template += '<th class="indicate">'+value+'</th>'
								template += '<td>{{model.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td></tr>';break;
							}
							else{
								template += '<td rowspan="'+rowspan+'" class="'+tmplSetting.align+' '+key+'">{{model.'+key+' '+tmplSetting.numberFilter+'| format:"'+key+'"}}</td>'; break;
							}
						}
					});
					template += '<tr ng-repeat-end></tr>'; 
 				}
			}
			$elm.append($compile(template)(scope));
		}
	}
}]);


/**
 * 日付セル 自動生成
 */
directives.directive('setTableDailyCell', ['$compile', 'tableFactoryService',function($compile,tableFactoryService){
	return {
		scope: false,
		replace: true,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {

			//テーブルセル生成
			var indicate = $attrs.setTableDailyCell;
			_.each(scope.date_list, function(value,date){
				var tmplSetting = tableFactoryService.getTmplSetting(indicate);
				var template  = '<td class="'+indicate+' '+tmplSetting.align+'">';
				    template += '{{model.'+date+'.'+indicate+' '+tmplSetting.numberFilter+'| format:"'+indicate+'"}}</td>';

				$elm.append($compile(template)(scope));
			})
		}
	}
}]);


/**
 * 期間比較セル 自動生成
 */
directives.directive('setTableTermCompareCell', ['$compile', 'tableFactoryService',function($compile,tableFactoryService){
	return {
		scope: false,
		replace: true,
		restrict: 'A',
		link: function (scope, $elm, $attrs) {

			var indicate = $attrs.setTableTermCompareCell;

			//テーブルセル生成
			_.each(scope.date_list, function(val,key){
				var tmplSetting = tableFactoryService.getTmplSetting(indicate);

				if(key.indexOf('diff') >= 0){
					var percent      = 'model.'+key+'.percent.'+indicate;
					var realnumber   = 'model.'+key+'.realnumber.'+indicate;
					var template   = '<td class="'+indicate+'">{{'+realnumber+' '+tmplSetting.numberFilter+'| format:"'+indicate+'"}}';
					template       += '<span class="percent {{'+realnumber+' | label_class}}">{{'+percent+' | number:0}}%</span></td>';
				}else{
					var template   = '<td class="'+indicate+'">{{model.'+key+'.'+indicate+' '+tmplSetting.numberFilter+'| format:"'+indicate+'"}}</td>';
				}

				$elm.append($compile(template)(scope));
			})

		}
	}
}]);



/**
 * テーブル機能 ソート
 */
directives.directive('setSortItems', function() {
	return {
		restrict: 'A',
		scope:false,
		link: function (scope, $elm, $attrs) {

			scope.orderBy = function (place) {
				scope.table.order = getOrder(place);
			};

			//ソートの選定
			var getOrder = function(place){
				//現在のscope.sort.placeとクリックしたplaceが同じ場合のみtoggle処理
				var str = (scope.table.sort.isDesc && scope.table.sort.place == place) ? '-' : '';
				scope.table.sort.isDesc = (scope.table.sort.isDesc && scope.table.sort.place == place) ? true : false;
				scope.table.sort.place  = place;
				return str + scope.table.sort.place;
			};

			//activeクラスの付与
			scope.activeClass = function(place){
				if(typeof scope.table.sort !=='undefined') {
					if(scope.table.sort.place === place){
						return (!scope.table.sort.isDesc) ? {'sort-asc' : true} : {'sort-desc': true};
					}
				}
			};
		}
	};
});

