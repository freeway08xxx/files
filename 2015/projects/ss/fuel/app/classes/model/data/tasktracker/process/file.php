<?php

class Model_Data_Tasktracker_Process_File extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_process_file";
	protected static $_properties = array(
		'id',
		'process_id',
		'file_name',
		'created_at'
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
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('id',$id);
		return $query->execute("readonly")->current();
	}

	/**
	 * get_one_by_process_id
	 * 
	 * @param mixed $process_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_by_process_id($process_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('process_id',$process_id);
		return $query->execute("readonly")->current();
	}

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
	 * insert_duplicate
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
}
