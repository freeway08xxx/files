/* Filters */
var filter = angular.module('downloadlist.filters', []);

filter.filter('offset', function() {
	return function(input, start) {
		if (input !== undefined) {
			start = parseInt(start, 10);
			return input.slice(start);
		}
	};
});


filter.filter('statusClass',function() {
	return function(data) {
		switch (data){
			case '実行中':res = 'label-default';break;
			case '終了': res  = 'label-primary';break;
			case '完了': res  = 'label-success';break;
			case 'エラー':res = 'label-danger';break;
			break
		};
		return res;
	};
});

