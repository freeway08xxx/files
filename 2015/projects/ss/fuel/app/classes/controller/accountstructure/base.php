<?php

require_once APPPATH . "/const/accountstructure/common.php";

class Controller_AccountStructure_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/accountstructure/";

	public function before() {

		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'アカウント設定内容取得');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'accountStructure';

			## ページ固有CSS,JS
			$this->css = array(
				'accountstructure/main.css',
				'vendor/multi-select.css',
				'basic/main.css'
			);
			$this->js = array(
				'accountstructure/app.js',
				'accountstructure/controllers.js',
				'accountstructure/services.js',
				'common/module/client-combobox.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('accountstructure/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
