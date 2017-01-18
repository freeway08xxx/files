<?php

class Model_Data_WabisabiMD_MiyabiBidHistory extends \Model {

	// テーブル名定義
	protected static $_table_name = "miyabi_bid_history";

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
								  "campaign_name = VALUES(campaign_name)",
								  "adgroup_name = VALUES(adgroup_name)",
								  "name = VALUES(name)",
								  "bid_obj = VALUES(bid_obj)",
								  "old_adg_bid_cpc = VALUES(old_adg_bid_cpc)",
								  "old_bid_cpc = VALUES(old_bid_cpc)",
								  "new_bid_cpc = VALUES(new_bid_cpc)",
								  "old_bid_adj = VALUES(old_bid_adj)",
								  "new_bid_adj = VALUES(new_bid_adj)",
								  "adg_cpa = VALUES(adg_cpa)",
								  "cpa = VALUES(cpa)",
								  "adg_conv = VALUES(adg_conv)",
								  "conv = VALUES(conv)"));

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
