/* Services */

var app = angular.module( 'ssTemplate.services', [] )

/**
 * フォームページサービス
 */
app.service('bstmplManagerService', ['$q', '$http', 'ssModal', function($q, $http) {
	var deferred = null;
	return {

		/* フォームページ情報取得 */
		getFrom : function(basicId) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/basic/form/data',
				params: basicId ? {id:basicId} : null,
				method: 'GET'
			}).success(function(res) {
				//resolve
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/* フォームページ情報登録 */
		saveForm : function(data) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/basic/form/save',
				method: 'POST',
				data:data,
				headers:{"Content-type":undefined,"enctype":'multipart/form-data'},
			    transformRequest: null
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},

		/* テーブル情報取得 */
		getTable : function() {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/basic/table/datas',
				method: 'GET'
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
	}
}])
//フォームページStore
.service('bstmplFormStore', ['$q', '$filter', 'bstmplManagerService', function($q, $filter, bstmplManagerService) {

	var deferred = null;
	var models = {
		formModel : {},
		subjectsModel : {},
		suportsModel : {},
	};

	/**
	 * Form情報Model 
	 */
	function BstmplFormModel(id, basicData) {

		/**
		 * AngularJsで利用するModelを生成
		 * 特にcheckboxなどのng-modelは利用するdirectiveによって変化するので注意が必要
		 */


		this.id = id;
		this.text = {};
		this.select = {};
		this.checkbox = {button:{}};
		this.radio = {};
		this.file = {};

		//form情報がない
		if(!SsArrUtil.isDefined(basicData, 'form')){
			return false;
		}

		//マスター情報がない
		if(!models.subjectsModel && !models.suportsModel){
			return false;
		}


		var formData = basicData.form;

		//checkboxをシリアライズ(ノーマル)
		if(SsArrUtil.isDefined(formData, 'checkbox.normal')){
			tmpCheckboxNormal = Array();
			for(index in formData.checkbox.normal) {
				filter_checkbox = $filter('filter')(models.suportsModel, {id:formData.checkbox.normal[index]}, true);
				angular.forEach(filter_checkbox, function (val, key) {
					tmpCheckboxNormal.push(val);
				});
			}
			formData.checkbox.normal = tmpCheckboxNormal;
		}

		//checkboxをシリアライズ(ノーマル)
		if(SsArrUtil.isDefined(formData, 'checkbox.button')){
			tmpCheckboxButton = {};
			for(index in formData.checkbox.button) {
				filter_checkbox = $filter('filter')(models.suportsModel, {id:formData.checkbox.button[index]}, true);
				angular.forEach(filter_checkbox, function (val, key) {
					tmpCheckboxButton[val.id] = val;
				});
			}
			formData.checkbox.button = tmpCheckboxButton;
		}

		if(SsArrUtil.isDefined(basicData, 'file_name')){
			formData.file = {
				name : basicData.file_name
			}
		}

		for(key in formData){
			if(formData.hasOwnProperty(key)){
				this[key] = formData[key];
			}
		}
	}

	/**
	 * model情報からサーバーへpostするデータの生成 
	 */
	BstmplFormModel.prototype.getPostData = function(){

		var postData = new FormData();

		//checkboxをシリアライズ(normal)
		if(SsArrUtil.isDefined(this, 'checkbox.normal')){
			var tmp_normal = Array();
			angular.forEach(this.checkbox.normal, function (val, key) {
				if (val != null) tmp_normal.push(val.id);
			});
			this.checkbox.normal = tmp_normal;
		}
		//checkboxをシリアライズ(button)
		if(SsArrUtil.isDefined(this, 'checkbox.button')){
			var tmp_button = Array();
			angular.forEach(this.checkbox.button, function (val, id) {
				if (val != null) tmp_button.push(val.id);
			});
			this.checkbox.button = tmp_button;
		}

		
		//※ angularの仕様上オブジェクトは自動的にjsonになるが、
		//fileを送信する場合、挙動が変わる可能性があるので
		//手動でjsonの文字列として送る
		postData.append("form" , angular.toJson(this));

		//idある場合をそれも送る
		if(this.id){
			postData.append("id" , this.id);
		}

		//file更新がある場合をそれも送る
		if(this.file){
			postData.append("file" , this.file);
		}

		return postData;
	}

	return {
		/**
		 * Formで利用するmodel情報を取得する
		 */
		getModels : function(id) {
			this.release();
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();

			//サーバー取得
			bstmplManagerService.getFrom(id).then(function(res){
				if(SsArrUtil.isDefined(res, 'master.subject')){
					models.subjectsModel = res.master.subject;
				}
				if(SsArrUtil.isDefined(res, 'master.sports')){
					models.suportsModel = res.master.sports;
				}

				models.formModel = new BstmplFormModel(id, res.basic);

				deferred.resolve(models);
			},function(error){
				deferred.reject();
			});

			return deferred.promise;
		},

		/**
		 * Formで変更したmodelを登録する 
		 */
		saveForm : function() {
			postData = models.formModel.getPostData();
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			bstmplManagerService.saveForm(postData).then(function(res){
				deferred.resolve(res);
			},function(error){
				deferred.reject();
			});

			return deferred.promise;
		},

		/**
		 * 残っているオブジェクトの削除を行う 
		 */
		release : function() {
			models = {};
		},
	}
}])

.service('bstmplTableStore', ['$q', '$filter', 'bstmplManagerService', function($q, $filter, bstmplManagerService) {

	var deferred = null;
	return {
		getModels : function() {
			models = {
				tableModels : []
			};
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			bstmplManagerService.getTable().then(function(res){
				if(SsArrUtil.isDefined(res, 'basics')){
					for(key in res.basics){
						basic = res.basics[key];
						models.tableModels.push(basic);
					}
				}
				deferred.resolve(models);
			});

			return deferred.promise;
		},
	}

}]);



