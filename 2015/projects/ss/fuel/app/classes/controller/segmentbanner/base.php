<?php

class Controller_Segmentbanner_Base extends Controller_Base {

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'セグメントバナー生成');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'dummy';

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/multi-select.css',
				'segmentbanner/main.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',

				// dummy Angular App
				'common/ng/dummy.js',

				'segmentbanner/segmentbanner-main.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			// $this->content_nav = View::forge('userlist/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
