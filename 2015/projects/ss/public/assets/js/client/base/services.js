/* Services */
var service = angular.module('client.base.services', []);

/**
 * DataStore
 */
service.service('clBaseStore', [ function () {
	this.config = {
		tab    : '',
		client : {}
	};

	this.models = {
		client     : {},
		comboboxApi: null,
		tabs       : [
			{key: 'cv', title: 'コンバージョン設定', path: '/cv/index', detail_path: '/cv/list', is_active: false},
			{key: 'mediacost', title: 'レポート | 媒体費設定', path: '/mediacost/index', detail_path: '/mediacost/detail', is_active: false},
			// {key: 'category', title: 'レポート | カテゴリ設定', path: '/category/index', is_active: false}
		]
	};
}]);


/**
 * Servive Logic
 */
service.factory('clBaseService',
['$location', '$route', '$timeout', 'clBaseStore', 'ssUtils',
function ($location, $route, $timeout, clBaseStore, ssUtils) {

	/**
	 * private Methods
	 */

	// サイドメニューリンク
	function chgLocation (key) {
		var path = _.chain(clBaseStore.models.tabs)
						.filter({'key': key})
						.pluck('path')
						.value()
						.toString();

		if (!_.isNull(path) && !_.isUndefined(path)) $location.path(path);
	}

	function chgLocationToDetail () {
		var detail_path = _.chain(clBaseStore.models.tabs)
							.filter('is_active')
							.pluck('detail_path')
							.value()
							.toString();

		if ($location.path().indexOf(detail_path) !== -1) return false;

		$location.path(detail_path);
	}

	function setLocationParamsClient (id) {
		return $timeout(function () {
			ssUtils.setLocationParams('client_id', id);
		});
	}

	function setActiveTab (maintab) {
		clBaseStore.models.tabs = _.map(clBaseStore.models.tabs, function (val) {
			val.is_active = (val.key === maintab);
			return val;
		});
	}

	function isIndexAndClientSelected () {
		if (ssUtils.getLocationPath().indexOf('index') === -1) return false;

		var params = ssUtils.getLocationParams();
		if (_.isEmpty(params)) return false;

		return _.has(params, 'client_id');
	}

	function reload () {
		console.log('route reload');
		$route.reload();
	}

	function locateToDetailWhenClientChangeWatched () {
		return function (newval, oldval) {
			if (_.isEmpty(newval)) return false;

			setLocationParamsClient(newval.id).then(function () {
				// index ページでクライアントが選択されていれば詳細へ移動
				if (isIndexAndClientSelected()) {
					console.log('isIndex');
					chgLocationToDetail();
					return;
				}

				if (_.isEmpty(oldval)) {
					console.log('noOldVal');
					chgLocationToDetail();
					return;
				}

				if (oldval.id !== newval.id) {
					console.log('locate change');
					chgLocationToDetail();
				}
			});
		};
	}

	/**
	 * public
	 */
	return {
		chgLocation                          : chgLocation,
		chgLocationToDetail                  : chgLocationToDetail,
		setLocationParamsClient              : setLocationParamsClient,
		setActiveTab                         : setActiveTab,
		isIndexAndClientSelected             : isIndexAndClientSelected,
		reload                               : reload,
		locateToDetailWhenClientChangeWatched: locateToDetailWhenClientChangeWatched
	};
}]);
