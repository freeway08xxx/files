/* Services */

var app = angular.module( 'mypage.services', []);

/**
 * マイページサービス レポート
 */
app.service('mypageManagerService', ['$q', '$http', function($q, $http) {
	return {
		/* レポートデータ取得 */
		getReport : function(username,month,isSkipRedis) {
			var params = {
				report_type: 'daily',
				position_id: ['1', '4'],
				summary_type: 'client',
				'start_date[]': [],
				'end_date[]': [],
				use_forecast: true,
				use_aim_list: true,
				use_product_summary: true,
				filter_selected: 'user_name',
				search_user_name: username,
				export_type: 'display',
				term_count: '1',
				term_set: '',
				term:month,
				isSkipRedis:isSkipRedis
			};

			var yesterday = moment().subtract(1, 'days');
			var lastmonth = moment().subtract(1, 'month');
			lastmonth.date(lastmonth.daysInMonth());

			var f = 'YYYY/MM/DD';
			var term_list = {
				thismonth: {
					end_date:   yesterday.format(f),
					start_date: yesterday.date(1).format(f)
				},
				lastmonth: {
					end_date:   lastmonth.date(lastmonth.daysInMonth()).format(f),
					start_date: lastmonth.date(1).format(f)
				}
			};
			params['start_date[]'].push(term_list[month].start_date);
			params['end_date[]'].push(term_list[month].end_date);
            params['term'] = month;

			return $http({
				url: '/sem/new/mypage/report/summarydata',
				method: 'GET',
				params: params,
				ignoreLoadingBar: true
			});
		},
		/* ログインユーザー名取得 */
		getUserName : function() {
			return $http({
				url: '/sem/new/mypage/report/username', 
				method: 'GET',
				ignoreLoadingBar: true
			});
		},

		/* お知らせ取得 */
		getInformation : function() {
			return $http({
				url: '/sem/new/mypage/report/information', 
				method: 'GET',
				ignoreLoadingBar: true
			});
		},

		/* keyword情報取得 */
		getKeywordData : function(media) {
			var params = {
				media:media
			};
			return $http({
				url: '/sem/new/mypage/report/keyword_report',
				method: 'GET',
				params: params
			});
		}
	};
}]);

