<?php

require_once APPPATH . "/const/common/report.php";
require_once APPPATH . "/const/quickmanage.php";

class Controller_QuickManage_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/quickmanage/quickmanage.php";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'QuickManage');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'quickManage';

			## ページ固有CSS,JS
			$this->css = array(
				'quickmanage/main.css'
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.fixedColumns.min.js',
				'quickmanage/app.js',
				'quickmanage/controllers.js',
				'quickmanage/directives.js',
				'quickmanage/services.js',

				// app-module
				'common/module/termdate.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('quickmanage/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
