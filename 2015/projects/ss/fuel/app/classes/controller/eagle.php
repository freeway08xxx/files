<?php

class Controller_Eagle extends Controller_Eagle_Base
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
		$this->view->set_filename('eagle/index');	
	}
}
