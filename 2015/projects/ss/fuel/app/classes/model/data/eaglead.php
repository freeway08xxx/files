<?php

################################################################################
#
# Title : 広告管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleAd extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_ad";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".ad_id", "ad_id"),
						  array(self::$_table_name . ".ad_name", "ad_name"),
						  array(self::$_table_name . ".status", "ad_status"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id)
			->where(self::$_table_name . ".campaign_id", "=", $campaign_id)
			->where(self::$_table_name . ".adgroup_id", "=", $adgroup_id);

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

		$SQL->set_duplicate(array("ad_name = VALUES(ad_name)", "status = VALUES(status)",
								  "title = VALUES(title)", "description_1 = VALUES(description_1)", "description_2 = VALUES(description_2)"));

		return $SQL->execute("administrator");
	}
}
