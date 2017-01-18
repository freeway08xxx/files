<?php

class Controller_Eagle_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/eagle/";

	public function before() {
		parent::before();

		\Lang::load('main');

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'Eagle');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'eagle';

			## ページ固有CSS,JS
			$this->css = array(
				'eagle/main.css',
				'angular-ui-grid/ui-grid.min.css',
				//'vendor/angular-ui/select.css',
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.fixedColumns.min.js',
				'angular-ui-utils/ui-utils.min.js',
				'angular-ui-grid/ui-grid.min.js',
				//'vendor/moment.js',
				'eagle/app.js',
				'eagle/controllers.js',
				'eagle/directives.js',
				'eagle/filter.js',
				'eagle/services.js',
				'common/module/client-combobox.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('eagle/navi');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
