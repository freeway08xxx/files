/* Directives */
var directives = angular.module('wabisabid.directives', []);

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
