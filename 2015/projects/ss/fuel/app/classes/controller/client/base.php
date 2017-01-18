<?php

require_once APPPATH . "/const/client.php";

class Controller_Client_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/client/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'クライアント設定');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'client';

			## ページ固有CSS,JS
			$this->css = array(
				'client/main.css'
			);
			$this->js = array(
				'client/app.js',
				'client/base/controllers.js',
				'client/base/services.js',
				'client/conversion/controllers.js',
				'client/conversion/directives.js',
				'client/conversion/filters.js',
				'client/conversion/services.js',
				'client/mediacost/controllers.js',
				'client/mediacost/directives.js',
				'client/mediacost/filters.js',
				'client/mediacost/services.js',

				// app-module
				'common/module/client-combobox.js',

				// jQuery Module
				'vendor/select2.js'
			);
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
