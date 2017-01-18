<?php
class Model_Data_Mypage_Keyword extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_mypage_keyword';

	/*========================================================================*/
	/* マイキーワード取得 
	/*========================================================================*/
	public static function get_mykeyword($user_id) {
		$SQL = DB::select()
			->from(self::$_table_name)
			->where("user_id",$user_id);
		return $SQL->execute()->current();
	}

	/*========================================================================*/
	/* マイキーワード登録 
	/*========================================================================*/
	public static function ins($values) {
		$SQL = DB::insert(self::$_table_name,array_keys($values));
		$SQL->values(array_values($values));
		$SQL->set_duplicate(array("status = VALUES(status)"));
		return $SQL->execute('administrator');
	}
}