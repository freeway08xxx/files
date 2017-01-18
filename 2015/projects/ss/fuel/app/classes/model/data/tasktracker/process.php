<?php

class Model_Data_Tasktracker_Process extends \Model {

	// テーブル名
	protected static $_table_name = "t_tasktracker_process";
	protected static $_properties = array(
		'id',
		'task_id',
		'process_setting_id',
		'process_status',
		'process_start_datetime',
		'process_end_datetime',
		'forecast_cost',
		'owner_user_id',
		'process_bomb_flg',
		'created_at'
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
		$columns = array('task_id','process_setting_id','process_status','process_start_datetime','forecast_cost','owner_user_id');
		$query = DB::insert(self::$_table_name, $columns);
		foreach ($values as $value) {
			$query->values($value);
		}

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

	/**
	 * delete_by_task_ids
	 * 
	 * @param mixed $task_ids 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function delete_by_task_ids($task_ids){
		$query = DB::delete(self::$_table_name)
			->where('task_id','in',$task_ids)
			->where('process_status','=',TSKT_TASK_STATUS_INCOMP);
		return $query->execute("administrator");
	}

	/**
	 * ステータスの更新を行う 
	 * 
	 * @param int $id 
	 * @param int $status 
	 * @param date $end_datetime 
	 * @static
	 * @access public
	 * @return int update count 
	 */
	public static function update_status($id, $status, $end_datetime=null){
		$query = DB::update(self::$_table_name);
		$query->value('process_status', $status);
		$query->where('id',"=",$id);

		if(!empty($end_datetime)){
			$query->value('process_end_datetime', $end_datetime);
		}

		return $query->execute("administrator");
	}

	/**
	 * update_bomb_id
	 * 
	 * @param mixed $id 
	 * @param mixed $bomb_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_bomb_flg($id, $bomb_flg){
		$query = DB::update(self::$_table_name);
		$query->value('process_bomb_flg', $bomb_flg);
		$query->where('id',"=",$id);
		return $query->execute("administrator");
	}

	/**
	 * Idからプロセス1レコードを取得 
	 * 
	 * @param id $id 
	 * @static
	 * @access public
	 * @return array 
	 */
	public static function get_one_by_id($id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('id',$id);
		return $query->execute("readonly")->current();
	}

	/**
	 * タスクIDからプロセス一覧を取得 
	 * 
	 * @param id $task_id 
	 * @static
	 * @access public
	 * @return array
	 */
	public static function get_by_task_id($task_id) {
		$query = DB::select_array(self::$_properties)
			->from(self::$_table_name)
			->where('task_id',$task_id);
		return $query->execute("readonly")->as_array();
	}

	/**
	 * get_process_by_user_and_status
	 * 
	 * @param mixed $user_id 
	 * @param mixed $process_status 
	 * @param mixed $from_date 
	 * @param mixed $to_date 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_process_by_user_and_status($user_id, $process_status=null, $from_date=null, $to_date=null) {
		$alias_table_task 				= 'task';
		$alias_table_task_setting 		= 'task_setting';
		$alias_table_process_setting 	= 'process_setting';

		$query = DB::select(
			self::$_table_name.'.id as process_id',
			self::$_table_name.'.process_status',
			self::$_table_name.'.forecast_cost',
			self::$_table_name.'.process_start_datetime',
			self::$_table_name.'.process_end_datetime',
			$alias_table_task.'.id as task_id',
			$alias_table_task.'.task_setting_id',
			$alias_table_task.'.task_limit_datetime',
			$alias_table_task_setting.'.task_master_id',
			$alias_table_task_setting.'.task_name',
			$alias_table_task_setting.'.task_description',
			$alias_table_task_setting.'.client_id',
			$alias_table_task_setting.'.job_type',
			$alias_table_task_setting.'.routine_setting',
			$alias_table_process_setting.'.process_name'
		)
		->from(self::$_table_name)
      	->join('t_tasktracker_task as '.$alias_table_task, 'INNER')->on(self::$_table_name.'.task_id', '=', $alias_table_task.'.id')
      	->join('t_tasktracker_task_setting as '.$alias_table_task_setting, 'INNER')->on($alias_table_task.'.task_setting_id', '=', $alias_table_task_setting.'.id')
      	->join('t_tasktracker_process_setting as '.$alias_table_process_setting, 'INNER')->on(self::$_table_name.'.process_setting_id', '=', $alias_table_process_setting.'.id')
		->where(self::$_table_name.'.owner_user_id', '=', $user_id);
		if(!empty($process_status)){
			$query->where(self::$_table_name.'.process_status', '=', $process_status);
		}

		## ステータスが未完了の場合は期限を参照(開始日は対象としない)
		if($process_status == TSKT_TASK_STATUS_INCOMP){
			if(!empty($from_date)){
				$query->where(self::$_table_name.'.process_start_datetime',">=",$from_date);
			}
			$query->where(self::$_table_name.'.process_start_datetime',"<",$to_date);
			$query->order_by(self::$_table_name.'.process_start_datetime','asc');//最大で1000まで
		}

		## ステータスが完了の場合は終了日を参照
		elseif($process_status == TSKT_TASK_STATUS_COMP){
			$query->where(self::$_table_name.'.process_end_datetime',">=",$from_date);
			$query->where(self::$_table_name.'.process_end_datetime',"<",$to_date);
			$query->offset(0)->limit(300)->order_by(self::$_table_name.'.process_end_datetime','desc');//最大で1000まで
		}

		else{
			if(!empty($from_date)){
				$query->where(self::$_table_name.'.process_start_datetime',">=",$from_date);
				$query->where(self::$_table_name.'.process_start_datetime',"<",$to_date);
			}
			$query->offset(0)->limit(500);
		}

		return $query->execute("readonly")->as_array();
	}

	/**
	 * delete_by_process_setting_id
	 * 
	 * @param mixed $process_setting_id 
	 * @param mixed $from_limit_date 
	 * @param mixed $status 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function delete_by_process_setting_id($process_setting_id, $from_limit_date=null, $status=TSKT_TASK_STATUS_INCOMP){
		$query = DB::delete(self::$_table_name)
			->where('process_setting_id','=',$process_setting_id);


		if(!empty($from_limit_date)){
			$query->where(self::$_table_name.'.process_start_datetime',">=",$from_limit_date);
		}
		if(!empty($status)){
			$query->where('process_status',"=",$status);
		}

		return $query->execute("administrator");
	}
}
