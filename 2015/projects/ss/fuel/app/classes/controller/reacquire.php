<?php

class Controller_Reacquire extends Controller_Reacquire_Base
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
		$this->view->set_filename('reacquire/index');
	}

}
