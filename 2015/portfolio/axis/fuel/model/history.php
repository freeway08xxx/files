<?php
class Model_Data_Axis_History extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_axis_export_report_history";

	############################################################################
	## 取得
	############################################################################
	public static function get($id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("id", $id);

		return $SQL->execute("readonly")->current();
	}

	############################################################################
	## 一覧取得
	############################################################################
	public static function get_list($user_id_sem, $limit = null) {

		$user_table    = "mora.user";
		$company_table = "mora.company";
		$client_table  = "mora.client";

		$SQL = DB::select(self::$_table_name . ".*", $user_table . ".user_name", $company_table . ".company_name", $client_table . ".client_name");
		$SQL->from(self::$_table_name);
		$SQL->join($user_table, "INNER")->on($user_table . ".id", "=", self::$_table_name . ".created_user")
			->join($client_table, "INNER")->on($client_table . ".id", "=", self::$_table_name . ".client_id")
			->join($company_table, "INNER")->on($company_table . ".id", "=", $client_table . ".company_id");
		$SQL->where(self::$_table_name . ".created_user", $user_id_sem)
			->where($client_table . ".delete_flg", "0")
			->where($company_table . ".delete_flg", "0");
		$SQL->order_by(self::$_table_name . ".created_at", "desc");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		return $SQL->execute("administrator");
	}

	############################################################################
	## 更新
	############################################################################
	public static function upd($id, $values) {

		$SQL = DB::update(self::$_table_name);
		$SQL->set($values);
		$SQL->where("id", $id);

		return $SQL->execute("administrator");
	}
}
