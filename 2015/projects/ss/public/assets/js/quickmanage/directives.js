
angular.module('quickManage.directives', [])

	.directive('fileUpload', function () {
	  return {
	      link : function(scope, element) {
	          element.on('change', function(event) {
	              if (!window.FileReader) {
	                  alert('お使いのブラウザではこの画面でファイルをアップロードできません。\n HTML5対応ブラウザをご使用ください。');
	              }
	              var file = event.target.files[0];

	              scope.file.filetype = file.type;

	              var reader = new FileReader();
	              reader.readAsDataURL(file);
	              reader.onload = function (file) {
	                  scope.file.dataurl = file.target.result;
	              };
	          });
	      }
	  };
	})

	.directive('ngScrollable', function () {
		return {
			restrict: "A",
			link: function (scope, elements) {
				var element = elements[0];
				var content = document.getElementById('templateList'); // スクロールさせたいコンテンツ。適宜セレクタは書き換えるべし。

				scope.$watch(
					function () { return element.clientHeight; },
					function () { content.style.height = element.clientHeight + 'px'; }
				);
			}
		};
	});