<?php

class Model_Data_Tasktracker_Task_Setting_Account extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_task_setting_account";
	protected static $_properties = array(
		'id',
		'task_setting_id',
		'media_id',
		'account_id',
		'created_at',
	);

	/**
	 * insert
	 * 
	 * @param mixed $values 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function insert($values) {
		$columns = array('task_setting_id', 'media_id', 'account_id');
		$query = DB::insert(self::$_table_name, $columns);
		foreach ($values as $value) {
			$query->values($value);
		}

		return $query->execute("administrator");
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

	/**
	 * get_by_task_setting_id
	 * 
	 * @param int $task_setting_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_task_setting_id($task_setting_id) {
		$account_table = 'mora.account';
		$query = DB::select(
			self::$_table_name.'.id as id',
			'task_setting_id',
			self::$_table_name.'.media_id as media_id',
			'account_name',
			'account_id',
			'created_at'
		)
			->from(self::$_table_name)
			->join($account_table, 'INNER')->on(self::$_table_name.'.account_id', '=', $account_table.'.id')
			->and_on(self::$_table_name.'.media_id', '=', $account_table.'.media_id')
			->where('task_setting_id',$task_setting_id);
		return $query->execute("readonly")->as_array();
	}
}
