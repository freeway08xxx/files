<?php

class Controller_EditManager_QueryConnector_Base extends Controller_Base
{
	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'Query Connector');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'queryConnector';

			## ページ固有CSS,JS
			$this->css = array(
				'editmanager/main.css'
			);
			$this->js = array(
				'editmanager/queryconnector/queryconnector-main.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('editmanager/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
