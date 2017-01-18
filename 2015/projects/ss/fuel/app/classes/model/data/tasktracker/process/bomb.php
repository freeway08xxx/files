<?php

class Model_Data_Tasktracker_Process_Bomb extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_process_bomb";
	protected static $_properties = array(
		'id',
		'process_id',
		'bomb_description',
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
	 * get_by_process_id
	 * 
	 * @param mixed $process_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_process_id($process_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('process_id', $process_id);
		return $query->execute("readonly")->as_array();
	}
}
