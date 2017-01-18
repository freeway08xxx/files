<?php
class Model_Data_Report_History extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_report_history";

	############################################################################
	## 取得
	############################################################################
	public static function get($id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("id", $id);

		return $SQL->execute("searchsuite_report")->current();
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		return $SQL->execute("searchsuite_report_admin");
	}

	############################################################################
	## 更新
	############################################################################
	public static function upd($id, $values) {

		$SQL = DB::update(self::$_table_name);
		$SQL->set($values);
		$SQL->where("id", $id);

		return $SQL->execute("searchsuite_report_admin");
	}

	############################################################################
	## ステータス毎のID取得
	############################################################################
	public static function get_ids_status($id_list, $status = array(STATUS_START)) {

		$SQL = DB::select("id");
		$SQL->from(self::$_table_name);
		$SQL->where("id", "in", $id_list)
			->where("status", "in", $status);

		return $SQL->execute("searchsuite_report_admin")->as_array();
	}
}
