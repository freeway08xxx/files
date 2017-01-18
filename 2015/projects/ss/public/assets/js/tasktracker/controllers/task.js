/* Controllers */
var controller = angular.module('tskt.controllers.task', []);

/* TsktTaskSaveCtrl as tsktTaskNew  */
controller.controller('TsktTaskSaveCtrl', ['$scope', 'tsktTaskSaveService', '$location', 'tsktUtil', 'tsktModelProvider', 'ssModal', 'tsktCommonService', 
	function($scope, tsktTaskSaveService, $location, tsktUtil, tsktModelProvider, ssModal, tsktCommonService) {
		var _this = this;
		_this.models = tsktModelProvider.taskSave.forge();

		_this.master = {
			taskMaster : null,
			arujoMaster: null,
			isSubmit: false,
			processUserlist: [],
			datepicker: {},
			media_list: $scope.appConst.media_list,
		};

		_this.services = {
			bindAction: {

				/**
				 * カテゴリ変更 
				 */
				changeCategory: function() {
					privateUtil.setMasterTaskMaster();
				},

				/**
				 * タスクマスター変更 
				 */
				changeMaster: function() {
					if(!_this.models.taskMaster || !_this.master.taskMaster){
						return false;
					}
					//タスク名の変更
					privateUtil.setModelTaskName();
					//ARUJOのセット
					privateUtil.setModelArujo();
					//プロセスのセット
					privateUtil.setModelProcess();
				},

				/**
				 * ARUJOの追加 
				 */
				clickAddArujo: function() {
					privateUtil.setAddArujo();
				},

				/**
				 * ARUJOの削除 
				 */
				removeArujo: function(row) {
					for(k in _this.models.arujoMaster){
						var value = _this.models.arujoMaster[k];
						if (value == row) {
							_this.models.arujoMaster.splice(k, 1);
							break;
						}
					}
				},

				/**
				 * プロセスの追加 
				 */
				clickAddProcessList: function() {
					processMasterObject = tsktUtil.Factory.createProcessEditObject({});
					processMasterObject.user_id = $scope.user.id;
					processMasterObject.isAdd = true;
					_this.models.processList.push(processMasterObject);
				},

				/**
				 * プロセスの削除 
				 */
				removeProcess: function(row) {
					for(k in _this.models.processList){
						var value = _this.models.processList[k];
						if (value == row) {
							_this.models.processList.splice(k, 1);
							break;
						}
					}
				},

				/**
				 * 業務種別を変更 
				 */
				changeJobType: function() {
					if(_this.models.jobType == "1"){
						_this.master.datepicker.isDateHide = false;
					}
					if(_this.models.jobType == "2"){
						_this.master.datepicker.isDateHide = true;
					}
				},

				/**
				 * ユーザータスクを開く  
				 */
				openUserTask: function(userId) {
					tsktCommonService.openUserResource(userId);
				},

				/**
				 * 登録 
				 */
				submit: function() {
					if(tsktTaskSaveService.validModels(_this.models)){
						// validation 問題なし
						ssModal.confirm('更新してもよろしいですか？').result.then(function(){
							tsktTaskSaveService.saveTask(_this.models).then(function(){
								tsktModelProvider.taskSave.clear();
								$location.url('/top');
							});
						});
					}else{
						// validation 問題あり
						$("body").scrollTop(0);

					}
				},
				
			},
		};

		var privateUtil = {
			setMasterTaskMaster: function() {
				if (_this.models.category) {
					tsktTaskSaveService.getTaskMaster(_this.models.category).then(function(taskMaster){
						_this.models.taskName = "";
						_this.processListTable.data = [];
						_this.arujoMasterTable.data = [];
						_this.master.taskMaster = taskMaster;
					});
				}
			},
			setModelTaskName: function() {
				// マスターを変更したらタスク名を反映させる
				angular.forEach(_this.master.taskMaster, function(value, key) {
					if(value.id == _this.models.taskMaster){
						_this.models.taskName = value.task_name;
					}
				});
			},
			setModelArujo: function() {
				if(_this.models.client && _this.models.taskMaster){

					// ARUJOのマスターを取得
					tsktTaskSaveService.getArujoMaster(_this.models.taskMaster, _this.models.client.id).then(function(arujoMaster){
						// masterをデータ整形
						for(k in arujoMaster){
							arujoMaster[k] = tsktUtil.Factory.createArujoObject(arujoMaster[k]);
						}
						_this.models.arujoMaster = arujoMaster;
						_this.arujoMasterTable.data = _this.models.arujoMaster;
					});
				}
			},
			setAddArujo: function() {
				var addArujo = tsktUtil.Factory.createArujoObject({});
				addArujo.isAdd = true;
				_this.models.arujoMaster.push(addArujo);
			},
			setModelProcess: function() {
				if(_this.models.taskMaster){
					// 設定可能ユーザー取得
					tsktTaskSaveService.getProcessUserlist().then(function(userlist){
						_this.master.processUserlist = userlist;
					});

					tsktTaskSaveService.getProcessMaster(_this.models.taskMaster).then(function(processMaster){
						//初期メインプロセス情報を保持
						for(k in processMaster){
							//初期は自分
							processMaster[k].user_id = $scope.user.id;

							//メインプロセス
							if(processMaster[k].main_process == "1"){
								_this.models.main_process = processMaster[k];
							}
						}
						// Modelsに保持
						_this.models.processList = processMaster;

						//テーブルのレコードに表示する
						_this.processListTable.data = _this.models.processList;
					});
				}
			}
		}


		// クライアントコンボボックス
		_this.clientCombobox = {};
		_this.clientComboboxConfig = {
			registerApi : function(api){
				_this.comboboxApi = api;
			}
		}

		// プロセス一覧
		_this.processListTable = tsktTaskSaveService.getProcessListTable();
		_this.processListTable.data = [];
		// ARUJOマスターテーブル
		_this.arujoMasterTable = tsktTaskSaveService.getArujoMasterTable();
		_this.arujoMasterTable.data = [];

		//クライアント選択後に飛んでくるメッセージ
		$scope.$on('ss_combobox_client_change', function(event, data) {
			_this.models.client = _this.clientCombobox.client;
			_this.models.accounts = []; 
			privateUtil.setModelArujo();
		});

		//アカウント選択後に飛んでくるメッセージ
		$scope.$on('ss_combobox_account_change', function(event, data) {
			_this.models.client = _this.clientCombobox.client;
			_this.models.accounts = _this.clientCombobox.accounts;
		});
	}
]);
