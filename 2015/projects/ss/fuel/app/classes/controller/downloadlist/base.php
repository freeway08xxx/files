<?php

class Controller_DownloadList_Base extends Controller_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/downloadlist/export";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', '処理結果一覧');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'downloadlist';

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/jquery.dataTables.css',
				'downloadlist/main.css'
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',

				// jQuery Page Lib
				'vendor/jquery-ui-1.10.4.custom.min.js',
				'vendor/select2.js',

				'downloadlist/app.js',
				'downloadlist/controllers.js',
				'downloadlist/services.js',
				'downloadlist/directives.js',
				'downloadlist/filters.js',
				'downloadlist/downloadlist-main.js'
			);

		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
