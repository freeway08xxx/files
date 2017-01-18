var app_dir = angular.module('ssTemplate.directives', []);


app_dir.directive('markdown', function() {
	return {
		controller: ['$scope', function($scope) {
			var str = "# h1\n## h2\n### h3\n```\nfunction abc(){\n}\n```\n---\n1. list\n2. list\n\n**強調文字**";//初期値サンプル 不要なら削除

			$scope.markdown = {
				data : {
					before:str,
					after :''
				},
				captures : [],
				actions:{
					isShowCapture : true,
					isDragover    : false,
					onChange :  function() {
						var before = angular.copy($scope.markdown.data.before)
						if($scope.markdown.actions.isShowCapture){
							for (var i=0; i<$scope.markdown.captures.length; i++) {
								before = before.replace(eval('/replace:capture_' + (i+1) + '/g'),'<img src="'+$scope.markdown.captures[i].uri+'">');
							}
						}
						$scope.markdown.data.after  = marked(before);
					},
					save : function(){
						console.log($scope.markdown.data)
					}
				}
			}

			$scope.markdown.data.after = marked($scope.markdown.data.before);
		}],
		scope:false,
		restrict: 'A',
		link: function (scope) {
			var inEle    = $('.js-file-input');

			// ドラッグで画面遷移させない
			inEle.on('dragenter', function(e) {e.preventDefault();})
				.on('dragover', function(e){
					scope.$apply(function() {scope.markdown.actions.isDragover = true });
					e.preventDefault();
				})
				.on('dragleave', function(e){
					scope.$apply(function() {scope.markdown.actions.isDragover = false });
				})
				.on('drop', function(e){
					scope.$apply(function() {scope.markdown.actions.isDragover = false });
					e.preventDefault();
					var file = e.originalEvent.dataTransfer.files[0];

					// 画像表示
					if (file.type.match('image.*')) {
						var reader = new FileReader();
						reader.readAsDataURL(file);
						//読み込み後の処理
						reader.onload = function(e){
							scope.markdown.captures.push({uri:e.target.result});
							scope.$apply(function(){
								scope.markdown.data.before += '\nreplace:capture_' + scope.markdown.captures.length;
								scope.markdown.actions.onChange();
							});
						};
					}
			});
		}
	};
});
