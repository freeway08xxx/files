<?php
class Model_Data_Report_Hour extends \Model {

	## テーブル名定義
	protected static $_table_name = "r_hour_";

	############################################################################
	## 挿入
	############################################################################
	public static function ins($media_id, $values) {

		## 媒体毎
		$table_name = self::$_table_name . strtolower($GLOBALS["media_name_list"]["$media_id"]);

		$SQL = DB::insert($table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array("bid_modifier = VALUES(bid_modifier)",
								  "imp = VALUES(imp)",
								  "click = VALUES(click)",
								  "cost = VALUES(cost)",
								  "conv = VALUES(conv)",
								  "total_conv = VALUES(total_conv)"));

        return $SQL->execute("searchsuite_report_admin");
	}
}
