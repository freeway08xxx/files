var directives = angular.module('tskt.directives', []);


// ===================================================================
/**
 * htmlのバインドと$compileを後から実行してくれるdirective 
 *  
 * 例：
 * <div compile><div ng-click="click()"></div></div>
 *  
 */
// ===================================================================

directives.directive('compile', ['$compile', function ($compile) {
	return function(scope, element, attrs) {
		scope.$watch(
			function(scope) {
				return scope.$eval(attrs.compile);
			},
			function(value) {
				element.html(value);
				$compile(element.contents())(scope);
			}
		);
	};
}]);

// ===================================================================
/**
 * テーブルの表示を行うDirective 
 *  
 * 例：
 * <div ss-table-rc="tsktTaskSave.processListTable" external-scopes="tsktTaskSave"></div>
 *  
 */
// ===================================================================
directives.directive('ssTableRc', [ '$compile', 'ssTableRcUtil', function($compile, ssTableRcUtil){
	return {
        scope: {
          ssTableRc: '=',
          getExternalScopes: '&?externalScopes' 
        },
		restrict: 'AE',
		replace: true,
		require:'ssTableRc',
		controller:'ssTableRcCtrl',
		controllerAs:'$ssTableCtrl',
		template: '<div class="ss-table">	<table style="margin-bottom:0px;"class="table table-striped table-hover table-condensed table-bordered" ng-class="{\'table-scroll\': $ssTableCtrl.isScroll()}">		<thead class="ss-table-head"> 					<tr class="ss-table-tr"> 							<th class="ss-table-th-{{$ssColumn.field}}" 													ng-hide="$ssColumn.isHide" 													style="width:{{$ssColumn.width}}; text-align:center"													ng-repeat="$ssColumn in ssTable.config.columnDefs" >						<span ng-if="$ssColumn.isSortEnable">							<button ng-click="$ssTableCtrl.clickSort($ssColumn.field)" class="sort" ng-class="$ssTableCtrl.activeSortClass($ssColumn.field);">								{{$ssColumn.displayName}}								</button>								</span>						<span ng-if="!$ssColumn.isSortEnable">							{{$ssColumn.displayName}}						</span>				</th> 							</tr>  			</thead>		<tbody class="ss-table-body">							<tr ng-if="(ssTable.data || ssTable.data.length >= 0)" ng-class="{success: ssTable.api.selection.isActiveRow($ssRow)}"		 								ng-click="ssTable.api.selection.clickTableRow($ssRow);" 					 								ng-repeat="$ssRow in ssTable.data" > 					 								<td style="width:{{$ssColumn.width}}; vertical-align:middle;" class="ss-table-td ss-table-td-{{$ssColumn.field}}" 					ng-hide="$ssColumn.isHide" 						 											ng-repeat="$ssColumn in ssTable.config.columnDefs" 						 											align="{{$ssColumn.align}}" >							 											<ss-table-rc-cell></ss-table-rc-cell>					 								</td> 				 					</tr>					<tr ng-if="(!ssTable.data)">							<td style="height:89px;text-align:center;vertical-align:middle;"colspan="{{$ssTableCtrl.columnSize}}">									<p style="font-size:0.8rem:">						<i style="display:block;">							<img src="/sem/new/assets/img/icon_loading.gif" alt="loading icon" width="25">						</i>Now loading....					</P>							</td>					</tr>					<tr ng-if="(ssTable.data.length <= 0)" >							<td style="height:89px;text-align:center;vertical-align:middle;"colspan="{{$ssTableCtrl.columnSize}}">表示するデータがありません</td>					</tr> 		</tbody>			 				</table></div> ',
		compile: function () {
			return {
				post: function ($scope, $elm, $attrs, ssTableRcCtrl) {
					/*
					var tableElm = ssTableRcUtil.getTmeplate('ss-table-rc/table');
					template = $compile(tableElm)($scope);
					$elm.replaceWith(template);
					*/
				}
			}
		}
	}
}])
/**
 * Cell directive 
 */
