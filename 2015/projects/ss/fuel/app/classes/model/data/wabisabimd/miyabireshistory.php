<?php

class Model_Data_WabisabiMD_MiyabiResHistory extends \Model {

	// テーブル名定義
	protected static $_table_name = "miyabi_res_history";

	/*
	 * 登録
	 */
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array("upd_datetime = VALUES(add_datetime)",
								  "upd_user = VALUES(add_user)",
								  "result = VALUES(result)"));

		return $SQL->execute("wabisabi_md_admin");
	}

	/*
	 * 削除 - １ヶ月以前のデータ
	 */
	public static function del_by_one_month() {

		$SQL = DB::delete(self::$_table_name)
			 ->where("add_datetime", "<", DB::expr("date_add(now(), interval -1 month)"));

		return $SQL->execute("wabisabi_md_admin");
	}
}
