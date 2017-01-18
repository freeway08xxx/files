<?php

class Controller_Support_Api_Quota extends Controller_Support_Api_Quota_Base
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
		$this->view->set_filename('support/api/quota/index');	
	}
}
