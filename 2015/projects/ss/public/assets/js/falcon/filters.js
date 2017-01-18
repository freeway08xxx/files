/* Filters */
var filter = angular.module('falcon.filters', []);

filter.filter('extCvFilter', ['$filter', function ($filter) {
	return function (items, search, col_arr) {
		var selected_keys = _.pluck(_.filter(col_arr, {'element_type': 'ext_cv'}), 'key');

		var filtered = $filter('filter')(items, search);

		return _.transform(filtered, function (res, val) {
			if (!_.contains(selected_keys, val.key)) res.push(val);

			return res;
		});
	};
}]);

filter.filter('clip', function () {
	return function (text, length) {
		return text.substring(0, length);
	};
});

filter.filter('offset', function() {
	return function(input, start) {
		start = parseInt(start, 10);
		return input.slice(start);
	};
});
