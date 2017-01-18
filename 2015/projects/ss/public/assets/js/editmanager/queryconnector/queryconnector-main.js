// Query Connector
$(document).ready(function() {

    var page_id = 'editmanager';
    var nav_id  = 'queryconnector';
    $("ul.contents-nav li").removeClass("active");
    $("#" + page_id + "_" + nav_id).addClass("active");

	// 実行
	$("#query_connect").on("click", function() {

		// 素材ファイル設定時のみ実行
		if (document.form01.query_up_file.value !== "") {

			if (confirm("クエリコネクターを実行します。")) {

				document.form01.action_type.value = $("input[name='exec_type']:checked").val();
				document.form01.action = "/sem/new/editmanager/queryconnector/create/exec";
				document.form01.submit();
			}
		} else {
			alert ("ファイルが選択されていません。");
		}
	});
});

// AngularJS
var action_type = document.form01.action_type.value; // 実行モード取得

var app = angular.module('queryConnector', []);

app.controller('FormController', ['$scope', function($scope) {
	$scope.mode_list = [
	                    {name:'形態素解析のみ', value:'analysis'},
	                    {name:'GoogleAPI見積もり(※時間がかなりかかるのでスケジュールに余裕があるときのみ)', value:'google_estimate'},
	                    {name:'YahooAPI見積もり', value:'yahoo_estimate'}
	                    ];
	$scope.checked = action_type;

	// オプション
	$scope.OpenOptionEdit = function() {

		if ($scope.OptionEdit == true) {
			$scope.OptionEdit = false;
			document.form01.replace_words.value = "";

		} else {
			$scope.OptionEdit = true;
		}
	}
}]);