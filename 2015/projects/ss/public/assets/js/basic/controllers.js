/* Controllers */
var app = angular.module('ssTemplate.controllers', []);

/* フォーム */
app.controller('SsTemplateFormCtrl', ['$scope', '$location', 'bstmplFormStore', '$routeParams', 'ssModal',
	function($scope, $location, bstmplFormStore, $routeParams, ssModal) {

	//画面設定Obj
	$scope.settings = {
		tab : $routeParams.tab ? $routeParams.tab : "tab1"
	};

	$scope.clientCombobox = {};
	$scope.clientComboboxConfig = {
		focusMediaIds: [1,17],
		categoryGenre:{
			isView: true
		},
		registerApi : function(api){
			$scope.comboboxApi = api;
		}
	};
	$scope.clientCombobox2 = {};
	$scope.clientComboboxConfig2 = {};


	//detepicker用Obj
	$scope.datepicker = {
		minDate : '2014-01-01',
		maxDate : '2020-01-01',
		format : 'yyyy/MM/dd',
		dateOptions : {
			formatYear: 'yyyy',
			startingDay: 1
		},
		opened : false,
		open : function($event) {
			$event.preventDefault();
			$event.stopPropagation();
			this.opened = true;
		}
	};

	$scope.trans = {
		is_show: false
	};


	//初回ロード
	$scope.init = function() {
		console.log("start FormCtrl");
		bstmplFormStore.getModels($routeParams.id).then(function(models){
			//modelに保持
			$scope.models = {master:{},form:{}};
			$scope.models.master.subject = models.subjectsModel;
			$scope.models.master.sports = models.suportsModel;
			$scope.models.form = models.formModel;


			//デフォルトのmodelをセット
			$scope.comboboxApi.setModels({
				client:"8995",
				accounts:[
					{media_id:1,account_id:"388966"}
				],
				categoryGenre:"81",
			});

		},function(error){
			$scope.openModal.apiError();
		});
	};

	//コンボボックスを空にする
	$scope.clearCombobox = function() {
		$scope.comboboxApi.clearModelsAll();
	}

	//サーバーに送信
	$scope.submit = function() {
		console.log('submit');
		$scope.openModal.apiSave().result.then(
			function(){
				//更新リクエスト
				bstmplFormStore.saveForm().then(function(data){
					//更新が完了した場合は、テーブル一覧を表示
					$scope.openModal.custom();
				},function(error){
					$scope.openModal.apiError();
				});
			},
			function(){
			// モーダルキャンセル
			}
		);
	};

	$scope.openModal = {
		loading : function() {
			return ssModal.loading('通信中です...');
		},
		apiSave : function() {
			console.log('apiSave');
			return ssModal.confirm('更新してもよろしいですか？');
		},
		apiError : function(){
			return ssModal.error('通信エラーが発生しました(デバッグ用)');
		},
		custom : function(){
			return ssModal.custom({
				templateUrl: '/sem/new/assets/template/modal/custom1.html',
				controller:['$scope', '$modalInstance', function($scope, $modalInstance) {
					$scope.message = '一覧に移動します';
					$scope.title = '登録完了!!(仮)';
					$scope.close = function () {
						$modalInstance.close();
					};
				}],
			}).result.then(function(){
				$location.url('/table');
			},null);
		}
	};

	$scope.btn = {
		edit: function() {
			alert('edit');
		},
		delete: function() {
			alert('delete');
		}
	}


	//アカウント選択後に飛んでくるメッセージ
	$scope.$on('ss_combobox_account_change', function(event, data) {
		console.log($scope.clientCombobox);
		console.log($scope.clientCombobox2);
	});

	//カテゴリジャンル選択後に飛んでくるメッセージ
	$scope.$on('ss_combobox_category_genre_change', function(event, data) {
		console.log($scope.clientCombobox);
		console.log($scope.clientCombobox2);
	});
}]);

