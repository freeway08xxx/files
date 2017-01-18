<?php

class Model_Data_Tasktracker_Task_Master_Arujo extends \Model {

	// テーブル名
	protected static $_table_name = "m_tasktracker_task_master_arujo";
	protected static $_properties = array(
		'id',
		'task_master_id',
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
	 * delete_by_setting_id
	 * 
	 * @param int $setting_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function delete_by_id($id){
		$query = DB::delete(self::$_table_name)->where('id','=',$id);
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
			->where(self::$_table_name.'.task_master_id', '=', $task_master_id);
		return $query->execute()->as_array();
	}

	/**
	 * get_by_task_master_id_join_client
	 *
	 * ArujoマスターとArujoクライアントマスターを取得し、
	 * クライアントマスターがArujoマスターIDを保持している場合は上書き
	 * ない場合は追加して取得
	 *
	 * 
	 * @param mixed $task_master_id 
	 * @param mixed $client_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_by_task_master_id_join_client($task_master_id, $client_id) {
		$master_arujo_client 	= 'arujo_client';

		$master_arujo = self::get_by_task_master_id($task_master_id);
		$master_arujo_index_by_id = Util_Common_Array::attach_key($master_arujo, 'id');

		$master_arujo_client = Model_Data_Tasktracker_Task_Master_Arujo_Client::get_by_client_id($client_id, $task_master_id);
		foreach($master_arujo_client as $key => $value) {
			## idが重複して意味が変わるので別名で保持し,意味を統一
			$value['task_master_arujo_client_id'] = $value['id']; 	
			$value['id'] = $value['task_master_arujo_id'];

			## 上書きもしくは追加を行う 
			if(!empty($value['task_master_arujo_id'])){
				$master_arujo_index_by_id[$value['task_master_arujo_id']] = $value;
			}else{
				$master_arujo_index_by_id[] = $value;
			}
		}

		return array_values($master_arujo_index_by_id);
	}
}
