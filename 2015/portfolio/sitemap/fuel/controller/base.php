<?php
/**
 * Service Base Controller.
 */
class Controller_Sitemap_Base extends Controller_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/new/sitemap/";

	public function before() {
		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'サイトマップ');
			## AngularJS AppName
			$this->template->ngapp_name = 'sitemap';

			## ページ固有CSS,JS
			$this->css = array(
				'sitemap/main.css'
			);
			$this->js = array(
				'sitemap/app.js',
				'sitemap/controllers.js'
			);

		}
	}

	public function after($response) {
		return parent::after($response);
	}



	/**
	 * コンテンツ情報のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		//view へ出力
		$this->view->set_filename('sitemap/index');
	}

}
