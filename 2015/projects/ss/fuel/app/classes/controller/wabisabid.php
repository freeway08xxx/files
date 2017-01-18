<?php

class Controller_Wabisabid extends Controller_Wabisabid_Base
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
		$this->view->set_filename("wabisabid/index");
	}
}