directives.directive('ssTableRcCell', [ '$compile', function($compile){
	return {
    	scope: false,
		replace: true,
		restrict: 'AE',
		link: function ($scope, $elm, $attrs) {
			$scope.$ssCell = $scope.$ssRow[$scope.$ssColumn.field];
			if($scope.$ssCell == null){
				$scope.$ssRow[$scope.$ssColumn.field] = null;
			}

			// 表示
			view = $.extend({},$scope.$ssCell);
			if($scope.$ssColumn.template){
				view = $scope.$ssColumn.template;
			}else{
				view = '<div>{{$ssRow[$ssColumn.field]}}</div>';
			}
			new_elm = $compile(view)($scope);
			$elm.replaceWith(new_elm);
		}
	}
}])

/**
 * Controller 
 */
.controller('ssTableRcCtrl',
	['$scope', '$element', '$filter', 'ssTableRcFactory', function($scope, $element, $filter, ssTableRcFactory) {
		var _this = this;
		_this.broadcast = function(key,data){
			$scope.$parent.$broadcast(key,data);
		}

		_this.isScroll = function() {
			return $scope.ssTableRc.config.isScroll;
		}

		_this.columns = $scope.ssTableRc.config.columnDefs;
		_this.columnSize = _this.columns.length;

		_this.clickSort = function(field) {
			if(_this.sortField == field){
				_this.sortDesc = !_this.sortDesc;
			}else{
				_this.sortDesc = false;
				_this.sortField = field;
			}
			$scope.ssTable.data = $filter('orderBy')($scope.ssTable.data, _this.sortField, _this.sortDesc)
		}

		_this.activeSortClass = function(field) {
			if(_this.sortField == field){
				return !_this.sortDesc ? {'sort-asc' : true} : {'sort-desc': true};
			}
		}

		$scope.ssTable = ssTableRcFactory.createTable($scope);
	}]
);


/**
 * テーブルの生成/管理を行う 
 */
directives.factory('ssTableRcFactory',
	['ssTableRcApiService', function(ssTableRcApiService){
		var _this = this;
		_this.public = {
			createTable : function($scope) {
				// 渡されたcolumnDefsを再整形
				var tableConfig = {align:'left'};
				angular.forEach($scope.ssTableRc.config.columnDefs, function(value, key) {
					
					// 初期化設定
					if(!value.align){
						value.align = 'left';
					}
					var columnDefs = value;
					$scope.ssTableRc.config.columnDefs[key] = columnDefs;
					
				});
				ssTable = $scope.ssTableRc;

				//各種APIの作成
				api = ssTableRcApiService.core.initialize($scope,ssTable);
				if(ssTable.config.selection){
					api.selection = ssTableRcApiService.selection.initialize($scope,ssTable);
				}

				//それらのAPIを登録
				if('registerApi' in ssTable.config){
					ssTable.config.registerApi(api);
				}

				ssTable.api = api;
				return ssTable;
			}
		}

		return _this.public;
	}]	
);

/**
 * utility 
 */
directives.service('ssTableRcUtil', 
	[ '$templateCache', function($templateCache) {
		var _this = this;
		_this.util = {
			getTmeplate: function(template) {
				return $templateCache.get(template);
			},
		}

		return _this.util;
	}]
)

/**
 * 各APIの管理 
 */
.service('ssTableRcApiService',
	['ssTableRcApiCore', 'ssTableRcApiSelection', function(ssTableRcApiCore,ssTableRcApiSelection){
		var _this = this;
		_this.public = {
			core: ssTableRcApiCore,
			selection: ssTableRcApiSelection,
		};

		return _this.public;
	}]
)

/**
 * Api/core
 */
.service('ssTableRcApiCore',
	[function(){
		var _this = this;
		_this.service = {
			initialize: function($scope,ssTable) {
				var api = {

					/**
					 * スコープの指定を取得する 
					 */
					getExternalScopes : function() {
						return $scope.getExternalScopes();
					},


					showField: function(field) {
						angular.forEach($scope.ssTableRc.config.columnDefs, function(value, key) {
							if(value.field == field){
								value.isHide = false;
							}
						});
					},

					hideField: function(field) {
						angular.forEach($scope.ssTableRc.config.columnDefs, function(value, key) {
							if(value.field == field){
								value.isHide = true;
							}
						});
					}
				}

				return api;
			}
		}

		return _this.service;
	}]
)

