<?php
class Model_Data_QuickManage_Discount extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_quickmanage_discount_setting";

	/*========================================================================*/
	/* 一覧取得
	/*========================================================================*/
	public static function get_list($start_ym = null, $end_ym = null, $limit = null) {

		$accont_table = "mora.account";

		$SQL = DB::select(self::$_table_name . ".*", $accont_table . ".account_name");
		$SQL->from(self::$_table_name);
		$SQL->join($accont_table, "INNER")
			->on(self::$_table_name . ".account_id", "=", $accont_table . ".id");
		if (!is_null($start_ym)) {
			 $SQL->where("target_ym", ">=", $start_ym);
		}
		if (!is_null($end_ym)) {
			 $SQL->where("target_ym", "<=", $end_ym);
		}
		$SQL->order_by(self::$_table_name . ".media_id")
			->order_by(self::$_table_name . ".account_id");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array("discount_type = VALUES(discount_type)", "discount_rate = VALUES(discount_rate)"));

		return $SQL->execute("administrator");
	}
}
