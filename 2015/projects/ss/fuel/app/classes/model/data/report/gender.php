<?php
class Model_Data_Report_Gender extends \Model {

	## テーブル名定義
	protected static $_table_name = "r_gender_";

	############################################################################
	## 取得 - MIYABI'd
	############################################################################
	public static function get_by_miyabi($media_id, $account_id, $start_date, $end_date, $conv) {

		$table_name = self::$_table_name . strtolower($GLOBALS["media_name_list"][$media_id]);
		$SQL = DB::select("target_id",
						  "bid_modifier",
						  "report_date",
						  array(DB::expr("if(sum(" . $conv . ") > 0, truncate(sum(cost) / sum(" . $conv . ") + 0.5, 0), sum(cost))"), "cpa"),
						  array(DB::expr("sum(" . $conv . ")"), "conv"),
						  array(DB::expr("CONCAT(media_id, '::', account_id, '::', campaign_id, '::', adgroup_id, '::', target_id)"), "unique_key"),
						  array(DB::expr("CONCAT(media_id, '::', account_id, '::', campaign_id, '::', adgroup_id)"), "adg_unique_key"))
			 ->from($table_name)
			 ->where("media_id", $media_id)
			 ->where("account_id", $account_id)
			 ->where("report_date", "between", array($start_date, $end_date))
			 ->group_by("media_id", "account_id", "campaign_id", "adgroup_id", "target_id")
			 ->order_by("report_date", "desc");

		return $SQL->execute("searchsuite_report")->as_array("unique_key");
	}

	############################################################################
	## 取得 - MIYABI'd ADG Summary
	############################################################################
	public static function get_by_miyabi_adg($media_id, $account_id, $start_date, $end_date, $conv) {

		$table_name = self::$_table_name . strtolower($GLOBALS["media_name_list"][$media_id]);
		$SQL = DB::select(array(DB::expr("if(sum(" . $conv . ") > 0, truncate(sum(cost) / sum(" . $conv . ") + 0.5, 0), sum(cost))"), "cpa"),
						  array(DB::expr("sum(" . $conv . ")"), "conv"),
						  array(DB::expr("CONCAT(media_id, '::', account_id, '::', campaign_id, '::', adgroup_id)"), "unique_key"))
			 ->from($table_name)
			 ->where("media_id", $media_id)
			 ->where("account_id", $account_id)
			 ->where("report_date", "between", array($start_date, $end_date))
			 ->group_by("media_id", "account_id", "campaign_id", "adgroup_id")
			 ->having("conv", ">=", MIYABI_ADG_CV_MIN);

		return $SQL->execute("searchsuite_report")->as_array("unique_key");
	}

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
