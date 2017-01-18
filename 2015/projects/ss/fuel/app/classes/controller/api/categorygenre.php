<?php

class Controller_Api_CategoryGenre extends Controller_Api_Base
{
	public $is_access_free = true;

	public function get_index($client_id)
	{
		$response = array();
		if(empty($client_id)){
			return $this->response($response); 
		}

		$response["category_genre"] = \Model_Data_CategoryGenre::get_for_client_id($client_id);
		return $this->response($response); 
	}
}
