/* Controllers */
angular.module('knowledge.controllers', [])

	/* セクション管理 */
  .controller('SectionCtrl', ['$scope', '$rootScope', 'apiSection', 'locationHashService', function($scope, $rootScope, apiSection, locationHashService) {
	$scope.typeSection = locationHashService.getHashParam('type_section','SEM');

	/* モデル初期化 */
	$scope.model = $scope.model ? $scope.model : {};
	$scope.action = $scope.action ? $scope.action : {};
	$scope.service = $scope.service ? $scope.service : {};

	$.extend($scope.model,{
		sectionAll : [],
		selectSection : []
	});

	/* アクション */
	$.extend($scope.action, {
		//タブクリック
		clickSectionTab : function (typeSection) {
			$scope.typeSection = typeSection;
			$scope.service.setActiveSection();
			$rootScope.$broadcast('sectionTabClick');
		}
	});

	/* サービス */
	$.extend($scope.service, {
		isActivate : function (tabname) {
			if ($scope.typeSection == tabname) {
				return true;
			}
		},
		setActiveSection : function() {
			angular.forEach($scope.model.sectionAll, function (val, key) {
				if( $scope.service.isActivate(val.key) ) {
					$scope.model.selectSection = val;
				}
			});
		}
	});

	//section取得
	apiSection.getAll(function(data) {
		$scope.model.sectionAll = data;
		$scope.service.setActiveSection();
	});
  }])

	/* 資料カテゴリのコンボボックスを管理するコントローラー(SearchCtrlを継承してある) */
  .controller('purposeHierarchyCtrl', ['$scope', 'apiPurpose', 'apiSearch', '$q', '$http'  , 'locationHashService', '$compile'
	,function($scope, apiPurpose, apiSearch, $q, $http, locationHashService, $compile) {

		$scope.model = $scope.model ? $scope.model : {};
		$scope.action = $scope.action ? $scope.action : {};
		$scope.service = $scope.action ? $scope.service : {};
		$scope.disable = false;

		/* モデル */
		$.extend($scope.model,{
			purposes : {
				all : [],
				hierarchy : {
					high : 0,
					middle : 0,
					low : 0
				},
				hierarchyType : {
					high : 1,
					middle : 2,
					low : 3,
				},
				sectionType : {},
			}
		});

		/* アクション */
		$.extend($scope.action, {
			clickHierarchy : function(hierarchyType, purposesId) {
				if($scope.disable == false) {
					//カテゴリ階層別の選択カテゴリIDの更新(カテゴリ内の表示切り替え)
					$scope.service.setPurposeHierarchy(hierarchyType, purposesId);
					//検索クエリの登録(テーブル再ロード)
					$scope.query.purposes = $scope.service.getQueryPurpose(hierarchyType, purposesId, []);
				}
			}
		});

		/* サービス */
		$.extend($scope.service, {
			/*カテゴリ取得*/
			setPurposesAll : function () {
				if ($scope.model.purposes.sectionType[$scope.typeSection]) {
					$scope.model.purposes.all = $scope.model.purposes.sectionType[$scope.typeSection];
				}else{
					apiPurpose.getAll($scope.typeSection, function(data) {
						$scope.model.purposes.all = data;
						$scope.model.purposes.sectionType[$scope.typeSection] = data;
					});
				}
			},

			/*各階層カテゴリセット*/
			setPurposeHierarchy : function (hierarchyType, purposesId) {
				if (hierarchyType == $scope.model.purposes.hierarchyType.high){
					$scope.model.purposes.hierarchy.high = purposesId;
					$scope.model.purposes.hierarchy.middle = 0;
					locationHashService.addHashData('s_purpose_h',purposesId);
					locationHashService.addHashData('s_purpose_m',0);
				} else if(hierarchyType == $scope.model.purposes.hierarchyType.middle) {
					$scope.model.purposes.hierarchy.middle = purposesId;
					$scope.model.purposes.hierarchy.low = 0;
					locationHashService.addHashData('s_purpose_m',purposesId);
					locationHashService.addHashData('s_purpose_l',0);
				} else if(hierarchyType == $scope.model.purposes.hierarchyType.low) {
					$scope.model.purposes.hierarchy.low = purposesId;
					locationHashService.addHashData('s_purpose_l',purposesId);
				} else {
					$scope.model.purposes.hierarchy.high = 0;
					$scope.model.purposes.hierarchy.middle = 0;
					locationHashService.addHashData('s_purpose_h',0);
					locationHashService.addHashData('s_purpose_m',0);
				}
			},

			/*カテゴリ検索用クエリ取得*/
			getQueryPurpose : function (hierarchyType, purposesId, preQuerPurposes) {
				if (hierarchyType == $scope.model.purposes.hierarchyType.high){
					angular.forEach($scope.model.purposes.all, function (val, key) {
						PushArray(preQuerPurposes, purposesId);
						if (val.parent_purpose_master_id === purposesId) {
							//middleをセット
							preQuerPurposes = $scope.service.getQueryPurpose($scope.model.purposes.hierarchyType.middle, val.id, preQuerPurposes);
						}
					});
				} else if(hierarchyType == $scope.model.purposes.hierarchyType.middle) {
					angular.forEach($scope.model.purposes.all, function (val, key) {
						PushArray(preQuerPurposes, purposesId);
						if (val.parent_purpose_master_id === purposesId) {
							//lowをセット
							preQuerPurposes = $scope.service.getQueryPurpose($scope.model.purposes.hierarchyType.low, val.id, preQuerPurposes);
						}
					});
				} else if(hierarchyType == $scope.model.purposes.hierarchyType.low) {
					PushArray(preQuerPurposes, purposesId);
				} else {
					if (purposesId == "" && 'purposes' in $scope.query) {
						//delete $scope.query.purposes;
						preQuerPurposes = [];
					}
				}
				return preQuerPurposes;
			},

			/* カテゴリ追加View表示 */
			addPurposeView : function(hierarchy) {
				console.log();
				var el = $compile( "<div class='input-group'><div class='input-group-addon'><span class='glyphicon glyphicon-plus'></span></div><input style='width:100%;' ng-keydown='service.addPurpose($event,\"" + hierarchy +"\")' class='addPurpose form-control' typpe='text' placeholder='追加するカテゴリを入力してください'></div>" )( $scope );
				var id = "purpose-hierarchy-" + hierarchy;
				angular.element($("#" + id)).children().append(el);
				$("#" + id).find(".addPurpose").focus();
			},

			/* カテゴリ追加 */
			addPurpose : function(e,hierarchy) {
				if (e.which === 13) {
					$(e.target).attr("disabled","disabled");
					var value = e.target.value;
					var parent_purpose_master_id = $scope.model.purposes.hierarchy.middle;
					if (hierarchy == 'middle'){
						parent_purpose_master_id = $scope.model.purposes.hierarchy.high;
					}
					apiPurpose.addIndex($scope.typeSection, parent_purpose_master_id, value).then(function(data){
						var obj = {
							delete_datetime: null,
							delete_flg: "0" ,
							id: data.purpose_id,
							insert_datetime: null ,
							name: value,
							parent_purpose_master_id: parent_purpose_master_id,
							sort_order: "0",
							tmp_section: $scope.typeSection,
							update_datetime: null
						}
						$scope.model.purposes.all.push(obj);
						$("#purpose-hierarchy-" + hierarchy).find(".addPurpose").parent("div").remove();
					});
				}
			}
		});


		$scope.$on('sectionTabClick', function(event,data) {
			$scope.service.setPurposesAll();
			//カテゴリ表示切り替え(要検討)
			$scope.service.setPurposeHierarchy("","");
			//クエリ更新
			$scope.query.purposes = $scope.service.getQueryPurpose("", "", []);
			$scope.query.typeSection = $scope.typeSection;
		})

		//カテゴリ全部取得
		$scope.service.setPurposesAll();
		$scope.model.purposes.hierarchy.high = locationHashService.getHashParam('s_purpose_h',0);
		$scope.model.purposes.hierarchy.middle = locationHashService.getHashParam('s_purpose_m',0);
		$scope.model.purposes.hierarchy.low = locationHashService.getHashParam('s_purpose_l',0);

  }])

	/* ファイル検索を管理するコントローラー */
  	.controller('SearchCtrl', ['$scope', 'apiSearch', 'locationHashService',
			function($scope, apiSearch, locationHashService) {

		/* 検索クエリ設定 */
		$scope.query = apiSearch.query;
		$scope.query.typeSection = $scope.typeSection;

		/* テーブル情報 */
		$scope.fileTable = {
			loading : true,
			data : [],	//各データ配列
			columns : [	//各カラム設定
				{ width: '5%' },
				{ width: '20%' },
				{ width: '20%' },
				{ width: '10%' },
				{ width: '5%' },
				{ width: '10%' },
				{ width: '10%' },
				{ width: '10%' },
			],
			columnDefs : [	//各カラムマッピング
				  { "mDataProp": "id",               "className": "id",               "aTargets":[0]},
				  { "mDataProp": "title",            "className": "title",            "aTargets":[1]},
				  { "mDataProp": "description",      "className": "description",      "aTargets":[2]},
				  { "mDataProp": "entry_user_name",  "className": "entry-user-name",  "aTargets":[3]},
				  { "mDataProp": "download_count",   "className": "download-count",   "aTargets":[4]},
				  { "mDataProp": "file_update_date", "className": "file-update-date", "aTargets":[5]},
				  { "mDataProp": "update_datetime",  "className": "update-datetime",  "aTargets":[6]},
				  { "mDataProp": "insert_datetime",  "className": "insert-datetime",  "aTargets":[7]},
			],
			overrideOptions : {	//テーブル全体オプション
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
			},
			filesRowCallback : function(nrow, adata, idisplayindex, idisplayindexfull) {	//テーブル表示後のコールバック
				$(nrow).bind('click', function() {
					$scope.$apply(function() {
						//location.href = '/sem/new/knowledge/detail/view?file_id=' + adata.id;
					});
				});
				return nrow;
			},
			formateColumnData : function(data) {	//カラムのフォーマット
				for (key in data) {
					var val = data[key];
					var detailLink = '/sem/new/knowledge#/detail/view?file_id=' + val.id + '#type_section='  + $scope.typeSection;
					//val.title = "<a href='" + detailLink + "' target='_blank'>" + val.title + "</a>";
					val.title = "<a href='" + detailLink + "' >" + val.title + "</a>";
				}
				return data;
			}
		}

		$scope.init = function() {
			//ハッシュから初期値取得
			var tmp_freeWord = locationHashService.getHashParam('free_word',false);
			var tmp_purpose_ids = locationHashService.getHashParam('purpose_ids',false);
			if(tmp_freeWord != false){
				$scope.query.free_word = tmp_freeWord;
			}
			if(tmp_purpose_ids != false){
				$scope.query.purposes = tmp_purpose_ids.split(',');
			}

			//フィルター対象(フリーワード + 小カテゴリ)
			$scope.$watch(
				function() {	//監視する値を返す Angular 式（文字列）、または関数。
					target = "";
					angular.forEach($scope.query, function (val, key) {
						target += val;
					});
					return target;
				},
				function() {	//値が変化した時に実行される Angular 式、または関数。
					$scope.action.doSearch();
				}
			);
		}

		/* アクション登録 */
		$scope.timeid = "";
		$scope.action = $scope.action ? $scope.action : {};
		$.extend($scope.action, {
			//ファイル検索
			doSearch : function() {
				clearTimeout($scope.timeid);
				$scope.timeid = setTimeout( function(){
					$scope.fileTable.loading = true;
					apiSearch.doSearch(function(data) {
						$scope.fileTable.data = $scope.fileTable.formateColumnData(data.files);
						$scope.fileTable.loading = false;
						if ($scope.query.hasOwnProperty('free_word') ) {
							locationHashService.addHashData('free_word',$scope.query.free_word);
						}
						if ($scope.query.hasOwnProperty('purposes')) {
							locationHashService.addHashData('purpose_ids',$scope.query.purposes);
						}
						if ($scope.query.hasOwnProperty('typeSection')) {
							locationHashService.addHashData('type_section',$scope.query.typeSection);
						}
						locationHashService.setHashParam();
					});
				}, 500);
			},
		});
  	}])


	/* ファイル編集コントローラー */
	.controller('fileEditCtr', ['$scope','$window', 'fileDetail', 'lodingModal',
		function($scope, $window, fileDetail, lodingModal) {

			$scope.model = $scope.model ? $scope.model : {};
			/* モデル */
			$.extend($scope.model,{
				file : {},
				purposes : {
					hierarchy : {
						high : {id:0},
						middle : {id:0},
						low : {id:0}
					},
					isSelected : false,
				},
				form : {
					isFreeText :false,
					roleSelected : false,
				}
			});

			$scope.init = function(file_id){
				//画面モーダル開始
				lodingModal.start();

				//ファイル情報の取得
				if(!file_id) file_id = 0;
				$scope.file_id = file_id;
				fileDetail.get($scope.file_id,$scope.typeSection).then(function(data){
				$scope.model.file = data;

				//file情報が無い場合の初回データ
				if (!$scope.model.file.file) {
					$scope.model.file.file = {search_active_flg :1};
					$scope.model.file.file_role_ids = [2,3,4,5,8];
				}

					//初回選択カテゴリのマージ
					angular.forEach($scope.model.file.file_purpose, function (file_purpose_val, file_purpose_key) {
						angular.forEach($scope.model.file.purpose_all, function (purpose_val, purpose_key) {
							if(file_purpose_val.id == purpose_val.id) {
								purpose_val.selected = true;
							}
						});
					});

					//選択カテゴリの監視
					$scope.$watch(
						'model.file.purpose_all',
						function() {	//値が変化した時に実行される Angular 式、または関数。
							$scope.model.purposes.isSelected =
								$scope.model.file.purpose_all.some(function(value) {
									if(value.selected){
										return true;
									}
								});
						},true
					);

					//初回選択ロールのマージ
					angular.forEach($scope.model.file.file_role_ids, function (role_id, key) {
						angular.forEach($scope.model.file.role_all, function (role_data, key2) {
							if(role_id== role_data.id) {
								role_data.selected = true;
							}
						});
					});

					//選択ロールの監視
					$scope.$watch(
						'model.file.role_all',
						function() {	//値が変化した時に実行される Angular 式、または関数。
							$scope.model.form.roleSelected =
								$scope.model.file.role_all.some(function(value) {
									if(value.selected){
										return true;
									}
								});
						},true
					);

					//フリーワードのタグ設定
					$scope.model.form.isFreeText = $scope.model.file.file ? true : false;
					var freeTextSearch = $('#form_freetext_for_search').magicSuggest({
						placeholder : '検索タグ',
						name : 'freetext_for_search',
						data: {},
						hideTrigger: true,
						useTabKey: true,
						value : $scope.model.form.isFreeText ? $scope.model.file.file.freetext_for_search : [],
					});
					$(freeTextSearch).on('blur', function(c){
						var data = freeTextSearch.getValue();
						$scope.$apply(function() {
							$scope.model.form.isFreeText = data.length > 0 ? true : false;
						});
					});

					//モーダル終了
					lodingModal.end();
					$("#knowledge-main-content").fadeIn("slow");

				});
			}

			$scope.$on('sectionTabClick', function(event,data) {
				window.location = '/sem/new/knowledge#/list#type_section='  + $scope.typeSection;
			})

			$('#selectFile').on('click', function() {
				$('#form_knowledge_file').trigger('click');
			})


			$scope.actionDelete = function() {
				if(window.confirm('削除してもよろしいですか？')){
					location.href = "/sem/new/knowledge/detail/update/delete?file_id=" + $scope.model.file.file.id + "&type_section=" + $scope.typeSection;
				}
			}
		}
	]);
