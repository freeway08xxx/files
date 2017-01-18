<?php
require_once APPPATH."/const/main.php";
require_once APPPATH."/const/share.php";
require_once APPPATH."/const/budget.php";

/**
 * Service Base Controller.
 */
class Controller_Reacquire_Base extends Controller_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/new/reacquire/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'レポート再取得');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'Reacquire';

			## ページ固有CSS,JS
			$this->css = array(
				'reacquire/main.css'
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.fixedColumns.min.js',

				// app
				'reacquire/app.js',
				'reacquire/controllers.js',
				'reacquire/directives.js',
				'reacquire/filters.js',
				'reacquire/services.js',

				// app-module
				'common/module/client-combobox.js',
				'common/module/termdate.js'
			);
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
