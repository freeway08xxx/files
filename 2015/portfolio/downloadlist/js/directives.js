var dir = angular.module('downloadlist.directives', []);

//ソート処理
dir.directive('sortItems', function() {
	return {
		restrict: 'A',
		scope:false,
		link: function (scope) {

			scope.orderBy = function (place) {
				scope.order = getOrder(place);
			};

			//ソートの選定
			var getOrder = function(place){
				//現在のscope.sort.placeとクリックしたplaceが同じ場合のみtoggle処理
				var str                      = (scope.reportCtrl.sort.isDesc && scope.reportCtrl.sort.place==place) ? '-'  : ''   ;
				scope.reportCtrl.sort.isDesc = (scope.reportCtrl.sort.isDesc && scope.reportCtrl.sort.place==place) ? true : false;
				scope.reportCtrl.sort.place  = place;
				return str + scope.reportCtrl.sort.place;
			};

			//クラスの付与
			scope.activeClass = function(place){
				if(scope.reportCtrl.sort.place  === place){
					if(!scope.reportCtrl.sort.isDesc) return {'sort-asc' : true};
					if(scope.reportCtrl.sort.isDesc)  return {'sort-desc': true};
				}
			};
		}
	};
});


//active切り換え
dir.directive('changeActivate', function() {
	return {
		restrict: 'A',
		scope:false,
		link: function (scope) {

			scope.formInactive = function () {
				$("#search").removeClass('active')
			};

			scope.formActive = function () {
				$("#search").addClass('active')
				scope.reportCtrl.search = "";
			}

		}
	};
});

