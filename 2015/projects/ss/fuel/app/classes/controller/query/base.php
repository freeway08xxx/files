<?php

require_once APPPATH."/const/share.php";
/**
 * Service Base Controller.
 */
class Controller_Query_Base extends Controller_Base {

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'クエリターゲティング');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'dummy';

			## ページ固有CSS,JS
			$this->css = array(
				'query/main.css'
			);

			## 固有JS -> 各ページのController

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('query/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
