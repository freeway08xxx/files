/* Services */
var service = angular.module('client.mediacost.services', []);

/**
 * DataStore
 */
service.service('clMediaCostStore', ['clMsg', function (clMsg) {

	this.config = {
		msg : clMsg.mediacost
	};

	this.models = {
		client: {},
		is_regist_mode: false,
		add: {
			target_type : '1',
			media_id    : '',
			cost        : 20
		},
		target_media_list  : {},
		target_account_list: {},
		delete_target: {
			media  : [],
			account: []
		},
		is_checked_alldelete: {
			media  : false,
			account: false
		},
		is_show_msg: {
			update : false,
			deleted: false,
			error  : false,
			invalid: false
		},
		msg: '',
		is_collapse_attention: true
	};
}]);

/**
 * DataHandler
 */
service.factory('clMediaCostService',
['$http', '$q', 'clBaseStore', 'clMediaCostStore',
function ($http, $q, clBaseStore, clMediaCostStore) {

	var _this = this;

	/**
	 * private Methods
	 */
	function initRegistMode () {
		clMediaCostStore.models.is_regist_mode = true;

		// _this.comboboxApi.setModels({
		// 	client: clBaseStore.models.client.client.id
		// });
	}

	function cancel() {
		clMediaCostStore.models.is_regist_mode = false;

	}

	function prepareAccountlistForServer () {
		if (_.isEmpty(clMediaCostStore.models.client.accounts)) return [];

		return _.map(clMediaCostStore.models.client.accounts, function (val) {
			return val.media_id + '//' + val.account_id;
		});
	}

	function validate(data, form) {
		var deferred = $q.defer();

		if (!form.$valid ||
			(data.target_type === '1' && _.isEmpty(data.media_id)) ||
			(data.target_type === '2' && _.isEmpty(data.account_list))
			) {
			deferred.reject({data: clMediaCostStore.config.msg.invalid});
			return deferred.promise;
		}

		return $http({
			url: '/sem/new/client/mediacost/check/' + clBaseStore.models.client.client.id + '.json',
			method: 'POST',
			data: data
		});
	}

	function save(form) {
		var data = {
			target_type : clMediaCostStore.models.add.target_type,
			media_id    : clMediaCostStore.models.add.media_id,
			media_cost  : clMediaCostStore.models.add.cost,
			account_list: prepareAccountlistForServer()
		};

		validate(data, form).then(function (res) {

			/*
			 * res.data は null = valid Ok, エラーの場合メッセージarray
			 */
			if (res.data[0]) {
				clMediaCostStore.models.msg = res.data[0];
				clMediaCostStore.models.is_show_msg.invalid = true;
				return false;
			}

			$http({
				url: '/sem/new/client/mediacost/save/' + clBaseStore.models.client.client.id + '.json',
				method: 'POST',
				data: data
			}).then(function (res) {
				console.log(res.data);

				// テーブル情報更新のため再読み込み
				location.reload();

			}, function (error) {
				console.log(error);
				clMediaCostStore.models.is_show_msg.error = true;
			});
		}, function (error) {
			clMediaCostStore.models.msg = error.data;
			clMediaCostStore.models.is_show_msg.invalid = true;
		});

	}

	function update (type) {
		var target_list = (type === 'media') ?
			clMediaCostStore.models.target_media_list : clMediaCostStore.models.target_account_list;

		var add_mediacost_list = _.transform(_.pairs(target_list), function(result, val) {
			result.push(_.zipObject(['id', 'media_cost'], val));
		});

		$http({
			url: '/sem/new/client/mediacost/update/' + clBaseStore.models.client.client.id + '.json',
			method: 'POST',
			data: {add_mediacost_list: add_mediacost_list}
		}).then(function (res) {
			console.log(res.data);

			if (res.data[0]) clMediaCostStore.models.is_show_msg.update = true;
		});
	}

	function deleteSet (type) {
		var del_mediacost_list = _.chain(clMediaCostStore.models.delete_target[type])
			.pick(function (n) {return n;})
			.keys()
			.value();

		$http({
			url: '/sem/new/client/mediacost/delete/' + clBaseStore.models.client.client.id + '.json',
			method: 'POST',
			data: {del_mediacost_list: del_mediacost_list}
		}).then(function (res) {
			console.log(res.data);

			// テーブル情報更新のため再読み込み
			location.reload();

		}, function (error) {
			console.log(error);
			clMediaCostStore.models.is_show_msg.error = true;
		});
	}

	function checkAll (key) {
		var target_list = (key === 'media') ?
			clMediaCostStore.models.target_media_list : clMediaCostStore.models.target_account_list;

		clMediaCostStore.models.delete_target[key] = _.transform(target_list, function (res, val, id) {
			res[id] = !clMediaCostStore.models.is_checked_alldelete[key];
			return res;
		});
	}

	function closeMsg (key) {
		clMediaCostStore.models.is_show_msg[key] = false;
	}

	/**
	 * public API
	 */
	return {
		initRegistMode: initRegistMode,
		cancel        : cancel,
		validate      : validate,
		save          : save,
		update        : update,
		deleteSet     : deleteSet,
		closeMsg      : closeMsg,
		checkAll      : checkAll,
	};
}]);
