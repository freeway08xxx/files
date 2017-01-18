<?php

class Model_Data_Tasktracker_Process_Master extends \Model {

	// テーブル名
	protected static $_table_name = "m_tasktracker_process_master";
	protected static $_properties = array(
		'id',
		'task_master_id',
		'process_name',
		'priority',
		'main_process',
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
	 * update_by_task_master_id
	 * 
	 * @param mixed $task_master_id 
	 * @param mixed $set 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_by_task_master_id($task_master_id, $set){
		$query = DB::update(self::$_table_name);
		$query->set($set);
		$query->where('task_master_id', '=', $task_master_id);
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
	 * get_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_by_id($id) {
		$query = DB::select()
		->from(self::$_table_name)
		->where('id','=',$id);

		return $query->execute("readonly")->current();
	}

	/**
	 * get_by_task_master_id
	 * 
	 * @param mixed $task_master_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_task_master_id($task_master_id) {
		$query = DB::select()
		->from(self::$_table_name)
		->where('task_master_id', '=', $task_master_id);

		return $query->execute("readonly")->as_array();
	}
}
