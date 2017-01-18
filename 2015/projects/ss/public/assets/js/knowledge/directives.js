angular.module('knowledge.directives', []).

/* datatableの作成 */
  directive('myDataTable', function(){
	return function(scope, element, attrs) {
	// apply DataTable options, use defaults if none specified by user
		var options = {};
		if (attrs.myDataTable.length > 0) {
			options = scope.$eval(attrs.myDataTable);
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
 })
	.directive('upFile',function(){
		return {
			require:'ngModel',
			link:function(scope,el,attrs,ngModel){

				ngModel.$render = function () {
					if(el.val()){
						ngModel.$setViewValue(el.val());
					}
				};

				el.bind('change', function (event) {
					scope.$apply(function () {
						ngModel.$render();
					});
				});
			}
		};
	})

	.directive( 'purposeBreadCrumb', function ( $compile ) {
		return {
			restrict: 'AE',	
			link : function(scope, element, attrs){
				var purposeId = scope.$eval(attrs.purposeId);
				var purposeAll = scope.$eval(attrs.purposeAll);
				var hierarchyPurposes = [];
				var addPurposeParent = function(setPurposeId) {
					angular.forEach(purposeAll, function (purpose_val, purpose_key) {
						if(purpose_val.id == setPurposeId){
							hierarchyPurposes.unshift(purpose_val);
							if(purpose_val.parent_purpose_master_id){
								addPurposeParent(purpose_val.parent_purpose_master_id);
							}
						}
					});
				}
				addPurposeParent(purposeId);

				var el = "<ol style=''class='breadcrumb'>";
				angular.forEach(hierarchyPurposes, function (purpose_val, purpose_key) {
					var li = "<li class='";
					if(hierarchyPurposes.length != purpose_key + 1){
						li += 'active';	
					}
					li += "'>" + purpose_val.name + "</li>";
					el += li;
				});
				el += "</ol>";
				var el = $compile(el)(scope);
    			element.replaceWith(el);
			}
		};
	})
	.directive( 'editPurposeButton', function ( $compile , apiPurpose) {
		var template = '';
		template += '<div ng-show="!disable" class="pull-right hover-pointer" style="color:DeepSkyBlue;">';
		template += '<span class="glyphicon glyphicon-edit"></span>';
		template += '</div>';
		return {
			restrict: 'A',
			replace:true,
			template:template,
			controller:['$scope', function($scope) {
				
				//編集Inputを表示
				$scope.editPurposeView = function(purpose) {
					$scope.$parent.disable = true;
					//var purpose = $scope.$eval(attrs.editPurposeButton)
					var editInput = $compile( "<input style='width:100%;' ng-keydown='editPurpose($event,\"" + purpose.id +"\")' class='editPurposeInput form-control' value='' type='text'>" )( $scope );
					var target = $(event.target).closest(".list-group-item");
					target.append(editInput);
					target.find(".editPurposeInput").focus().val(purpose.name);
					angular.forEach($scope.$parent.model.purposes.all, function (val, key) {
						if( val.id == purpose.id ) {
							val.editing = true;
						}
					});
				};

				//編集Input内容を送信
				$scope.editPurpose = function(event,purposeId) {
					if (event.which === 13) {
						var value = event.target.value;
						apiPurpose.editName(purposeId, value).then(function(data){
							angular.forEach($scope.$parent.model.purposes.all, function (val, key) {
								if( val.id == purposeId ) {
									val.name = value;
									val.editing = false;
								}
							});
							$(".editPurposeInput").remove();
							$scope.$parent.disable = false;
						});
					}
				}
			}],

			link:function(scope,el,attrs,ngModel){
				//ボタンクリック
				el.bind('click', function (event) {
					var purpose = scope.$eval(attrs.editPurposeButton)
					scope.editPurposeView(purpose);
				});
			}
		}
	})