/* テーブル */
app.controller('SsTemplateTableCtrl', ['$scope', '$location', '$timeout', 'bstmplTableStore', 'ssModal',
	function($scope, $location, $timeout, bstmplTableStore, ssModal) {

		var _this = this;


		_this.clickGetTableRow = function() {
			_this.ssTable.data = null;
			bstmplTableStore.getModels().then(function(basics){
				_this.ssTable.data = basics.tableModels;
			},function(error){
				ssModal.error('通信エラーが発生しました');
			});
		}
		_this.clickDeleteTableRow = function(){
			_this.ssTable.data = [];
		}

		_this.clickTableLoading = function(){
			_this.ssTable.data = null;
		}
		_this.clickTableHideAge = function(){
			_this.isShow = !_this.isShow;
			if(_this.isShow){
				_this.ssTable.api.showField('text');
			}else{
				_this.ssTable.api.hideField('text');
			}
		}

		_this.ssTable = {
			data : null, 
			config : {
				class: ['table','table-hover','table-condensed'],
				paging: {
					type: 'pager',
					itemsView: true,
				},
				columnDefs: [
					{width:'10%',	align: 'center', displayName:"ID",	field: "id", 	isSortEnable:true, 	fieldType:'int', template: '<a ng-click="getExternalScopes().clickRow($ssRow)">{{$ssRow[$ssColumn.field]}}</a>'	},
					{width:'60%',	align: 'center', displayName:"登録内容",field: "text", 	isSortEnable:true, isHide:true},
					{width:'10%',	align: 'center', displayName:"作成日",	field: "created_at"},
					{width:'10%',	align: 'center', displayName:"ダウンロード",field: "download", template:'<a href="/sem/new/basic/table/file?id={{$ssRow[\'id\']}}">ダウンロード</a>'},
					{width:'10%',	align: 'center', displayName:"編集",	field: "edit", 	template:'<a href="/sem/new/basic/#/form?id={{$ssRow[\'id\']}}">編集</a>', 
						headerTemplate: '<ss-button type="edit" size="xs" ng-click="getExternalScopes().editAll()">一括編集</ss-button>'
					},
				],
				register : {
					clickRow : function(event, row) {
						console.log(event);
						console.log(row);
					},
					api : function(api) {
						_this.ssTable.api = api;
					}
				},
			},
			external : {
				clickRow : function(row) {
					alert(row.text);
				},
				editAll : function() {
					alert('一括編集');
				}
			}
		}

		_this.clickGetTableRow();


		/* テーブル情報 */
		$scope.table = {
			loading : true,
			data : [],	//各データ配列
			columns : [	//各カラム設定
				{ width: '10%' },
				{ width: '60%' },
				{ width: '10%' },
				{ width: '10%' },
				{ width: '10%' },
			],
			columnDefs : [	//各カラムマッピング
				  { "mDataProp": "id",          "className": "id",          "aTargets":[0]},
				  { "mDataProp": "text",        "className": "text",        "aTargets":[1]},
				  { "mDataProp": "created_at",  "className": "created_at",  "aTargets":[2]},
				  { "mDataProp": "download",  "className": "download",  "aTargets":[3]},
				  { "mDataProp": "edit",  "className": "download",  "aTargets":[4]},
			],
			overrideOptions : {	//テーブル全体オプション
				"bFilter": false,			// フィルタなし
				"bLengthChange": false,		// 表示件数変更なし
				"aaSorting": [0,"desc"],	// デフォルトソート
				"aLengthMenu" : [5],		// 表示件数メニュー
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
				/*
				// クリックで詳細編集に飛ぶ
				$(nrow).bind('click', function() {
					$scope.$apply(function() {
						$location.path('/form').search('id',adata.id);
					});
				});
				*/
				return nrow;
			},
		};

		// グラフ情報
		$scope.graph = {
			type: '', // 'line' or 'bar' or 'donut'
			title: '', // if donut chart, Enter title : ex. 'CPA'
			index: '', // if line chart, Enter data index : ex 'cost | cvr | cpa'
			data: []
		};

		// グラフデータのサンプル

		// line
		$scope.graph.type = 'line';
		$scope.graph.index = 'click';
		$scope.graph.data = [
			{x: '2014-11-12', アフラック株式会社: 2210035, リクルート: 7890330, DeNA: 4012348},
			{x: '2014-11-13', アフラック株式会社: 1210035, リクルート: 7890330, DeNA: 4012348},
			{x: '2014-11-14', アフラック株式会社: 3210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-15', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-16', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-17', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-18', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-19', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-20', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-21', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-22', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-23', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-24', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
			{x: '2014-11-25', アフラック株式会社: 4210035, リクルート: 7890330, DeNA: 5012348},
		];

		// change Data
		$timeout(function () {
			$scope.graph.index = 'cv';
			$scope.graph.data = [
				{x: '2014-12-22', アフラック株式会社: 200, リクルート: 200, DeNA: 400},
				{x: '2014-12-23', アフラック株式会社: 100, リクルート: 300, DeNA: 400},
				{x: '2014-12-24', アフラック株式会社: 300, リクルート: 200, DeNA: 500},
				{x: '2014-12-25', アフラック株式会社: 400, リクルート: 100, DeNA: 500},
				{x: '2014-12-26', アフラック株式会社: 400, リクルート: 100, DeNA: 500},
			];
		}, 5000);

		// // bar
		$timeout(function () {
			$scope.graph.type = 'bar';
			$scope.graph.data = [
				{x: 'アフラック株式会社', cost: 20120, cvr: 0.031, cpa: 4200},
				{x: 'リクルートキャリア // 転職情報ならリクルート', cost: 11230, cvr: 0.035, cpa: 7412},
				{x: 'じゃらんゴルフ || 全国ゴルフ場', cost: 43300, cvr: 0.013, cpa: 14320},
				{x: 'NECアクセステクニカ株式会社（マーケティング）', cost: 35300, cvr: 0.0264, cpa: 1986},
			];
		}, 10000);

		// donut
		$timeout(function () {
			$scope.graph.type = 'donut';
			$scope.graph.title = 'CV';
			$scope.graph.data = [
				['アフラック株式会社', 400],
				['リクルートキャリア // 転職情報ならリクルート', 400],
				['じゃらんゴルフ || 全国ゴルフ場', 1200, 1000, 23,],
				['NECアクセステクニカ株式会社（マーケティング）',  50, 2312, 22],
			];
		}, 15000);
}]);