/**
 * Api/selection
 *
 * rowなどの選択を拡張する
 */
.service('ssTableRcApiSelection',
	[function(){
		var _this = this;
		_this.models = {};
		_this.service = {
			initialize: function($scope,ssTable) {
				var api = {

					/**
					 * rowのクリックアクション 
					 */
					clickTableRow: function(row) {
						if(ssTable.config.selection.rowClick){
							ssTable.config.selection.rowClick(row);
						}
					},

					setActiveRow: function(row) {
						_this.models.activeRow = row;
					},
					
					/**
					 * そのRowがアクティブかどうか 
					 */
					isActiveRow: function(row) {
						return _this.models.activeRow == row;
					},
				}

				return api;
			}
		}

		return _this.service;
	}]	
)





// ===================================================================
/**
 * inputと通常spanを切り替えるdirective(未完成) 
 *  
 * 例：
 * <span ss-transform-input="{type:'text'}" ng-model="model" edit-mode="true"></span>
 *  
 */
// ===================================================================

directives.constant('ssTransformInputConst', {
	type:{
		TEXT: 'text',
		SELECT: 'select',
		RADIO: 'radio',
		CHECKBOX: 'checkbox',
		FILE: 'file',
		TEXTAREA: 'textarea'
	}
});

directives.directive('ssTransformInput', [ '$compile', 'ssTransformInputUtil', function($compile, ssTransformInputUtil){
	return {
        scope: {
			ssTransformInput: '=',
			ngModel: '=',
			editMode: '=',
        },
		restrict: 'AE',
		//replace: true,
		require  : '^ngModel',
		controller:'ssTransformInputCtrl',
		controllerAs:'$ssTransformInput',
		link: function($scope, $elem, $attrs, ngModel) {
			//$scope.ngModel = ngModel;
			//$scope.$ssTransformInput.models.value = ngModel.$viewValue;

			var option = $scope.ssTransformInput
			var template = ssTransformInputUtil.getTmeplate(option.type);
			var element = $compile(template)($scope);
			$elem.replaceWith(element);
		},
	}
}])
/**
 * Controller 
 */
//ssTransformInputCtrl AS $ssTransformInput
.controller('ssTransformInputCtrl',
	['$scope', '$element', '$attrs', function($scope, $element, $attrs) {
		var _this = this;
		_this.models = {};

		$scope.$watch('ngModel', function() {
			_this.models.value = $scope.ngModel;
		});
		$scope.$watch('editMode', function() {
			_this.models.editMode = $scope.editMode;
		});
	}]
)
.service('ssTransformInputUtil', 
	[ '$templateCache', 'ssTransformInputConst' , function($templateCache, ssTransformInputConst) {
		var _this = this;
		_this.util = {
			getTmeplate: function(type,isTransfrom) {
				var template_path = isTransfrom ? 'input' : 'view';
				template_path += '/'+type;

				return $templateCache.get('ssTransformInput/' + template_path);
			},
		}
		return _this.util;
	}]
)



// ===================================================================
/**
 * datepickerの表示を制御するDirective 
 *  
 * 例：
 * <div tskt-datepicker="config" ng-model="model"></div> 
 *  
 *	config = {
 *		minDate : '2014-01-01',
 *		maxDate : '2020-01-01',
 *		format : 'yyyy/MM/dd',
 *		dateOptions : {
 *			formatYear: 'yyyy',
 *			startingDay: 1
 *		},
 *		opened : false,
 *		open : function($event) {
 *			$event.preventDefault();
 *			$event.stopPropagation();
 *			this.opened = true;
 *		}
 *	};
 */
// ===================================================================


directives.constant('tsktDatepickerConst', {
	config: {
		minDate : '2014-01-01',
		maxDate : '2020-01-01',
		format : 'yyyy/MM/dd',
		dateOptions : {
			formatYear: 'yyyy',
			startingDay: 1
		},
	},
	minute : ['00','15','30','45'],
});

