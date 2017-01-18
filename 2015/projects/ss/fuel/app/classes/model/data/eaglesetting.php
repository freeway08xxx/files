<?php

################################################################################
#
# Title : バッチ処理管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleSetting extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_setting";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".exec_code", "exec_code"),
						  array(self::$_table_name . ".result_cnt", "result_cnt"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		return $SQL->execute("administrator");
	}
}
