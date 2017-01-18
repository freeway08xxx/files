<?php
require_once APPPATH . "/const/main.php";
require_once APPPATH . "/const/tasktracker.php";

class Controller_Tasktracker_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/eagle/";

	public function before() {
		parent::before();

		\Lang::load('main');

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'Tasktracker');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'tskt';

			## ページ固有CSS,JS
			$this->css = array(
				'tasktracker/main.css',
				'fullcalendar/fullcalendar.css',
			);
			$this->js = array(
				'angular-animate/angular-animate.js',
				'angular-ui-router/angular-ui-router.min.js',
				'angular-ui-calendar/calendar.js',
				'fullcalendar/fullcalendar.min.js',
				'fullcalendar/gcal.js',
				'tasktracker/app.js',
				'tasktracker/controllers/base.js',
				'tasktracker/controllers/task.js',
				'tasktracker/controllers/top.js',
				'tasktracker/controllers/setting.js',
				'tasktracker/controllers/admin.js',
				'tasktracker/directives.js',
				'tasktracker/filter.js',
				'tasktracker/services/common.js',
				'tasktracker/services/task.js',
				'tasktracker/services/top.js',
				'tasktracker/services/setting.js',
				'tasktracker/services/admin.js',
				'common/module/client-combobox.js',
			);

			$this->app_const = Util_Tasktracker_Common::get_app_const();
		}

		## ページタイトル横ナビゲーション 不要の場合は削除
		$this->content_nav = View::forge('tasktracker/navi');
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
