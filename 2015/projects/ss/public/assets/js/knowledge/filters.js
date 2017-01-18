/* Filters */

angular.module('knowledge.filters', [])
	.filter('filterSection', function() {
		return function(items, field, reverse) {
			var filtered = [];
			angular.forEach(items, function(item) {
				if(item.authority_level > 0) {
					filtered.push(item);
				}
			});
			return filtered;
		};
	})
	.filter('noHTML', function($sce){
		return function(text) {
			return text ? text.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/&/, '&amp;') : '';
		}
	})
	.filter('newlines', function($sce){
		return function(text) {
			return $sce.trustAsHtml( text ? text.replace(/\n/g, '<br />') : '')
		}
	});
