/**
* AngularJS CommonAppModule
*/

// Declare app level module which depends on filters, and services
var app = angular.module('ss', [
	'ui.bootstrap',
	'ui.select',
	'ngRoute',
	'ngSanitize',
	'angular-loading-bar',
	'ss.services',
	'ss.controllers',
	'ss.filters',
	'ss.directives',
	window.ssContentApp,
]);

// common library config
if (window.numeral) {
	numeral.language('ja');
}