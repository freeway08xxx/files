/* Filters */

var ReacquireFilter = angular.module('Reacquire.filters', []);

ReacquireFilter.filter('offsetFrom', function() {
    return function(input, start) {
		if (Object.keys(input).length > 0) {
			start = +start;

			return input.slice(start);
		}
    }
});