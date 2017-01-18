<?php
class Model_Data_Axis_DimensionParent extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_axis_dimention_parent";

	############################################################################
	## 一覧取得
	############################################################################
	public static function get_list_by_client($client_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("client_id", $client_id);
		$SQL->order_by("id");

		return $SQL->execute("readonly")->as_array();
	}
}