directives.directive('tsktDatepicker', [ '$compile', 'tsktDatepickerConst', '$templateCache', function($compile, tsktDatepickerConst, $templateCache){
	return {
        scope: true,
		restrict: 'AE',
		replace: true,
		require  : '^ngModel',
		//templateUrl: 'tskt/datepicker',
		template: '<div class="form-inline">	<div class="input-group" ng-hide="option.isDateHide" style="display: inline-block; width: auto;">		<input style=" max-width: 100px;" datepicker-localdate ng-change="change()" type="text" class="form-control" name="text_date"  ng-model="models.date" placeholder="日付テキスト"			is-open="models.opened" min-date="datepicker.minDate" max-date="datepicker.maxDate" datepicker-popup="{{datepicker.format}}" datepicker-options="datepicker.dateOptions" 		/>		<span class="input-group-btn">			<button type="button" class="btn btn-default" ng-click="open($event)"><i class="glyphicon glyphicon-calendar"></i></button>		</span></div> 	<span ng-hide="option.isTimeHide" style="display: inline-block; white-space: nowrap;"> 		<select name="select_normal" class="form-control" style="width:60px; display: inline;"			ng-model="models.hour"			ng-options="n for n in [] | range:1:25"		></select>		:		<select name="select_normal" class="form-control" style="width:60px; display: inline;"			ng-model="models.minute"			ng-options="value for value in minute"		></select>	</span></div>',
		link: function($scope, $elm, $attrs, ngModel) {

			$scope.minute = tsktDatepickerConst.minute;
			$scope.opened = false;
			$scope.datepicker = tsktDatepickerConst.config;
			if ($attrs.tsktDatepicker) {
				$.extend($scope.datepicker, $scope.$eval($attrs.tsktDatepicker));
			}

			if ($attrs.option) {
				$scope.option = $scope.$eval($attrs.option);
			}

			if ($attrs.ngModel) {
				var time = $scope.$eval($attrs.ngModel);
				if(!time.date){
					var dt = new Date();
					dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
					//dt = dt.toISOString().substring(0, 10);
					time.date = dt;
				}else{
					time.date.setMinutes(time.date.getMinutes() - time.date.getTimezoneOffset());
					//time.date = time.date.toISOString().substring(0, 10);
					time.date = time.date;
				}

				if(!time.hour){
					time.hour = 19;
				}
				if(!time.minute){
					time.minute = '00';
				}
				$scope.models = time;
			}

			$scope.open = function($event) {
				$event.preventDefault();
				$event.stopPropagation();
				$scope.models.opened = true;
			}

			$scope.change = function() {
				if($scope.option.changeDate){
					$scope.option.changeDate();
				}
			}
		},
	}
}]);

directives.directive('datepickerLocaldate', ['$parse', function ($parse) {
	var directive = {
			restrict: 'A',
			require: ['ngModel'],
			link: link
		};
	return directive;

	function link(scope, element, attr, ctrls) {
		var ngModelController = ctrls[0];
		ngModelController.$parsers.push(function (viewValue) {
			if (!viewValue) {
				return undefined;
			}

			viewValue.setMinutes(viewValue.getMinutes() - viewValue.getTimezoneOffset());
			//return viewValue.toISOString().substring(0, 10);
			return viewValue;
		});

		ngModelController.$formatters.push(function (modelValue) {
			if (!modelValue) {
				return undefined;
			}
			var dt = new Date(modelValue);
			dt.setMinutes(dt.getMinutes() + dt.getTimezoneOffset());
			return dt;
		});
	}
}]);


directives.directive('scrollOnClick', function() {
	return {
		restrict: 'A',
		link: function(scope, $elm, attrs) {
			var idToScroll = attrs.href;
			$elm.on('click', function() {
				var $target;
				if (idToScroll) {
					$target = $(idToScroll);
				} else {
					$target = $elm;
				}
				$("body").animate({scrollTop: $target.offset().top}, "slow");
			});
		}
	}
});
