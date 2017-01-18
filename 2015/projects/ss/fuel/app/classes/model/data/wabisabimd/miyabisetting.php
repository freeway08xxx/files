<?php

class Model_Data_WabisabiMD_MiyabiSetting extends \Model {

	// テーブル名定義
	protected static $_table_name = "miyabi_setting";

	/*
	 * 取得 - 画面設定データ
	 */
	public static function get_setting($miyabi_id) {

		$SQL = DB::select("miyabi_id",
						  "miyabi_name",
						  "client_id",
						  "status",
						  "setting_value")
			 ->from(self::$_table_name);
		if (isset($miyabi_id)) $SQL->where("miyabi_id", $miyabi_id);

		return $SQL->execute("wabisabi_md")->as_array();
	}

	/*
	 * 入札日を更新
	 */
	public static function upd_edit_date($miyabi_id, $setting_value) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("upd_datetime" => date("Y-m-d H:i:s"),
						 "upd_user" => Session::get("user_id_sem"),
						 "setting_value" => $setting_value))
			 ->where("miyabi_id", $miyabi_id);

		return $SQL->execute("wabisabi_md_admin");
	}
}
