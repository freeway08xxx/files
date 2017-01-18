<?php

class Model_Data_Tasktracker_Task_Master extends \Model {

	// テーブル名
	protected static $_table_name = "m_tasktracker_task_master";
	protected static $_properties = array(
		'id',
		'task_name',
		'task_category_id',
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
	 * get_by_category_id
	 * 
	 * @param mixed $category_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_category_id($category_id) {
		$query = DB::select()
		->from(self::$_table_name)
		->where('task_category_id', '=', $category_id);

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
}
