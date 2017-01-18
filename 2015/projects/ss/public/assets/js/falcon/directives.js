var dir = angular.module('falcon.directives', []);

dir.constant('falconTemplateConst', {
	msg: '※注意※\n削除すると元には戻せませんが、よろしいですか？\n\nテンプレート名：'
});

/**
 * テンプレートリスト
 */
dir.directive('falconTemplate', function () {
	return {
		scope: true,
		restrict: 'AE',
		replace: true,
		require: 'ngModel',
		templateUrl: '/sem/new/assets/template/falcon/template-list.html',
		template:  '',
		controller: 'FalconTemplateCtrl',
		controllerAs: 'template',
		link: function () {
		}
	};
});

dir.controller('FalconTemplateCtrl',
	['$scope', '$rootScope', 'falconTemplateStore', 'falconTemplateData', 'falconTemplateConst',
	function($scope, $rootScope, falconTemplateStore, falconTemplateData, falconTemplateConst) {

	var _this = this;

	_this.models = falconTemplateStore.models;

	/**
	 * ViewMethods
	 */
	_this.apply = function (i) {
		falconTemplateData.apply(i).then(function (res) {

			/**
			 * Apply ClientComboBox
			 */
			var models = {
				client: $scope.common.models.client.client.id
			};
			models.accounts      = (!_.isEmpty(res.accounts)) ? res.accounts : null;
			models.categoryGenre = (!_.isEmpty(res.category_genre_id)) ? res.category_genre_id: null;

			$scope.common.models.comboboxApi.setModels(models);

			if (!falconTemplateData.isDaily()) $scope.report.tab.set('format');

			$rootScope.$broadcast($scope.common.config.bs_name.applyTemplate);
		});
	};
	_this.isSelected = function (i) {
		return _this.models.selected_index === i.toString();
	};
	_this.getReportTypeColor = function (report_type) {
		return (report_type === 'daily') ? 'label-warning' : 'label-info';
	};

	_this.info = function ($event) {
		$event.stopPropagation();
	};
	_this.delete = function ($event, i) {
		$event.stopPropagation();

		if (window.confirm(falconTemplateConst.msg + _this.models.list[i].template_name)) {
			falconTemplateData.delete(i);
		}
	};

	$scope.$on($scope.common.config.bs_name.chgClient, function() {
		console.info('start GetTemplate');

		falconTemplateData.get();
	});

	// テンプレート適用時のみ有効にするフォーム項目
	$scope.$watch('template.models.selected_index', function (newval) {
		falconTemplateData.toggleAimTab();
		$scope.report.models.cp_status.exclusion.is_disabled = _.isEmpty(newval);
	});
}]);


/**
 * ソート処理
 */
dir.directive('sortItems', function() {
	return {
		scope: false,
		restrict: 'A',
		link: function (scope) {

			scope.cp.models.regist.display.sort = {
				is_desc: true,
				in_sort: '',
				target : ''
			};

			var models = scope.cp.models.regist.display.sort;

			/**
			 * private Methods
			 */
			var _clear = function () {
				scope.cp.models.regist.display.sort = {
					is_desc: true,
					in_sort: '',
					target : ''
				};
			};

			// ソートの選定
			var _sortTarget = function (target) {
				models.in_sort = target;

				if (target === 'account_id') {
					scope.cp.models.list = _.sortByOrder(scope.cp.models.list, [target, 'campaign_id'], [!models.is_desc, true]);
					return;
				}

				scope.cp.models.list = _.sortByOrder(scope.cp.models.list, [target, 'account_id'], [!models.is_desc, true]);
			};

			/**
			 * accesser
			 */
			scope.sort = {
				methods: {
					toggleIsDesc: function (target) {
						// はじめてソートする場合は常に asc(昇順) から
						if (models.in_sort !== target) {
							models.is_desc = false;
							return;
						}

						models.is_desc = !models.is_desc;
					},
					orderBy: function (target) {
						_sortTarget(target);
					},
					getSortClass: function(target) {
						if (models.in_sort !== target) return false;

						return (models.is_desc) ? {'sort-desc': true} : {'sort-asc': true};
					}
				}
			};


			/**
			 * pubsub
			 */
			scope.$on(scope.common.config.bs_name.updateCpSetting, function () {
				_clear();
			});
		}
	};
});