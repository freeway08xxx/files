<?php

class Controller_Support_Api_Quota_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/quickmanage/quickmanage.php";

	public function before() {
		parent::before();

		\Lang::load('main');

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'API使用量');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'support_api_quota';

			## ページ固有CSS,JS
			$this->css = array(
				'support/api/quota/main.css',
				'angular-ui-grid/ui-grid.min.css',
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.fixedColumns.min.js',
				'angular-ui-utils/ui-utils.min.js',
				'angular-ui-grid/ui-grid.min.js',
				'support/api/quota/app.js',
				'support/api/quota/controllers.js',
				'support/api/quota/directives.js',
				'support/api/quota/filter.js',
				'support/api/quota/services.js',
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('support/api/quota/navi');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
