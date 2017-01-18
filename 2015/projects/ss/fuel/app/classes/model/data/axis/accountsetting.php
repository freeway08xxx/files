<?php
class Model_Data_Axis_AccountSetting extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_axis_template_account_setting";

	############################################################################
	## 一覧取得
	############################################################################
	public static function get_list($template_id, $limit = null) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("template_id", $template_id);

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
	## 一括挿入
	############################################################################
	public static function ins_list($template_id, $values_list) {

		foreach ($values_list as $values) {
		    if (!isset($SQL)) {
		        $SQL = DB::insert(self::$_table_name, array_merge(array("template_id"), array_keys($values)));
		    }
		    $SQL->values(array_merge(array($template_id), array_values($values)));
		}

		return $SQL->execute("administrator");
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($template_id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where("template_id", $template_id);

		return $SQL->execute("administrator");
	}
}
