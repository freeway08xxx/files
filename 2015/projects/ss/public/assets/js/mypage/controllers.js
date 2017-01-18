/* Controllers */

/* MypageBaseCtrl as baseCtrl */
var controller = angular.module('mypage.controllers.base', []);
controller.controller('MypageBaseCtrl', ['$scope','$rootScope','mypageConst','$routeParams',function($scope,$rootScope,mypageConst,$routeParams) {
	var _this = this;

	//base settings
	this.master_data = [];
	this.models      = {info: {},report: {msg: ''},isEmptyData:{thismonth:false,lastmonth:false }};
	this.isLastMonth = mypageConst.isLastMonth[2];
	this.diff        = {
		visible     : mypageConst.diff.visible[1]
	};
	this.isloaded    = {
		thismonth   : mypageConst.isloaded.thismonth[2],
		lastmonth   : mypageConst.isloaded.lastmonth[2]
	};
	this.sortItems   = {
		adType      : mypageConst.sortItems.adType['search'],
		place       : mypageConst.sortItems.place['cost'],
		isDesc      : mypageConst.sortItems.isDesc[2]
	};
	this.settings    = {
		tab         : mypageConst.settings.tab['summary']
	}
}]);



/* MypageReportCtrl as reportCtrl */
var controller = angular.module('mypage.controllers.report', []);
controller.controller('MypageReportCtrl',['$scope','mypageManagerService','$routeParams',function($scope,mypageManagerService, $routeParams) {
	var baseScope = angular.element($('#js-scope_base')).scope().baseCtrl;
	var _this = this;

	//データ更新
	this.update = function() {
		var username    = baseScope.models.report.thismonth.user_name;
		var isSkipRedis = true;
		delete baseScope.models.report.thismonth;
		delete baseScope.models.report.lastmonth;

		baseScope.isloaded = {
			thismonth:false,
			lastmonth:false
		};
		_this.viewReport(username,'thismonth',isSkipRedis);
		_this.viewReport(username,'lastmonth',isSkipRedis);
	};



	//初回ロード
	this.init = function() {
		mypageManagerService.getUserName().then(function(res) {
			var username = res.data[0];

			_this.viewReport(username,'thismonth',false);
			_this.viewReport(username,'lastmonth',false);
			$scope.orderBy('cost');

			//お知らせ
            mypageManagerService.getInformation().then(function(res_obj) {
				baseScope.models.info = res_obj.data.info;
            }, function(error) {
                baseScope.models.report.msg.push('お知らせ取得に失敗しました: ' + error);
            });
		});
	};


    //データ取得表示
    this.viewReport = function (username,month,isSkipRedis){
        mypageManagerService.getReport(username,month,isSkipRedis).then(function(res_obj) {
            console.log(res_obj.data)

            baseScope.models.report[month] = res_obj.data;
			baseScope.master_data[month]  = angular.copy(res_obj.data);

            //表示データがない場合
            if(typeof baseScope.isloaded[month] == "undefined" ||typeof res_obj.data.results == "undefined" || res_obj.data == ""){
                baseScope.models.isEmptyData[month] = true;
                baseScope.isloaded[month]           = true;
            }
            console.log('End async get '+ month);


        }, function(error) {
            baseScope.models.report.msg.push('レポートデータ取得に失敗しました: ' + error);
        });
    };
}]);



/* MypageGraphCtrl as graphCtrl*/
var controller = angular.module('mypage.controllers.graph', []);
controller.controller('MypageGraphCtrl', ['$scope', '$routeParams',function($scope, $routeParams) {
	//Graph setting
	this.setting = {
		data: {
			type: 'line',
			json:[],
			keys: {
				x: 'x',
				value: [],
			},
			axes: {
				value: 'y',
				Total: 'y2'
			}
		},
		axis: {
			x: {
				type: 'timeseries',
				tick: {
					multiline: false,
					format: '%Y/%m/%d',
					rotate: 70
				},
				height: 80
			},
			y: {
				label: {
					text: 'Daily cost',
					position: 'outer-middle'
				},
				tick: {format: function (d) { return "¥" +  Math.round(d).toLocaleString(); }}
			},
			y2: {
				show: true,
				label: {
					text: 'Total',
					position: 'outer-middle'
				},
				tick: {format: function (d) { return "¥" + Math.round(d).toLocaleString(); }}
			}
		},
		grid: {
			x: {
				show: true,
				lines: [{value: moment().format('YYYY-MM-DD'), class: 'grid4', text: 'Today'}]
			}
		}
	};

}]);

/* MypageKeywordCtrl as keywordCtrl */ 
var controller = angular.module('mypage.controllers.keyword', []);
controller.controller('MypageKeywordCtrl', ['$scope','mypageManagerService','mypageConst',function($scope,mypageManagerService,mypageConst) {
	/* settings */
	var _this = this;
	_this.models = {
		term:{},
		google:{my_keywords:{},keyword_data:[],my_clients:{}},
		yahoo :{my_keywords:{},keyword_data:[]
		},
	};

	/* 取得表示 */
	_this.get_keyword = function(){

		//登録画面の表示リピートで使用する空のテンプレを10個用意
		for (var i = 1; i <= mypageConst.myKeywords.max; i++) {
			this.models.google.my_keywords[i] = {"client_info": "","keyword": "", "match_type" : ""}
		};

		_this.models.isDataEmpty = false;

		mypageManagerService.getKeywordData("MEDIA_ID_GOOGLE").then(function(res_obj) {

			//mykeywordにテンプレをextend
			$.extend(_this.models.google.my_keywords, res_obj.data.my_keywords);
			_this.models.google.my_clients   = res_obj.data.my_clients;
			_this.models.google.keyword_data = res_obj.data.keyword_data;
			_this.models.term                = res_obj.data.term;

			mypageManagerService.getKeywordData("MEDIA_ID_YAHOO").then(function(res) {
				_this.models.yahoo = res.data;

				//表示データなしの場合
				if(typeof res_obj.data.keyword_data == "undefined" || ( _this.models.google.keyword_data.length  == 0 && _this.models.yahoo.keyword_data.length == 0 ) ){
					_this.models.isDataEmpty = true;
				}
			});
		});
	}
	_this.get_keyword();
}]);

