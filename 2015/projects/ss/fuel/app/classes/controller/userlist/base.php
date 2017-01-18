<?php

require_once APPPATH . "/const/share.php";
require_once APPPATH . "/const/userlist.php";
/**
 * Service Base Controller.
 */
class Controller_UserList_Base extends Controller_Base
{
	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'ユーザリスト');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'dummy';

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/jquery.dataTables.css',
				'vendor/dataTables.tableTools.css',
				'vendor/dataTables.colVis.css',
				'vendor/multi-select.css',
				'userlist/main.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',
				'vendor/jquery.quicksearch.js',
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.tableTools.min.js',
				'vendor/dataTables.colVis.min.js',

				// jQuery Page Lib
				'vendor/jquery-ui-1.10.4.custom.min.js',
				'vendor/select2.js',

				// dummy Angular App
				'common/ng/dummy.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('userlist/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
