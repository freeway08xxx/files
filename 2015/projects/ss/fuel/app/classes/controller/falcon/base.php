<?php

require_once APPPATH."/const/common/report.php";
require_once APPPATH."/const/falcon.php";

/**
 * Service Base Controller.
 */
class controller_Falcon_base extends Controller_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'Falcon Report');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'falcon';

			## ページ固有CSS,JS
			$this->css = array(
				'falcon/main.css'
			);
			$this->js = array(
				'falcon/app.js',
				'falcon/controllers.js',
				'falcon/directives.js',
				'falcon/filters.js',
				'falcon/services.js',

				// app-module
				'common/module/client-combobox.js',
				'common/module/termdate.js'
			);

			# ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('falcon/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
