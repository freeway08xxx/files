<?php
class Model_Data_QuickManage_History extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_quickmanage_export_report_history";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($id) {

		$SQL = DB::select(self::$_table_name . ".*");
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", $id);

		return $SQL->execute("readonly")->current();
	}

	/*========================================================================*/
	/* 一覧取得
	/*========================================================================*/
	public static function get_list($limit = null) {

		$user_table     = "mora.user";

		$SQL = DB::select(self::$_table_name . ".*",
						  $user_table . ".user_name");
		$SQL->from(self::$_table_name);
		$SQL->join($user_table, "INNER")
			->on($user_table . ".id", "=", self::$_table_name . ".created_user");
		$SQL->order_by(self::$_table_name . ".created_at", "desc");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		return $SQL->execute("administrator");
	}

	/*========================================================================*/
	/* 更新
	/*========================================================================*/
	public static function upd($id, $values) {

		$SQL = DB::update(self::$_table_name);
		$SQL->set($values);
		$SQL->where("id", $id);

		return $SQL->execute("administrator");
	}
}
