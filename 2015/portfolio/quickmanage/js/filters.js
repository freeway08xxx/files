/* filters */
var filters = angular.module('quickManage.filters', []);

/**
 * データの￥、％を選定。データがなければ'--'
 */
filters.filter('format', ['quickManageConst','tableFactoryService', function(quickManageConst,tableFactoryService){
	return function (data,elm) {

		var res = tableFactoryService.getFormat(elm)

		if(data == '' || data == null){
			data        = '--';
			res.yen     =  '' ;
			res.percent =  '' ;
		}

		return res['yen'] + data + res['percent'];
	};
}]);


/**
 * ソートをかけても常に合計値はテーブル上部にくるようにする
 */
filters.filter('customOrderBy', ['quickManageConst', function(axisConst){
	return function(data) {
		var arr_filtered = [];
		var arr_amount   = [];

		_.each(data, function(val){
			(val["bureau_name"] === '合計') ? arr_amount.push(val) : arr_filtered.push(val);
		});

		if(arr_amount.length != 0) arr_filtered.unshift(arr_amount[0]);
		return arr_filtered;
	};
}]);

/**
 * trが合計であればclass'sum'を付与
 */
filters.filter('addSumClass', [function(){
	return function (data) {
		return (data === '合計') ? 'sum':'';
	};
}]);


/**
 * trが合計であればデータ欄は'合計'を入れる
 */
filters.filter('replaceSum', [function(){
	return function (data,str) {
		return data = (str === '合計') ? '合計':data;
	};
}]);

/**
 * 期間比較のclass名
 */
filters.filter('label_class', [function(){
	return function (data) {

		parseFloat(data);

		if(data >= 0){
			data = "percent label label-success";
		}else if(data < 0){
			data = "percent label label-danger";
		}else{
			data = "hide";
		}

		return data;
	};
}]);

