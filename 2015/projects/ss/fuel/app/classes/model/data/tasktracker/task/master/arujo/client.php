<?php

class Model_Data_Tasktracker_Task_Master_Arujo_Client extends \Model {

	// テーブル名
	protected static $_table_name = "m_tasktracker_task_master_arujo_client";
	protected static $_properties = array(
		'id',
		'client_id',
		'media_id',
		'name',
		'value',
		'arujo_description',
		'task_master_id',
		'task_master_arujo_id',
		'value',
		'created_at',
	);

	/**
	 * insert
	 * 
	 * @param mixed $set 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function insert($set) {
		$query = DB::insert(self::$_table_name)->set($set);
		return $query->execute("administrator");
	}

	/**
	 * update
	 * 
	 * @param mixed $id 
	 * @param mixed $set 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update($id, $set){
		$query = DB::update(self::$_table_name);
		$query->set($set);
		$query->where('id',"=", $id);
		return $query->execute("administrator");
	}

	/**
	 * delete_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function delete_by_id($id){
		$query = DB::delete(self::$_table_name)->where('id','=',$id);
		return $query->execute("administrator");
	}

	/**
	 * get_one_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_by_id($id) {
		$query = DB::select_array(self::$_properties)
		->from(self::$_table_name)
		->where('id', '=', $id);

		return $query->execute("readonly")->current();
	}

	/**
	 * get_by_arujo_id
	 * 
	 * @param mixed $task_master_arujo_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_arujo_id($task_master_arujo_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('task_master_arujo_id', '=', $task_master_arujo_id);
		return $query->execute("readonly")->as_array();
	}

	/**
	 * get_by_client_id
	 * 
	 * @param mixed $client_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_client_id($client_id, $task_master_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('client_id', '=', $client_id)
			->where('task_master_id', '=', $task_master_id);
		return $query->execute("readonly")->as_array();
	}
}
