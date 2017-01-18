<?php
class Model_Data_Axis_DimensionChild extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_axis_dimention_child";

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
	public static function get_list($dimension_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("dimension_id", $dimension_id);

		return $SQL->execute("readonly")->as_array("account_id");
	}
}
