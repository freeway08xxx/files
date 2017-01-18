/* Services */

angular.module( 'knowledge.services', [] )

	/* locationHashService */
	.factory('locationHashService', function($location) {
		var paramsObj = new Array();
		return {
			getHashParam : function(pName,defaultData) {
				var hash = decodeURI($location.hash());
				var hashArr = hash.replace(/^#/, '').split("#");
				params = hashArr[hashArr.length-1].split("&");
				for (key in params) {
					paramsMap = params[key].split("=");
					paramsKey = paramsMap[0];
					paramsValue = paramsMap[1];
					if (pName == paramsKey) {
						return paramsValue;
					}
				}
				return defaultData;
			},
			addHashData : function(pName, pValue) {
				hash = ""
				paramsObj[pName] = pValue;
				for (var key in paramsObj) {
					var param = key + "=" + paramsObj[key] + "&"
					hash += param;
				}
			},
			setHashParam : function() {
				$location.hash(hash)
			}
		};
	})
	/* カテゴリの検索を管理するService */
	.factory('apiSection', function($q, $http) {
		return {
			getAll : function(callback) {
				var deferred = $q.defer();
				$http.get('/sem/new/knowledge/api/section/all.json')
					.success(function(data) {
						deferred.resolve(data);
						callback(data.section_all);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},	
		};
	})

	/* カテゴリの検索を管理するService */
	.factory('apiPurpose', function($q, $http) {
		return {
			getIndex : function(id, callback) {
				var deferred = $q.defer();
				$http.get('/sem/new/knowledge/api/purpose/index/' + id + '.json')
					.success(function(data) {
						deferred.resolve(data);
						callback(data.purposes);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},	
			getAll : function(sectionType, callback) {
				var deferred = $q.defer();
				$http.get('/sem/new/knowledge/api/purpose/all/' + sectionType + '.json')
					.success(function(data) {
						deferred.resolve(data);
						callback(data.purposes);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},	
			getChilds : function(id, callback) {
				var deferred = $q.defer();
				$http.get('/sem/new/knowledge/api/purpose/childs/' + id + '.json')
					.success(function(data) {
						deferred.resolve(data);
						callback(data.purposes);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},
			addIndex : function(typeSection, parentPurposeId, name) {
				var deferred = $q.defer();
				var postData = { type_section : typeSection, parent_purpose_id : parentPurposeId , name : name};
				$http.post('/sem/new/knowledge/api/purpose/index.json', postData)
					.success(function(data) {
						deferred.resolve(data);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},
			editName : function(purposeId, name) {
				var deferred = $q.defer();
				var postData = {  name : name };
				$http.post('/sem/new/knowledge/api/purpose/name/' + purposeId, postData)
					.success(function(data) {
						deferred.resolve(data);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			}
		};
	})

	/* ファイルの検索を管理するService */
	.factory('apiSearch', function($location, $http, $q) {
		var deferred = null;
		return {
			query : {},
			doSearch : function(callback) {
				if (deferred) {
					deferred.resolve();
					deferred = null;
				}
				deferred = $q.defer();
				var	search_url = "/sem/new/knowledge/api/search.json?";
				var add_search="";
				if ('typeSection' in this.query) {
					add_search += 'type_section=' + this.query['typeSection'];
				}
				if ('free_word' in this.query) {
					add_search += '&free_word=' + this.query['free_word'];
				}
				if ('purposes' in this.query) {
					add_search += '&purpose_ids=' + this.query['purposes'];
				}
				search_url += add_search;
				//検索実行
				$http.get(search_url, {timeout: deferred.promise})
					.success(function(data, status, headers, config) {
						deferred.resolve(data);
						callback(data);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			}
		};
	})

	/* ファイルの検索を管理するService */
	.factory('fileDetail', function($location, $http, $q) {
		return {
			get : function(id,typeSection) {
				var deferred = $q.defer();
				$http.get('/sem/new/knowledge/api/file/detail/' + id + '.json?type_section=' + typeSection)
					.success(function(data) {
						deferred.resolve(data);
					}).error(function (data, status, headers, config) {
						if(status == 404){
							alert("エラーが発生しました。");
						}
				});
    			return deferred.promise;
			},
		};
	})

	.factory('lodingModal', function($modal) {
		var modalInstance = null;
		return {
			start : function() {
				if (!modalInstance){
					modalInstance = $modal.open({
						templateUrl:"progress.html", 
						backdrop:"static",keyboard:false// ユーザーがクローズできないようにする
					});
				}
			},
			end : function() {
				if (modalInstance){
					modalInstance.close()
					modalInstance = null;
				}

			}
		};
	});
