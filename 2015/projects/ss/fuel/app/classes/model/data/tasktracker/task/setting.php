<?php

class Model_Data_Tasktracker_Task_Setting extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_task_setting";
	protected static $_properties = array(
		'id',
		'task_master_id',
		'task_name',
		'task_description',
		'client_id',
		'job_type',
		'routine_setting',
		'owner_user_id',
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
	 * get_one_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_by_id($id) {
		$query = DB::select_array(self::$_properties)->from(self::$_table_name)->where('id',$id);
		return $query->execute("readonly")->current();
	}
}
