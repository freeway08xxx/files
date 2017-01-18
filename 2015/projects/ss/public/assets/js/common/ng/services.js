/* Services */

var app = angular.module( 'ss.services', [] );

app.service('ssGlobalNavService', ['$q', '$http', function($q, $http) {
	var deferred = null;
	return {
		getNav: function () {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();

			$http({
				url: '/sem/new/api/nav/globalNav',
				params: null,
				method: 'GET'
			}).success(function(res) {
				//resolve
				deferred.resolve(res);
			})
			.error(function (data, status, headers, config) {
				if(status == 404){
					alert("グローバルナビ取得エラー");
				}
			});

			return deferred.promise;
		},
		isDropdownMenu: function (item) {
			return item.hasOwnProperty('dropdown');
		}
	};
}]);


/* ModalService */
app.factory('ssModal', function($modal) {
	return {
		loading : function(message) {
			var modalInstance = $modal.open({
				templateUrl: '/sem/new/assets/template/modal/loading.html',
				controller:'ssModalCtrl',
				backdrop: 'static',
				resolve: {
					ssModalData: function(){
						return {
							message: message
						};
					}
				}
			});
			return modalInstance;
		},
		confirm : function(message) {
			var modalInstance = $modal.open({
				templateUrl: '/sem/new/assets/template/modal/confirm.html',
				controller:'ssModalCtrl',
				backdrop: 'static',
				resolve: {
					ssModalData: function(){
						return {
							message: message
						};
					}
				}
			});
			return modalInstance;

		},
		error : function(message) {
			var modalInstance = $modal.open({
				templateUrl: '/sem/new/assets/template/modal/error.html',
				controller:'ssModalCtrl',
				backdrop: 'static',
				resolve: {
					ssModalData: function(){
						return {
							message: message
						};
					}
				}
			});
			return modalInstance;
		},
		custom : function(option) {
			var modalInstance = $modal.open(option);
			return modalInstance;
		}
	};
});

/* modalController */
app.controller('ssModalCtrl', ['$scope', '$document', '$modalInstance', 'ssModalData', function($scope, $document, $modalInstance, ssModalData) {
	if ('message' in ssModalData){
		$scope.message = ssModalData.message;
	}
	if ('custom' in ssModalData){
		for(k in ssModalData.custom) {
			$scope[k] = ssModalData.custom[k];
		}
	}
	$scope.ok = function () {
		$modalInstance.close();
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};

	var enterClick = function(event) {
		if (event.which === 13) {
			$modalInstance.close();
		}
	}

	$document.bind('keydown', enterClick);
	$scope.$on('$destroy', function() {
		$document.unbind('keydown', enterClick);
	});
}]);

/** 汎用UtilFunctions */
app.service('ssUtils',
['$location',
function ($location) {
	var _this = this;

	_this.getLocationPath = function () {
		return $location.path();
	};
	_this.getLocationParams = function () {
		return $location.search();
	};

	_this.setLocationParams = function (name, val) {
		$location.search(name, val);
	};

}]);
