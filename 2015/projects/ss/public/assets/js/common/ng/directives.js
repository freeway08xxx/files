var app_dir = angular.module('ss.directives', []);

/**
 * データテーブル作成
 */
app_dir.directive('ssDataTable', function(){
	return function(scope, element, attrs) {
	// apply DataTable options, use defaults if none specified by user
		var options = {};
		if (attrs.ssDataTable.length > 0) {
			options = scope.$eval(attrs.ssDataTable);
		} else {
			options = {
				"bFilter": false,			// フィルタなし
				"bLengthChange": false,		// 表示件数変更なし
				"aaSorting": [0,"desc"],	// デフォルトソート
				"aLengthMenu" : [20],		// 表示件数メニュー
				"bAutoWidth" : false,
				"oLanguage": {
					"oPaginate": {
						"sPrevious": "前へ",
						"sNext": "次へ",
					},
					"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
					"sSearch":"検索",
				}
			};
		}
		if (attrs.aoColumns) {
			options["aoColumns"] = scope.$eval(attrs.aoColumns);
		}

		// aoColumnDefs is dataTables way of providing fine control over column config
		if (attrs.aoColumnDefs) {
			options["aoColumnDefs"] = scope.$eval(attrs.aoColumnDefs);
		}

		if (attrs.fnRowCallback) {
			options["fnRowCallback"] = scope.$eval(attrs.fnRowCallback);
		}

		// apply the plugin
		var dataTable = $(element).dataTable(options);

		// watch for any changes to our data, rebuild the DataTable
		scope.$watch(attrs.aaData, function(value) {
			var val = value || null;
			if (val.length > 0) {
				dataTable.fnClearTable();
				dataTable.fnAddData(scope.$eval(attrs.aaData));
			} else {
				//TODO
				//alert("見つかりませんでした。(仮)" );
				dataTable.fnClearTable();
			}
		});
	};
});



/**
 * ツールチップ表示
 */
app_dir.directive('ssTooltip', function () {
	return {
		restrict: "A",
		link: function (scope, elements) {
			$(elements).tooltip();
		}
	};
});

/**
 * ポップオーバー表示 HTMLタグ対応版
 * ※ UI-Bootstrap の拡張なので 、名前にSS付与しない
 */
app_dir.directive("popoverHtmlUnsafePopup", function () {
	return {
		restrict: "EA",
		replace: true,
		scope: { title: "@", content: "@", placement: "@", animation: "&", isOpen: "&" },
		templateUrl: "/sem/new/assets/template/popover-html-unsafe-popup.html"
	};
});
app_dir.directive("popoverHtmlUnsafe", [ "$tooltip", function ($tooltip) {
	return $tooltip("popoverHtmlUnsafe", "popover", "click");
}]);

/**
 * bootstrapっぽいinput[file]
 *
 * 必須属性
 *  ・ng-model
 * オプション
 * 	・button-value ボタン名 (デフォルト「ファイル選択」)
 */
app_dir.directive('ssInputFile',function(){
	var template = "";
	template += "<div name='ssBindFile' class='input-group ss-fileup-group'>";
	template += "<input id='input_file' type='file' style='display:none' ng-model='ssInputFile' name='ssfile'>";
	template += "<input type='text' class='form-control ss-fileup-text input-{{sizeType}}' readonly ng-model='ssInputFile.$modelValue.name' placeholder='ファイル選択'>";
	template += "<span class='input-group-btn ss-fileup-group-btn'><button class='btn btn-default ss-fileup-group-btn btn-{{sizeType}}' type='button' ng-bind='buttonValue' ng-click='ssFileSelect($event)'></button></span>";
	template += "</div>";
	return {
		require:'ngModel',
		scope: true,
		restrict: "AE",
		replace: true,
		template: template,
		controller:['$scope', '$timeout', function($scope, $timeout) {

			//ファイル選択を開く
			$scope.ssFileSelect = function(event) {
				$timeout(function(){
					$(event.target).closest("div[name=ssBindFile]").find("input[type=file]").trigger('click');
				});
			};
		}],
		link:function(scope,el,attrs,ngModel){
			scope.ssInputFile = ngModel;
			scope.buttonValue = 'ボタン選択';

			//ボタン名変更
			if (attrs.buttonValue) {
				scope.buttonValue = scope.$eval(attrs.buttonValue);
			}

			//sizeType
			if (attrs.sizeType) {
				scope.sizeType = scope.$eval(attrs.sizeType);
			}

			//ng-change
			if (attrs.ngChange) {
				scope.ngChange = attrs.ngChange;
			}



			//input file の変更をバインド
			$(el).find("input[type=file]").bind('change', function (event) {
				scope.$apply(function () {
					var file = event.target.files[0];
					ngModel.$render(file);
				});
			});
			//ngModelにset
			ngModel.$render = function (file) {
				if($(el).find("input[type=file]").val()){
					ngModel.$setViewValue(file);
				}
			};
		}
	};
});


/**
 * media_idをロゴ画像に差し替えるdirective 
 *  
 * 例：<span set-media-icon="{{media_id}}"></span>
 * 
 *  * 表示サイズはcssで調整
 *  * 1,2,3,17はss全体の定数取得確定後コンストに変更
 */

app_dir.directive('setMediaIcon', ['$compile',function($compile) {
	return {
		restrict: 'A',
		replace: true,
		scope: {
			setMediaIcon: '@'
		},
		link: function (scope, el) {
			var media = '';

			switch (scope.setMediaIcon){
				case '1': media = '<img src="/sem/new/assets/img/common/media_icon_mini_yahoo.png">';break;
				case '2': media = '<img src="/sem/new/assets/img/common/media_icon_mini_google.png">';break;
				case '3': media = '<img src="/sem/new/assets/img/common/media_icon_mini_ydn.png">';break;
				case '17':media = '<span>D2C</span>';break;
				break;
			};

		var new_elm = $compile(media)(scope);
		el.replaceWith(new_elm);
		}
	};
}]);


