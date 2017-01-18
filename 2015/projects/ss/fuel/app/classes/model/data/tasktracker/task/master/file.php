<?php

class Model_Data_Tasktracker_Task_Master_File extends \Model {

	// テーブル名
	protected static $_table_name = "m_tasktracker_task_master_file";
	protected static $_properties = array(
		'id',
		'task_master_id',
		'type',
		'file_name',
		'created_at',
	);


	/**
	 * get_one_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_by_id($id) {
		$query = DB::select()
		->from(self::$_table_name)
		->where('id', '=', $id);

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
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('task_master_id',$task_master_id);
		return $query->execute("readonly")->as_array();
	}
}
