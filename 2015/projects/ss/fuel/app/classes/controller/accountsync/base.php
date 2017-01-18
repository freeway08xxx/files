<?php

class Controller_Accountsync_Base extends Controller_Base {

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'アカウント同期実行');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'dummy';

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/multi-select.css',
				'vendor/jquery.dataTables.css',
				'vendor/jquery-ui.min.css',
				'vendor/jquery.ui.theme.css',

				'accountsync/main.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',
				'vendor/jquery.dataTables.min.js',

				// jQuery Page Lib
				'vendor/jquery-ui-1.10.4.custom.min.js',
				'vendor/select2.js',

				// dummy Angular App
				'common/ng/dummy.js',

				'accountsync/accountsync-main.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			// $this->content_nav = View::forge('accountsync/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
