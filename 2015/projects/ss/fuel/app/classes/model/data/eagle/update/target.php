<?php

class Model_Data_Eagle_Update_Target extends \Model {

	// テーブル名
	protected static $_table_name = "t_eagle_update_target";
	protected static $_properties = array(
		'id',
		'eagle_id',
		'data',
		'created_at',
		'updated_at'
	);

	/**
	 * Eagle更新情報登録 
	 * 
	 * @param mixed $values 
	 * @static
	 * @access public
	 */
	public static function insert($columns,$values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		return $SQL->execute("administrator");
	}

	/**
	 * eagle_idから検索を行う 
	 * 
	 * @param mixed $eagle_id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function find_by_eagle_id($eagle_id){
		$SQL = DB::select(implode(',',self::$_properties));
		$SQL->from(self::$_table_name);
		$SQL->where('eagle_id',"=",$eagle_id);
		return $SQL->execute("administrator")->as_array();

	}
}
