// Declare app level module which depends on filters, and services
angular.module('Reacquire', [
	'Reacquire.services',
	'Reacquire.controllers',
	'Reacquire.filters',
	'Reacquire.directives',
	'checklist-model',
	'ss.module.client-combobox',
	'ss.module.termdate'
]);

var app = angular.module('Reacquire');

app.config(["datepickerConfig", "datepickerPopupConfig", function(datepickerConfig, datepickerPopupConfig) {
	datepickerConfig.showWeeks        = false;
	datepickerConfig.yearRange        = 10;
	datepickerConfig.dayTitleFormat   = "yyyy年 MMMM";
	datepickerConfig.formatYear       = "yyyy";
	datepickerPopupConfig.currentText = "本日";
	datepickerPopupConfig.clearText   = "クリア";
	datepickerPopupConfig.closeText   = "閉じる";
}]);

app.config(["ssClientComboboxConfig", function(ssClientComboboxConfig) {
	ssClientComboboxConfig.client.bcName  = 'ss_combobox_client_change';
	ssClientComboboxConfig.account.bcName = 'ss_combobox_account_change';
	ssClientComboboxConfig.account.isView = true;
}]);

app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeBar       = true;
    cfpLoadingBarProvider.includeSpinner   = true;
    cfpLoadingBarProvider.latencyThreshold = 100;
}]);