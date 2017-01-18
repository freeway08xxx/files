<?php

class Controller_Api_Axis_DimensionParent extends Controller_Api_Base
{
	public function get_index($client_id)
	{
		$response = array();
		if(empty($client_id)){
			return $this->response($response); 
		}

		$response["dimension_parent"] = \Model_Data_Axis_DimensionParent::get_list_by_client($client_id);
		return $this->response($response); 
	}
}
