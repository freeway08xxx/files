<?php

class Model_Data_Eagle extends \Model {

	// テーブル名
	protected static $_table_name = "t_eagle";
	protected static $_properties = array(
		'id',
		'exec_code',
		'client_id',
		'status',
		'options',
		'created_at',
		'created_user',
		'updated_at'
	);

	/**
	 * 登録 
	 * 
	 * @param mixed $exec_code 
	 * @param mixed $client_id 
	 * @param mixed $status 
	 * @param mixed $options 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function insert($exec_code, $client_id, $status, $options=null){
		// insert 文を準備します
		$query = 
			DB::insert(self::$_table_name)->set(array(
				'exec_code' => $exec_code,
				'client_id' => $client_id,
				'status' => $status,
				'options' => $options
			));
		return $query->execute("administrator");
	}


	/**
	 * 全件取得 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_all($offset=null, $limit=null) {
		$query = DB::select()->from(self::$_table_name);
		$query->order_by('id','desc');
		if(isset($offset)){
			$query->offset($offset);
		}
		if(isset($limit)){
			$query->limit($limit);
		}
		return $query->execute("administrator")->as_array();
	}

	/**
	 * PKから一件取得 
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function get_one($id) {
		$query = 
			DB::select(implode(',',self::$_properties))
				->from(self::$_table_name)
				->where('id',$id);
		return $query->execute("administrator")->current();
	}

	/**
	 * ステータス情報の更新 
	 * 
	 * @param int $id 
	 * @param int $status 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_status($id, $status){
		$query = DB::update(self::$_table_name)
					->value('status', $status)
					->where('id',$id);
		return $query->execute("administrator");
	}

	/**
	 * idを元に更新 
	 * 
	 * @param mixed $id 
	 * @param mixed $value 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_by_id($id, $value){
		$query = DB::update(self::$_table_name)
					->set($value)
					->where('id',$id);
		return $query->execute("administrator");
	}
}
