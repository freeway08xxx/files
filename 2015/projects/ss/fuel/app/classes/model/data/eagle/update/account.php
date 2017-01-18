<?php

class Model_Data_Eagle_Update_Account extends \Model {

	// テーブル名
	protected static $_table_name = "t_eagle_update_account";
	protected static $_properties = array(
		'id',
		'eagle_id',
		'media_id',
		'account_id',
		'status',
		'created_at',
		'updated_at'
	);


	/**
	 * eagle更新情報取得
	 * 
	 * @static
	 * @access public
	 * @return Array 
	 */
	public static function find_by_eagle_id($eagle_id) {

		$SQL = DB::select(implode(',',self::$_properties));
		$SQL->from(self::$_table_name);
		$SQL->where('eagle_id',"=",$eagle_id);
		return $SQL->execute("administrator")->as_array();
	}

	/**
	 * eagle更新情報取得
	 * 
	 * @static
	 * @access public
	 * @return Array 
	 */
	public static function find_by_eagle_id_join_account($eagle_id) {

		$SQL = DB::select(
			self::$_table_name.'.id',
			self::$_table_name.'.eagle_id',
			self::$_table_name.'.account_id',
			self::$_table_name.'.media_id',
			'mora.account.account_name'
		);
		$SQL->from(self::$_table_name)
      		->join('mora.account', 'INNER')->on(self::$_table_name.'.account_id', '=', 'mora.account.id');
		$SQL->where('eagle_id',"=",$eagle_id);
		$SQL->where('account.delete_flg',"=",0);
		return $SQL->execute("readonly")->as_array();
	}

	/**
	 * Eagle更新情報登録 
	 * 
	 * @param mixed $values 
	 * @static
	 * @access public
	 */
	public static function ins($columns,$values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		return $SQL->execute("administrator");
	}

	/**
	 * update_status
	 * 
	 * @param mixed $id 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function update_status($eagle_id,$account_id,$status){
		$SQL = DB::update(self::$_table_name);
		$SQL->value('status', $status);
		$SQL->where('eagle_id',"=",$eagle_id);
		$SQL->where('account_id',"=",$account_id);
		return $SQL->execute("administrator");
	}
}
