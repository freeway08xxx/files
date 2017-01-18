<?php

class Controller_Support_Api_Quota_Ydn extends Controller_Support_Api_Quota_Base
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
		$this->view->set_filename('support/api/quota/ydn/index');	
		$this->response($this->view);
	}

	public function get_list()
	{
		$response['quotas'] = Model_Data_Quota_Ydn::get_all();
		return $this->response($response); 
	}
}
