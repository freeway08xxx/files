<?php

class Controller_Client extends Controller_Client_Base
{

	/**
	 * action_index
	 *
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		//view へ出力
		$this->view->set_filename('client/index');
	}
}
