
/**
 * ss-client-combobox
 */

(function () {
  //"use strict";

	var module = angular.module('ss.module.client-combobox', []);
	module.constant('ssClientComboboxConfig', {
		isOutInput : true,
		bsNameClear: 'ss_combobox_clear',
		client : {
			defaultId : '',
			name: 'ssClient',
			bsName: 'ss_combobox_client_change',
		},
		account : {
			defaultId : '',
			isView : true,
			name: 'ssAccount[]',
			bsName: 'ss_combobox_account_change',
		},
		categoryGenre : {
			isView: false,
			name: 'ssCategoryGenre',
			bsName: 'ss_combobox_category_genre_change'
		},
		dimension: {
			isView: false,
			name: 'ssDimension',
			bsName: 'ss_combobox_dimension_change'
		}
	});

	module.directive('ssClientCombobox',[ function(){
		return {
			scope: true,
			restrict: 'AE',
			replace: true,
			require:'ngModel',
			templateUrl: '/sem/new/assets/template/module/client-combobox.html',
			controller:'ssClientComboboxCtrl'
		};
	}]);

	module.controller('ssClientComboboxCtrl', ['$rootScope', '$scope', '$attrs', '$filter', '$timeout', 'ssClientComboboxConfig', 'ssClientComboboxService',
			function($rootScope, $scope, $attrs, $filter, $timeout, ssClientComboboxConfig, ssClientComboboxService) {
		// 設定値
		$scope.config = $.extend(true, {}, ssClientComboboxConfig);
		if ($attrs.ssClientCombobox) {
			// 設定値拡張
			$.extend(true, $scope.config, $scope.$eval($attrs.ssClientCombobox));
			if('registerApi' in $scope.config){
				var publicApi = {
					setModels : function(models){
						if('client' in models){
							$scope.$watch('client.master',function(){
								if(models.client && $scope.client.master && $scope.client.master.length > 0){
									var clientId = models.client;
									var default_clients = $filter('filter')($scope.client.master, {id:clientId}, true);
									if(default_clients && default_clients.length > 0){
										$scope.moduleModel.client = default_clients[0];
										$scope.ssComboboxChangeClient();
									}
								}
							});
						}
						if('accounts' in models){
							$scope.$watch(function(){
								return [models.accounts,$scope.accounts.master]
							},function(){
								if(models.accounts && $scope.accounts.master && $scope.accounts.master.length > 0){
									for(k in models.accounts){
										var value = models.accounts[k];
										var filterAccount = $filter('filter')($scope.accounts.master, {media_id:value.media_id,account_id:value.account_id}, true);
										if(filterAccount && filterAccount.length > 0){
											filterAccount[0].selected = true;
											$scope.changeAccount();
											delete models.accounts[k];
										}
									}
								}
							},true);
						}
						if('categoryGenre' in models){
							$scope.$watch('categorygenres.master',function(){
								if(models.categoryGenre && $scope.categorygenres && $scope.categorygenres.master && $scope.categorygenres.master.length > 0){
									var categoryGenreId = models.categoryGenre;
									var default_categoryGenre = $filter('filter')($scope.categorygenres.master, {id:categoryGenreId}, true);
									if(default_categoryGenre && default_categoryGenre.length > 0){
										$scope.moduleModel.categoryGenre = default_categoryGenre[0];
										$scope.changeCategoryGenre();
									}
								}
							});
						}
					},
					clearModelsAll: function() {
						initializeData.client(true);	//クライアントクリア(masterは残す)
						initializeData.account();		//アカウントクリア
						initializeData.categoryGenre();	//カテゴリジャンルクリア
						initializeData.dimension();		//ディメンションクリア
						outInputHidden(true);			//HTML吐き出し(クリアフラグ)
						$rootScope.$broadcast($scope.config.bsNameClear);
					}
				};
				$scope.config.registerApi(publicApi);
			}
		}

		// models情報
		$scope.moduleModel = $scope.$eval($attrs.ngModel);

		var initializeData = {
			/* クライアント初期化 */
			client : function(modelOnly) {
				$scope.moduleModel.client = null;

				if(!modelOnly){
					$scope.client = {
						master : [],
					};
				}
			},

			/* アカウント初期化 */
			account : function() {
				$scope.moduleModel.accounts = [];

				$scope.accounts = {
					master : {},
					countMax : 0,
					countSelected : 0,
				};

				//表示する一覧の設定
				$scope.selectAccountViewList = [];
				$scope.selectedAccountViewList = [];
			},

			/*カテゴリジャンル */
			categoryGenre : function() {
				$scope.moduleModel.categoryGenre = null;

				$scope.categorygenres = {
					master : [],
				};
			},

			dimension: function() {
				$scope.moduleModel.dimension = null;

				$scope.dimensions = {
					master : [],
				};
			}
		};


		//クライアント変更->アカウント検索
		$scope.ssComboboxChangeClient = function() {
			if (!('client' in $scope.moduleModel)) return false;
			if ($scope.config.account.isView){
				//アカウント情報は初期化
				initializeData.account();
				//アカウント検索
				ssClientComboboxService.account.findByClientId($scope.moduleModel.client.id, $scope.config.focusMediaIds).then(function(accounts){
					for(k in accounts){
						var value = accounts[k];
						value.selected = false;
						if(value.media_id == 1){	//Yahoo
							value.icon = '/sem/new/assets/img/common/media_icon_mini_yahoo.png' ;
						}else if(value.media_id == 2){	//Google
							value.icon =  '/sem/new/assets/img/common/media_icon_mini_google.png' ;
						}else if(value.media_id == 3){	//YDN(IM)
							value.icon =  '/sem/new/assets/img/common/media_icon_mini_ydn.png' ;
						}else if(value.media_id == 17){	//D2C
							//value.icon =  '/sem/new/assets/img/common/media_icon_mini_ydn.png' ;
						}
	    				value.media_id = parseInt(value.media_id);
					}
					//情報保存
					$scope.accounts.master = accounts;
					$scope.changeAccountSelectQuery();
					$scope.changeAccountSelectedQuery();
					$scope.accounts.countMax = accounts.length;
				});
			}

			if ($scope.config.categoryGenre.isView){
				//カテゴリージャンル初期化
				initializeData.categoryGenre();
				//アカウント検索
				ssClientComboboxService.categoryCenre.getCategoryGenre($scope.moduleModel.client.id).then(function(categoryGenres){
					//情報保存
					$scope.categorygenres.master = categoryGenres;
				});
			}

			if ($scope.config.dimension.isView){
				//カテゴリージャンル初期化
				initializeData.dimension();
				//アカウント検索
				ssClientComboboxService.dimension.findByClientId($scope.moduleModel.client.id).then(function(dimensions){
					//情報保存
					$scope.dimensions.master = dimensions;
				});
			}

			//DOMへ書き出し
			outInputHidden();
			//通知
			$rootScope.$broadcast($scope.config.client.bsName);
		};


		/**
		 * メモ
		 * Shift押しながら押した時にオブジェクトを保持しておき、
		 * 次にShift押しながら押した時に以前のオブジェクトとの間を選択として判定する
		 */
		var isShiftLock = false;
		var hoverAccountDatas = [];
		$scope.clickAccount = function(event,account) {
			if(event && event.shiftKey){
				if(isShiftLock){
					$scope.clickAccount(null,account);
					return false;
				}
				isShiftLock = true;
				$scope.addHoverAccount(event,account);
				//shift押し初回
			}else{
				account.selected = toggleFlg(account.selected);

				angular.forEach(hoverAccountDatas, function(obj, key) {
					if( obj !== account){
						obj.selected = toggleFlg(obj.selected);
					}
				});

				isShiftLock = false;
				hoverAccountDatas = [];
				$scope.changeAccount();
			}
		};


		$scope.addHoverAccount = function(event,account) {
			if(isShiftLock && event && event.shiftKey){
				$(event.target).addClass("account-select-hover");
				hoverAccountDatas.push(account);
			}
		};

		//全選択(選択前->選択済み)
		$scope.clickAccountAll = function(){
			arr = $filter('ssClientComboboxSelectFilter')($scope.accounts.master,$scope.ssClientComboboxSelectText);
			angular.forEach(arr, function(account, key) {
				account.selected = true;
			});
			$scope.ssClientComboboxSelectText = '';
			$scope.ssClientComboboxSelectedText = '';
			$scope.changeAccount();
		};

		//全選択(選択済み->選択前)
		$scope.clickReleaseAccountAll = function(){
			arr = $filter('ssClientComboboxSelectedFilter')($scope.accounts.master,$scope.ssClientComboboxSelectedText);
			angular.forEach(arr, function(account, key) {
				account.selected = false;
			});
			$scope.ssClientComboboxSelectText = '';
			$scope.ssClientComboboxSelectedText = '';
			$scope.changeAccount();
		};


		//選択したアカウントをmodelに保持し、通知
		$scope.changeAccount = function() {
			$scope.moduleModel.accounts = $.extend(true, [], $filter('filter')($scope.accounts.master, {selected:true}));
			$scope.accounts.countSelected = $scope.moduleModel.accounts.length;

			$scope.changeAccountSelectQuery();
			$scope.changeAccountSelectedQuery();

			outInputHidden();

			$rootScope.$broadcast($scope.config.account.bsName);
		};


		$scope.changeAccountSelectQuery = function() {
			$scope.selectAccountViewList = $filter('ssClientComboboxSelectFilter')($scope.accounts.master,$scope.ssClientComboboxSelectText);
		};

		$scope.changeAccountSelectedQuery = function() {
			$scope.selectedAccountViewList = $filter('ssClientComboboxSelectedFilter')($scope.accounts.master,$scope.ssClientComboboxSelectedText);
		};

		//カテゴリージャンル変更通知
		$scope.changeCategoryGenre = function() {
			outInputHidden();
			$rootScope.$broadcast($scope.config.categoryGenre.bsName);
		};

		//ディメンション変更通知
		$scope.changeDimension = function() {
			outInputHidden();
			$rootScope.$broadcast($scope.config.dimension.bsName);
		};

		var outInputHidden = function(isClear) {
			if($scope.config.isOutInput){
				var outInputHiddenHtml = '';

				if($scope.moduleModel.client){
					outInputHiddenHtml += '<input type="hidden" name="'+$scope.config.client.name+'" value="'+$scope.moduleModel.client.id+'">';
				}
				if($scope.moduleModel.accounts){
					angular.forEach($scope.moduleModel.accounts, function (val, key) {
						outInputHiddenHtml += '<input type="hidden" name="'+$scope.config.account.name+'" value=\''+JSON.stringify(val)+'\'>';
					});
				}
				if($scope.moduleModel.categoryGenre){
					outInputHiddenHtml += '<input type="hidden" name="'+$scope.config.categoryGenre.name+'" value=\''+JSON.stringify($scope.moduleModel.categoryGenre)+'\'>';
				}
				if($scope.moduleModel.dimension){
					outInputHiddenHtml += '<input type="hidden" name="'+$scope.config.dimension.name+'" value=\''+JSON.stringify($scope.moduleModel.dimension)+'\'>';
				}

				if(!isClear){
					//追加
					$("#ssClientComboboxHiddens").html(outInputHiddenHtml);
				}else{
					//空にする
					$("#ssClientComboboxHiddens").html('');
				}

			}
		};

		//初回設定
		initializeData.client();
		//クライアント情報取得
		ssClientComboboxService.client.getAll().then(function(clients){
			$scope.client.master = clients;
		});
		if($scope.config.account.isView){
			initializeData.account();
		}

	}]);


	// ==========================================================
	// 以下 Service
	// ==========================================================
	module.service('ssClientComboboxService', ['$q', '$http', 'ssClientComboboxClient', 'ssClientComboboxAccount', 'ssClientComboboxCategoryGenre', 'ssClientComboboxDimension',
			function($q, $http, ssClientComboboxClient, ssClientComboboxAccount, ssClientComboboxCategoryGenre, ssClientComboboxDimension) {
		return {
			client: ssClientComboboxClient,
			account: ssClientComboboxAccount,
			categoryCenre : ssClientComboboxCategoryGenre,
			dimension: ssClientComboboxDimension
		};
	}]);

	module.service('ssClientComboboxClient', ['$q', '$http', function($q, $http) {
		var _this = this;
		_this.clients = null;
		return {
			getAll : function() {
				var deferred = $q.defer();
				if(_this.clients) {
					deferred.resolve(_this.clients);
				}else{
					$http({
						url: '/sem/new/api/client/all',
						method: 'GET'
					}).success(function(res) {
						angular.forEach(res.clients, function (val, key) {
							val.display_name = val.company_name;
							if (val.client_name) {
								val.display_name += ' :: ' + val.client_name;
							}
						});
						_this.clients = res.clients;
						deferred.resolve(_this.clients);
					});
				}

				return deferred.promise;
			},
		};
	}]);

	module.service('ssClientComboboxAccount', ['$q', '$http', function($q, $http) {
		return {
			findByClientId : function(id, focusMediaIds) {
				if(focusMediaIds){
					focusMediaIds = angular.toJson(focusMediaIds);
				}
				var deferred = $q.defer();
				$http({
					url: '/sem/new/api/account/index/' + id,
					params: {focus_media_ids:focusMediaIds},
					method: 'GET'
				}).success(function(res) {
					deferred.resolve(res.accounts);
				});
				return deferred.promise;
			},
		};

	}]);

	module.service('ssClientComboboxCategoryGenre', ['$q', '$http', function($q, $http) {
		return {
			getCategoryGenre : function(clientId) {
				var deferred = $q.defer();
				$http({
					url: '/sem/new/api/categorygenre/index/' + clientId,
					method: 'GET'
				}).success(function(res) {
					deferred.resolve(res.category_genre);
				});
				return deferred.promise;
			}
		};

	}]);


	module.service('ssClientComboboxDimension', ['$q', '$http', function($q, $http) {
		var deferred = null;
		return {
			findByClientId : function(clientId) {
				if (deferred) {
					deferred.resolve();
					deferred = null;
				}
				deferred = $q.defer();
				$http({
					url: '/sem/new/api/axis/dimensionparent/index/' + clientId,
					method: 'GET'
				}).success(function(res) {
					deferred.resolve(res.dimension_parent);
				});
				return deferred.promise;
			}
		};
	}]);



	// ==========================================================
	// 以下 Filter
	// ==========================================================
	//選択済み検索
	module.filter('ssClientComboboxSelectFilter', function($filter){
		return function(arr,text) {
			arr = $filter('filter')(arr, {selected:false});
			if(arr){
				arr = $filter('ssClientComboboxMuitipleFilter')(arr, text);
			}
			return arr;
		};
	});


	//未選択検索
	module.filter('ssClientComboboxSelectedFilter', function($filter){
		return function(arr,text) {
			arr = $filter('filter')(arr, {selected:true});
			if(arr){
				arr = $filter('ssClientComboboxMuitipleFilter')(arr, text);
			}
			return arr;
		};
	});

	//複数検索
	module.filter('ssClientComboboxMuitipleFilter',function($filter){
		return function(arr,query){
			if (query) {
				// 全角スペースを半角スペースに置換
				var query = query.replace(/　/g, " ");
				// 検索対象ワードの配列を作成
				var queryWordArray = query.split(" ");

				var filteredList = [];
				var tmpIdList = [];

				queryWordArray.forEach(function (queryWord) {
					var tmpArr = $filter('filter')(arr, queryWord);
					if(tmpArr && tmpArr.length > 0){
						tmpArr.forEach(function(obj) {
							if(filteredList.indexOf(obj) < 0){
								filteredList.push(obj);
							}
						});
					}
				});
				return filteredList;
			}
			return arr;
		};
	});


}());



// ==========================================================
// その他
// ==========================================================

var toggleFlg = function(obj) {
	if(obj){
		return false;
	}else{
		return true;
	}
};

