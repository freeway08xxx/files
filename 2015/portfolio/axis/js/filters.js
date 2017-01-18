/* Filters */
var filters = angular.module('axis.filters', [])

/**
 * ソートをかけても常に合計値はテーブル上部にくるようにする
 */
filters.filter('customOrderBy', ['axisConst', function(axisConst){
	return function(data) {
		var arr_filtered = [];
		var arr_amount   = [];

		_.each(data, function(val){
			(val["account_name"] === axisConst.unsorted['account_name']) ? arr_amount.push(val): arr_filtered.push(val);
		});

		if(arr_amount.length!=0) arr_filtered.unshift(arr_amount[0]);
		return arr_filtered;
	};
}]);


filters.filter('addSumClass', ['axisConst', function(axisConst){
	return function (data) {
		return (data === axisConst.unsorted['account_name']) ? 'sum':'';
	};
}]);


ss_filter.filter('hidden', function () {
	return function (data) {
		return res = (data === '' || data == null) ? 'hidden':'';
	};
});


filters.filter('label_class', [function(){
	return function (data) {

		parseFloat(data);
		if(data >= 0){
			data = "percentage label label-success";
		}else if(data < 0){
			data = "percentage label label-danger";
		}else{
			data = "hide";
		}

		return data;
	};
}]);


filters.filter('format', ['axisConst','utilFactoryService', function(axisConst,utilFactoryService){
	return function (data,elm) {

		var res = utilFactoryService.getFormat(elm)

		if(data === '' || data == '--' || data == null){
			data        = '--';
			res.yen     =  '' ;
			res.percent =  '' ;
		}

		return res['yen'] + data + res['percent'];
	};
}]);


filters.filter('sub_str', [function(){
	return function (data,num) {
		if(typeof data !== 'undefined' ) return  data.substr(num);
		
	};
}]);


//or検索
filters.filter('muitipleFilter',function($filter){
	return function(arr,query,is_show){
		var filteredList = [];
		var expression   = '';

		if (query) {
			// 全角スペースを半角スペースに置換
			var query = query.replace(/　/g, " ");
			// 検索対象ワードの配列を作成
			var queryWordArray = query.split(" ");

			queryWordArray.forEach(function (queryWord) {
				expression += "obj['cv_display'].toLowerCase().indexOf('"+queryWord.toLowerCase()+"') >= 0 || "
				arr.forEach(function(obj,i) {
					obj['is_search_result'] = eval(expression.slice(0, -4)) ? true:false;
					filteredList[i] = obj;
				});
			});
		}else{
			arr.forEach(function(obj) {
				obj['is_search_result'] = true;
				filteredList.push(obj);
			});
		}
		return filteredList;
	};
});


filters.filter('pickup', [function(){
	return function (data,boole) {
		var cnt = 0;
		_.each(data, function(value,i){
			if(value['is_show'] == boole) cnt++
		});
		return  cnt;
		
	};
}]);


filters.filter('replaceSumTitle', [function(){
	return function (data,account_name,cell) {
		if(cell == 'media_id' && account_name != '合計') {
			data = '';
		}
		else if(account_name == '合計') {
			 data = account_name;
		}
		return  data;

	};
}]);


filters.filter('addImageClass', ['tableFactoryService',function(tableFactoryService){
	return function (data) {
		return (tableFactoryService.hasImg(data)) ? '':'no-image';
	};
}]);


filters.filter('showImage',['tableFactoryService',function(tableFactoryService){
	return function (data) {
		return  (tableFactoryService.hasImg(data)) ? data:'';
	};
}]);
