<?php

class Model_Data_Tasktracker_Process_Setting extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_process_setting";
	protected static $_properties = array(
		'id',
		'task_setting_id',
		'process_name',
		'process_description',
		'routine_setting',
		'priority',
		'main_process',
		'forecast_cost',
		'owner_user_id',
		'created_at',
		'created_user',
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
	 * get_by_task_setting_id
	 * 
	 * @param mixed $task_setting_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_task_setting_id($task_setting_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('task_setting_id',$task_setting_id);
		return $query->execute("readonly")->as_array();
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
			->where('id',$id);
		return $query->execute("readonly")->current();
	}


	/**
	 * delete_by_setting_id
	 * 
	 * @param int $setting_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function delete_by_setting_id($setting_id){
		$query = DB::delete(self::$_table_name)->where('task_setting_id','=',$setting_id);
		return $query->execute("administrator");
	}
}
