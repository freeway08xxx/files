/* Controllers */
var controller = angular.module('tskt.controllers.base', []);
/* ベース */
controller.controller('TsktBaseCtrl', ['$scope','appConst', 'broadcast_names', 
	function($scope, appConst, broadcast_names) {
		$scope.appConst = appConst;
		$scope.appConst.broadcast_names = broadcast_names;
		var _this = this;
	}
]);

controller.controller('TsktUserResourceCtrl', ['userId', '$modalInstance', '$scope','$timeout', '$compile', 'TsktUserResourceService', 
	function(userId, $modalInstance, $scope, $timeout, $compile, TsktUserResourceService) {
		$scope.cancel = function () {
			$modalInstance.dismiss();
		};
		$scope.models = {};
		$scope.calendarOptions = {
			height: 550,
			allDaySlot:false,				//全日スロット非表示
			firstHour:10,
			minTime: "06:00:00",			//最小時間
			slotMinutes : 15,				//時間間隔
			axisFormat: 'HH時',				//列区切り時間フォーマット
			defaultView: 'agendaDay',		//初回表示(日)
			slotEventOverlap: false,		//時間が重なった時の表示
			defaultEventMinutes:60,			//初期イベントコスト
			header:{
				left: 'prev,next today',
				center: 'title',
				right: 'month agendaWeek agendaDay'
			},
			timeForma: {
				agenda: 'h(:mm)',
				'': 'h(:mm)'
			},
			titleFormat: {
				month: 'yyyy年M月',                             // 2013年9月
				week: "yyyy年M月d日{ ～ }{[yyyy年]}{[M月]d日}", // 2013年9月7日 ～ 13日
				day: "yyyy年M月d日'('ddd')'"                  // 2013年9月7日(火)
			},
			columnFormat: {
				month: 'ddd',    // 月
				week: "d'('ddd')'", // 7(月)
				day: "d'('ddd')'" // 7(月)
			},
			buttonText: {
				prev:     '&lsaquo;', // <
				next:     '&rsaquo;', // >
				prevYear: '&laquo;',  // <<
				nextYear: '&raquo;',  // >>
				today:    '今日',
				month:    '月',
				week:     '週',
				day:      '日'
			},
			/*
			eventRender: function(event, element) {
				element.attr({'tooltip': event.title,
					'tooltip-append-to-body': true});
				$compile(element)($scope);
			}
			*/
			/*
			eventMouseover: function(event, jsEvent, view) {
				var element = jsEvent.target;
			}
			*/
		};
		$scope.userEvents = [];
		$scope.eventSources = [$scope.userEvents];

		TsktUserResourceService.getUserResourceData(userId).then(function(res){
			if(!res.data){
				return false;
			}
			$scope.user = res.data.user;
			for(k in res.data.process){
				var value = res.data.process[k];
				value.title = '['+value.process_id+']'+ '【'+ value.client_name + '】'+ value.process_name ;
				value.start = new Date(value.process_start_datetime);
				value.end 	= new Date(value.process_end_cost_datetime);
				value.allDay = false; 
				value.className = value.process_status == 2 ? 'status-comp' : 'status-incomp';
				$scope.userEvents.push(value);
			}
		});
	}
]);
