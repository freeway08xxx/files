var mod = angular.module('ss.module.graph', []);

mod.constant('ssGraphTemplate', {
	line: {
		data: {
			type: 'line',
			json: [],
			keys: {
				x: 'x',
				value: [],
			},
			axes: {}
		},
		axis: {
			x: {
				type: 'timeseries',
				tick: {
					multiline: false,
					format: '%Y/%m/%d',
					rotate: 70
				},
				height: 80
			},
			y: {
				label: {
					text: '',
					position: 'outer-middle'
				},
				tick: {}
			},
			y2: {
				show: false,
				label: {
					text: '',
					position: 'outer-middle'
				},
				tick: {}
			}
		},
		grid: {
			x: {
				show: true
			}
		}
	},
	bar: {
		data: {
			type: 'bar',
			json: [],
			keys: {
				x: 'x',
				value: [],
			},
			axes: {}
		},
		axis: {
			x: {
				type: 'category',
			},
			height: 60,
			y: {
				label: {
					text: '',
					position: 'outer-middle'
				},
				tick: {}
			},
			y2: {
				show: true,
				label: {
					text: '',
					position: 'outer-middle'
				},
				tick: {}
			}
		},
		grid: {
			y: {
				show: true
			}
		}
	},
	donut: {
		data: {
			type: 'donut',
			columns: []
		},
		donut: {
			title: ''
		}
	}
});

mod.directive('ssGraph',[ function(){
	return {
		scope: true,
		restrict: 'AE',
		replace: true,
		// require:'ngModel',
		templateUrl: '/sem/new/assets/template/module/graph.html',
		template:  '',
		link: function() {
		},
		controller:'ssGraphCtrl'
	};
}]);

mod.controller('ssGraphCtrl', ['$rootScope', '$scope', '$attrs', 'ssGraphService',
 function($rootScope, $scope, $attrs, ssGraphService) {
	$scope.chart = null;

	$scope.moduleModel = $scope.$eval($attrs.model);

	// watch model changes, then rebuild chart
	$scope.$watch('moduleModel.data', function(newdata) {
		if (newdata.length !== 0) {
			$scope.setting = angular.extend(
				{bindto: '#chart'},
				ssGraphService[$scope.moduleModel.type].get($scope.moduleModel)
			);

			$scope.chart = c3.generate($scope.setting);
		}
	}, true);
}]);


// ==========================================================
// 以下 Service
// ==========================================================
mod.service('ssGraphService', ['ssGraphLine', 'ssGraphBar', 'ssGraphDonut',
		function(ssGraphLine, ssGraphBar, ssGraphDonut) {
	return {
		line:  ssGraphLine,
		bar:   ssGraphBar,
		donut: ssGraphDonut
	};
}]);

mod.service('ssGraphLine', ['ssGraphTemplate', function(ssGraphTemplate) {
	var obj = angular.copy(ssGraphTemplate.line);

	return {
		get: function (model) {
			obj = getObjAxisFormated(obj, [model.index]);
			obj = linkDataFormatToAxis(obj, [model.index]);

			obj.data.json = model.data;
			obj.data.keys.value = getKeyValue(model.data);

			return obj;
		}
	};
}]);

mod.service('ssGraphBar', ['ssGraphTemplate', function(ssGraphTemplate) {
	var obj = angular.copy(ssGraphTemplate.bar);

	return {
		get: function (model) {
			var key_arr = getKeyValue(model.data);

			obj = getObjAxisFormated(obj, key_arr);
			obj = linkDataFormatToAxis(obj, key_arr);

			obj.data.json = model.data;
			obj.data.keys.value = key_arr;

			return obj;
		}
	};
}]);

mod.service('ssGraphDonut', ['ssGraphTemplate', function(ssGraphTemplate) {
	var obj = angular.copy(ssGraphTemplate.donut);

	return {
		get: function (model) {
			obj.data.columns = model.data;
			if (model.hasOwnProperty('title')) {
				this.setTitle(model.title);
			}

			return obj;
		},
		setTitle: function (title) {
			obj.donut.title = title;
		}
	};
}]);

// ==========================================================
// その他
// ==========================================================

var getKeyValue = function (data_arr) {
	var key_val = [];

	angular.forEach(data_arr[0], function (obj, key) {
		if (key !== 'x') {
			key_val.push(key);
		}
	});

	return key_val;
};

/**
 * 指標が金額やパーセントの場合に数値を整形
 * 指標が2つ以上ある場合はY軸を追加
 **/
var getObjAxisFormated = function (obj, key_arr) {
	var yn = 'y';

	angular.forEach(key_arr, function(keyname, i) {
		var format_str = '';

		// 2つ以上Y軸設定済みならば終了
		if (yn === 'y2') {
			return false;
		}
		if (i > 0) {
			yn = 'y2';
		}

		// add index option
		format_str = '0,0';
		if (isCurrency(keyname)) { format_str = '$0,0'; }
		if (isPercent(keyname))  { format_str = '0.00%'; }

		// save format_str for link data
		obj.axis[yn].format = format_str;

		// format axis option
		obj.axis[yn].label.text = keyname;
		obj.axis[yn].tick = {
			format: function (d) {
				return numeral(d).format(format_str);
			}
		};

		if (yn === 'y2') {
			obj.axis[yn].show = true;
		}
	});

	return obj;
};

var linkDataFormatToAxis = function (obj, key_arr) {
	var format = {normal: '', currency: '', percent: ''};

	// Detect Y Axis Option
	var axis_format = [obj.axis.y.format];
	if (obj.axis.y2.hasOwnProperty('format')) {
		axis_format.push(obj.axis.y2.format);
	}

	var yn = ['y', 'y2'];
	angular.forEach(axis_format, function(str, i) {
		if (String(str).indexOf('$') !== -1) {
			format.currency = yn[i];
		} else if (String(str).indexOf('%') !== -1) {
			format.percent = yn[i];
		} else {
			format.normal = yn[i];
		}
	});

	// link Data to Axis
	angular.forEach(key_arr, function(keyname) {
		var target = format.normal;
		if (isCurrency(keyname)) { target = format.currency; }
		if (isPercent(keyname))  { target = format.percent; }

		obj.data.axes[keyname] = target;
	});

	return obj;
};

var isCurrency = function (key) {
	var list = ['cost', 'cpa', 'cpc', 'discount_value', 'ext_cpa'];
	return list.indexOf(String(key).toLowerCase()) >= 0;
};

var isPercent = function (key) {
	var list = ['ctr', 'cvr', 'discount_rate', 'ext_cvr'];
	return list.indexOf(String(key).toLowerCase()) >= 0;
};
