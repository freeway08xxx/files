<?php

class Controller_Api_ExtCv extends Controller_Api_Base
{

	/**
	 * idから取得 
	 * 
	 * @param int $purpose_id 
	 * @access public
	 */
	public function get_index($client_id)
	{
		$response = array();
		if(empty($client_id)){
			return $this->response( $response); 
		}

		$response['ext_cv'] = \Model_Susie_CvNameAccount::get_client_tool($client_id);
		return $this->response( $response); 
	}
}
