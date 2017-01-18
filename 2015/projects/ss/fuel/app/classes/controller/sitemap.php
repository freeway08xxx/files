<?php

class Controller_Sitemap extends Controller_Sitemap_Base
{

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
