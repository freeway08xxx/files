<?php

require_once APPPATH."/const/share.php";
/**
 * Service Base Controller.
 */
class Controller_Basic_Base extends Controller_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/new/develop/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'FuelPHP & AngularJS テンプレート');

			## ロゴ画像を使用する場合、以下を有効にしてください
			$this->template->set_global('logo_img', '<img src="/sem/new/assets/img/logo.png">', false);

			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'ssTemplate';

			## ページ固有CSS,JS
			$this->css = array(
				'c3/c3.min.css',
				'basic/main.css'
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'vendor/dataTables.fixedColumns.min.js',

				// graph library
				'd3/d3.min.js',
				'c3/c3.min.js',

				// app
				'basic/app.js',
				'basic/controllers.js',
				'basic/directives.js',
				'basic/filters.js',
				'basic/services.js',

				// app-module
				'common/module/client-combobox.js',
				'common/module/graph.js'
			);

			## ページタイトル横ナビゲーション 不要の場合は削除
			$this->content_nav = View::forge('basic/nav');
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
