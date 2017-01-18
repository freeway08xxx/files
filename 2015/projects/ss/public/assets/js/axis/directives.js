/* Directives */
var directives = angular.module("axis.directives", []);

directives.directive("ngScrollable", function () {
	return {
		restrict: "A",
		link: function (scope, elements) {
			var element = elements[0];
			var content = document.getElementById("templateList"); // スクロールさせたいコンテンツ。適宜セレクタは書き換えるべし。

			scope.$watch(
				function () { return element.clientHeight; },
				function () { content.style.height = element.clientHeight + "px"; }
			);
		}
	};
});



/**
 * テーブル生成 
 */
directives.directive('setTables', [ '$compile','axisConst', function($compile,axisConst){
	return {
		scope: false,
		replace: false,
		restrict: 'AE',
		link: function (scope, $elm, $attrs) {
			// scope.$watch('params.report_type', function(newVal){
			// 	if(newVal){


			// 	}
			// });


			//テーブルセル生成
			scope.setCell = function(){

				//サマリ
				_.each(scope.async_data.cell_name, function(item){

					var nameFilter = _.pick(axisConst.summayTableCell, item);
					var template = '<th class="'+item+'"><button ng-click="sort.isDesc=!sort.isDesc;orderBy('+"'"+item+"'"+')" class="sort '+item+'" ng-class="activeClass('+"'"+item+"'"+')">'+nameFilter[item]+'</button></th>';

						var new_elm = $compile(template)(scope);
						$elm.append(new_elm);
				});

				//デイリ
				_.each(scope.async_data.daily, function(date){

					var template = '<th class="'+date+'"><button ng-click="sort.isDesc=!sort.isDesc;orderBy('+"'"+date+"'"+')" class="sort '+date+'" ng-class="activeClass('+"'"+date+"'"+')">'+date+'</button></th>';

						var new_elm = $compile(template)(scope);
						$elm.append(new_elm);
				});






			}
		}
	}
}])



/**
 * テーブル機能 ソート ページング
 */

directives.directive('sortItems', function() {
	return {
		//scope定義
		controller: ['$scope','axisConst', function($scope,axisConst) {
			/**
			 *  ソート
			 */
			$scope.sort   = {
				place       : axisConst.sortItems.place['account_id'],
				isDesc      : axisConst.sortItems.isDesc[1]
			};

			/**
			 *  ページング
			 */
			$scope.pagination = {
				currentPage : axisConst.pagination["currentPage"],
				numPages    : axisConst.pagination["numPages"],
				limit       : axisConst.pagination["limit"],
				maxSize     : axisConst.pagination["maxSize"],
				offset      : axisConst.pagination["offset"]
			};

			$scope.$watch(function () {
				$scope.pagination.offset = ($scope.pagination.currentPage - 1) * $scope.pagination.limit;
			});
		}],
		restrict: 'A',
		scope:false,
		link: function (scope) {

			scope.orderBy = function (place) {
				scope.order = getOrder(place);

				_.each(scope.async_data.summary, function(val){
					val[place] = (Number.isNaN(parseInt(val[place]))) ? val[place]:parseInt(val[place]);
				});

			};

			//ソートの選定
			var getOrder = function(place){
				//現在のscope.sort.placeとクリックしたplaceが同じ場合のみtoggle処理
				var str = (scope.sort.isDesc && scope.sort.place==place) ? '-'  : '';
				scope.sort.isDesc = (scope.sort.isDesc && scope.sort.place == place) ? true : false;
				scope.sort.place  = place;

				return str + scope.sort.place;
			};

			//activeクラスの付与
			scope.activeClass = function(place){
				if(scope.sort.place  === place){
					return (!scope.sort.isDesc) ? {'sort-asc' : true}: {'sort-desc': true};
				}
			};
		}
	};
});






