/* Services */
var service = angular.module('client.conversion.services', []);

/**
 * DataStore
 */
service.service('clCvStore', [ function () {
	this.config = {
		client : {}
	};

	this.models = {
		client: {},
	};
}]);

/**
 * DataHandler
 */
service.factory('clCvService',
['clCvStore', 'clBaseStore',
function (clCvStore, clBaseStore) {

	/**
	 * private Methods
	 */
	function submit () {
		var client_id = clBaseStore.models.client.client.id;

		$('form#cv_setting').attr({
			action: '/sem/new/client/conversion/setting/?client_id=' + client_id,
			method: 'POST'
		})
		.submit();
	}

	/**
	 * public API
	 */
	return {
		submit: submit
	};
}]);
