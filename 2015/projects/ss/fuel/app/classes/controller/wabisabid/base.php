<?php
require_once APPPATH . "/const/wabisabih.php";

class Controller_Wabisabid_Base extends Controller_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/wabisabid/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global("title", "WABISABI'd");
			## ロゴ画像を使用する場合、以下を有効にしてください
			$this->template->set_global('logo_img', '<img src="/sem/new/assets/img/logo_wabisabi.png">', false);
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = "wabisabid";

			## ページ固有CSS,JS
			$this->css = array(
				"wabisabid/main.css",
			);
			$this->js = array(
				"common/module/client-combobox.js",
				"wabisabid/app.js",
				"wabisabid/controllers.js",
				"wabisabid/directives.js",
				"wabisabid/filters.js",
				"wabisabid/services.js"
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge("wabisabid/nav");
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
