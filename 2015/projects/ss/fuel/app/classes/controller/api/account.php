<?php

class Controller_Api_Account extends Controller_Api_Base
{

	/**
	 * idから取得 
	 * 
	 * @param int $purpose_id 
	 * @access public
	 */
	public function get_index($client_id)
	{
		$focus_media_ids = Input::param('focus_media_ids');

		$response = array();
		if(empty($client_id)){
			return $this->response( $response); 
		}

		$media_list = array(MEDIA_ID_YAHOO, MEDIA_ID_GOOGLE, MEDIA_ID_IM, MEDIA_ID_D2C, MEDIA_ID_X_LISTING);
		if(!empty($focus_media_ids)){
			$media_list = json_decode($focus_media_ids,true);
		}
		$response['accounts'] = Model_Mora_Account::get_by_client($client_id,$media_list);
		
		return $this->response( $response); 
	}
}
