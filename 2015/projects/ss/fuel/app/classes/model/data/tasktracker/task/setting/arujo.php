<?php

class Model_Data_Tasktracker_Task_Setting_Arujo extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_task_setting_arujo";
	protected static $_properties = array(
		'id',
		'task_setting_id',
		'task_master_arujo_id',
		'task_master_arujo_client_id',
		'media_id',
		'name',
		'value',
		'arujo_description',
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
	public static function insert($values) {
		$columns = array(
			'task_setting_id',
			'task_master_arujo_id',
			'task_master_arujo_client_id',
			'name',
			'value',
			'arujo_description',
			'media_id',
		);
		$query = DB::insert(self::$_table_name, $columns);
		foreach ($values as $value) {
			$query->values($value);
		}

		return $query->execute("administrator");
	}

	/**
	 * insert_deplicate
	 * 
	 * @param mixed $values 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function insert_duplicate($values) {
		$deplicate = array();
		$columns = array_keys($values[0]);
		$columns[] = 'created_at';
		$columns[] = 'created_user';

		$query = DB::insert(self::$_table_name, $columns);
		foreach ($values as $value) {
			$query->values($value);
		}

		foreach($columns as $column){
			if($column != 'id'){
				$deplicate[] = "${column} = VALUES(${column})";
			}
		}

		$query->set_duplicate($deplicate);
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
			->where('id',$id);
		return $query->execute("readonly")->current();
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
