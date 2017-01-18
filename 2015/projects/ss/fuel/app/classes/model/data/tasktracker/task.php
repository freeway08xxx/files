<?php

class Model_Data_Tasktracker_Task extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_task";
	protected static $_properties = array(
		'id',
		'task_setting_id',
		'task_status',
		'task_end_datetime',
		'task_limit_datetime',
		'owner_user_id',
		'created_at'
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
		$query = DB::insert(self::$_table_name, $columns);
		foreach ($values as $value) {
			$query->values($value);
		}
		foreach($columns as $column){
			$deplicate[] = "${column} = VALUES(${column})";
		}
		$query->set_duplicate($deplicate);
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
	public static function delete_by_id($id) {
		$query = DB::delete(self::$_table_name);
		if(is_array($id)){
			$query->where('id',"in", $id);
		}else{
			$query->where('id',"=", $id);
		}
		return $query->execute("administrator");
	}

	/**
	 * update
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update($id,$set) {
		$query = DB::update(self::$_table_name);
		$query->set($set);
		$query->where('id',"=", $id);
		return $query->execute("administrator");
	}


	/**
	 * update_status
	 * 
	 * @param mixed $id 
	 * @param mixed $status 
	 * @param mixed $end_datetime 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_status($id, $status, $end_datetime=null){
		$query = DB::update(self::$_table_name);
		$query->value('task_status', $status);
		$query->where('id',"=",$id);

		if(!empty($end_datetime)){
			$query->value('task_end_datetime', $end_datetime);
		}

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

	/**
	 * get_by_id
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one_last_by_setting_id_and_status($task_setting_id, $status) {
		$query = DB::select_array(self::$_properties)->from(self::$_table_name)
			->where('task_setting_id',$task_setting_id)
			->where('task_status',$status)
			->order_by('task_limit_datetime','desc')
			->offset(0)->limit(1);
		return $query->execute("readonly")->current();
	}

	/**
	 * 特定クライアントのタスクをステータスを元に取得 
	 * 
	 * @param array $client_ids 
	 * @param int $status 
	 * @param date $from_limit_date 
	 * @param date $to_limit_date 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_tasks_by_status($client_ids, $status, $from_limit_date=null, $to_limit_date=null) {
		$table_task_setting = 't_tasktracker_task_setting';
		$table_task_master = 'm_tasktracker_task_master';
		$query = DB::select(
			self::$_table_name.'.id as task_id',
			self::$_table_name.'.task_setting_id',
			$table_task_setting.'.task_master_id',
			$table_task_setting.'.task_name',
			$table_task_setting.'.task_description',
			$table_task_setting.'.client_id',
			$table_task_setting.'.job_type',
			$table_task_setting.'.routine_setting',
			$table_task_master.'.task_category_id',
			self::$_table_name.'.task_status',
			self::$_table_name.'.task_end_datetime',
			self::$_table_name.'.task_limit_datetime',
			self::$_table_name.'.owner_user_id'
		)
		->from($table_task_setting)
      	->join(self::$_table_name, 'INNER')->on($table_task_setting.'.id', '=', self::$_table_name.'.task_setting_id')
      	->join($table_task_master, 'INNER')->on($table_task_master.'.id', '=', $table_task_setting.'.task_master_id')
		->where($table_task_setting.'.client_id', 'in', $client_ids)
		->where(self::$_table_name.'.task_status',"=",$status);


		## ステータスが未完了の場合は期限を参照(開始日は対象としない)
		if($status == TSKT_TASK_STATUS_INCOMP){
			$query->where(self::$_table_name.'.task_limit_datetime',"<=",$to_limit_date);
			$query->order_by(self::$_table_name.'.task_limit_datetime','asc');//最大で1000まで
		}

		## ステータスが完了の場合は終了日を参照
		else{
			$query->where(self::$_table_name.'.task_end_datetime',">=",$from_limit_date);
			$query->where(self::$_table_name.'.task_end_datetime',"<=",$to_limit_date);
			$query->offset(0)->limit(300)->order_by(self::$_table_name.'.task_end_datetime','desc');//最大で1000まで
		}

		return $query->execute("readonly")->as_array();
	}

	/**
	 * get_by_setting_id
	 * 
	 * @param mixed $task_setting_id 
	 * @param mixed $from_limit_date 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_setting_id($task_setting_id, $from_limit_date=null, $status=TSKT_TASK_STATUS_INCOMP) {
		$query = DB::select(
			self::$_table_name.'.id',
			self::$_table_name.'.task_setting_id',
			self::$_table_name.'.task_status',
			self::$_table_name.'.task_end_datetime',
			self::$_table_name.'.task_limit_datetime',
			self::$_table_name.'.owner_user_id'
		)
		->from(self::$_table_name)
		->where('task_setting_id',"=",$task_setting_id);

		if(!empty($from_limit_date)){
			$query->where(self::$_table_name.'.task_limit_datetime',">=",$from_limit_date);
		}
		if(!empty($status)){
			$query->where('task_status',"=",$status);
		}

		return $query->execute("readonly")->as_array();

	}
}
