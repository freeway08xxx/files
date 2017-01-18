<?php
/**
 * Service Base Controller.
 */
class Controller_Mypage_Base extends Controller_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/new/mypage/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'マイページ');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'mypage';

			## ページ固有CSS,JS
			$this->css = array(
				'c3/c3.min.css',
				'mypage/main.css'
			);
			$this->js = array(
				'd3/d3.min.js',
				'c3/c3.min.js',
				'mypage/app.js',
				'mypage/controllers.js',
				'mypage/directives.js',
				'mypage/filters.js',
				'mypage/services.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			// $this->content_nav = View::forge('mypage/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
