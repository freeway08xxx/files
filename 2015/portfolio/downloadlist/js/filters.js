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
			case '処理中': res = 'label-default';break;
			case '完了': res = 'label-success';break;
			case 'スキップ': res = 'label-primary';break;
			case 'エラー': res = 'label-danger';break;
			break
		};
		return res;
	};
});


filter.filter('replaceUser',function() {
	return function(data) {
		if (data != null){
			var targetList   = ['tasks','batch'];
			var replacedName = 'システム';
			_.each(targetList, function(target) {
				if (data.toLowerCase().indexOf(target) != -1) data = replacedName; 
			})
		}
		return data;
	};
});

filter.filter('tableFilter',['$filter', function($filter){
	return function(master,user_id,filters){
		var res = [];
		_.each(master, function(val) {
			if(!_.isNull(val[filters.displayType + '_id'])){ 
				res.push(val); 
			}
		})

		var tmp_res = [];
		if(filters.service != ''){
			_.each(res, function(val) {
				if(val['service'] == filters.service) tmp_res.push(val);
			})
			res = tmp_res;
		}

		if(filters.is_my_history){
			tmp_res = [];
			_.each(res, function(val) {
				if(val['user_id'] == user_id) tmp_res.push(val);
			})
			res = tmp_res;
		}

		if(filters.has_download){
			tmp_res = [];
			_.each(res, function(val) {
				if(!_.isNull(val['file_path'])) tmp_res.push(val); 
			})
			res = tmp_res;
		}

		return res;
	};
}]);
