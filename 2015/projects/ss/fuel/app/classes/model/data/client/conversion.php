<?php
class Model_Data_Client_Conversion extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_client_cv_setting";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($client_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("client_id", $client_id);

		return $SQL->execute()->current();
	}

	/*========================================================================*/
	/* 一覧取得
	/*========================================================================*/
	public static function get_list($limit = null) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute()->as_array("client_id");
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		$SQL->set_duplicate(array("cv_list = VALUES(cv_list)"));

		return $SQL->execute("administrator");
	}
}
