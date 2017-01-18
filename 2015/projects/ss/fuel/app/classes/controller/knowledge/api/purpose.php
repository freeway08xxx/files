<?php

class Controller_Knowledge_Api_Purpose extends Controller_Knowledge_Api_Base
{

	/**
	 * idから取得 
	 * 
	 * @param int $purpose_id 
	 * @access public
	 */
	public function get_index($purpose_id)
	{
		$purpose = Model_Mora_Knowledge_Purpose::find_one_by_id($purpose_id);
		return $this->response( array(
			'purpose_id' => $purpose_id,
			'purpose' => $purpose
		)); 
	}

	/**
	 * 新規追加 
	 * 
	 * @access public
	 */
	public function post_index()
	{
		$section = Input::json('type_section');
		$parent_purpose_id = Input::json('parent_purpose_id');
		$name = Input::json('name');

		if(empty($section) && empty($parent_purpose_id) && empty($name)){
			$result = array('message' => 'This paramater is required');
			return $this->response($result,404);
		}

		$result = Model_Mora_Knowledge_Purpose::insert_purpose($section, $parent_purpose_id, $name);
		if(empty($result[0])){
			$result = array('message' => 'Failed to save the purpose');
			return $this->response($result,404);
		}

		return $this->response( array(
			'purpose_id' => $result[0]
		)); 
	}

	/**
	 * sectionからすべて取得 
	 * 
	 * @param string $type_section 
	 * @access public
	 */
	public function get_all($type_section='SEM')
	{
		$purpose_all = Model_Mora_Knowledge_Purpose::find_by_section($type_section);

		return $this->response( array(
			'type_section' => $type_section,
			'purposes' => array_values($purpose_all),
		)); 
	}

	/**
	 * 自分が親とするpurpose一覧を取得 
	 * 
	 * @param int $purpose_id 
	 * @access public
	 */
	public function get_childs($purpose_id)
	{
		$purposes = Model_Mora_Knowledge_Purpose::find_by_parent_purpose_master_id($purpose_id);

		return $this->response( array(
			'parent_purpose_master_id' => $purpose_id,
			'purposes' => array_values($purposes),
		)); 
	}

	public function post_name($purpose_id)
	{
		if(empty($purpose_id)) return false;
		$purpose = Model_Mora_Knowledge_Purpose::find_one_by_id($purpose_id);
		if(empty($purpose)) return false;

		$name = Input::json('name');
		$update = Model_Mora_Knowledge_Purpose::update_purpose_name($purpose_id, $name);

		return $this->response( array(
			'purpose_id' => $purpose_id
		)); 
	}
}
