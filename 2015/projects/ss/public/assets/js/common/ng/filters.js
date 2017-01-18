/* Filters */

var ss_filter = angular.module('ss.filters', []);

ss_filter.filter('noHTML', function($sce){
	return function(text) {
		return text ? text.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/&/, '&amp;') : '';
	}
});
ss_filter.filter('newlines', function($sce){
	return function(text) {
		return $sce.trustAsHtml( text ? text.replace(/\n/g, '<br />') : '')
	}
});

ss_filter.filter('unsafe', function($sce) { 
	return $sce.trustAsHtml; 
});

ss_filter.filter('moment', function () {
    return function (data, format) {
        var res = (data==undefined || data=='' || data==null) ? '' :  moment(data).format(format);
		return res == 'Invalid date' ? '' : res;
    };
});

ss_filter.filter('emptyResArg', function () {
	return function (data,className) {
		return className = (data === '' || data == null) ? className:data;
	};
});

ss_filter.filter('offset', function() {
	return function(input, start) {
		if (input !== undefined) {
			start = parseInt(start, 10);
			return input.slice(start);
		}
	};
});
