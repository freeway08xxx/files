<?php
require_once APPPATH . '/const/main.php';
require_once APPPATH . '/const/common/report.php';
require_once APPPATH . '/const/axis.php';

class Controller_Axis_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = '/sem/quickview/quickview.php';

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'Axis Report');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'axis';

			## ページ固有CSS,JS
			$this->css = array(
				'c3/c3.min.css',
				'axis/main.css',
			);
			$this->js = array(
				'common/module/client-combobox.js',
				'common/module/termdate.js',
				'd3/d3.min.js',
				'c3/c3.min.js',
				'axis/app.js',
				'axis/controllers.js',
				'axis/directives.js',
				'axis/services.js',
				'axis/filters.js',
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('axis/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
