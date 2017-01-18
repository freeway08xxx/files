<?php

class Controller_Quickmanage extends Controller_QuickManage_Base
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
		$this->view->set_filename('quickmanage/index');
	}
}
