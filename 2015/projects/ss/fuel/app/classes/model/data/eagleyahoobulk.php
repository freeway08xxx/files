<?php

################################################################################
#
# Title : Yahoo バルクファイル管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleYahooBulk extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_yahoo_bulk";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_bulk", "campaign_bulk"),
						  array(self::$_table_name . ".adgroup_bulk", "adgroup_bulk"),
						  array(self::$_table_name . ".keyword_bulk", "keyword_bulk"),
						  array(self::$_table_name . ".ad_bulk", "ad_bulk"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id);

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

	/*========================================================================*/
	/* キャンペーン用バルクファイル保存
	/*========================================================================*/
	public static function upd_cpnbulk($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		$SQL->set_duplicate(array("campaign_bulk = VALUES(campaign_bulk)","adgroup_bulk = adgroup_bulk",
								  "keyword_bulk = keyword_bulk","ad_bulk = ad_bulk"));

		return $SQL->execute("administrator");
	}
	/*========================================================================*/
	/* 広告グループ用バルクファイル保存
	/*========================================================================*/
	public static function upd_adgbulk($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		$SQL->set_duplicate(array("campaign_bulk = campaign_bulk","adgroup_bulk = VALUES(adgroup_bulk)",
								  "keyword_bulk = keyword_bulk","ad_bulk = ad_bulk"));

		return $SQL->execute("administrator");
	}
	/*========================================================================*/
	/* キーワード用バルクファイル保存
	/*========================================================================*/
	public static function upd_kwbulk($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		$SQL->set_duplicate(array("campaign_bulk = campaign_bulk","adgroup_bulk = adgroup_bulk",
								  "keyword_bulk = VALUES(keyword_bulk)","ad_bulk = ad_bulk"));

		return $SQL->execute("administrator");
	}
	/*========================================================================*/
	/* 広告用バルクファイル保存
	/*========================================================================*/
	public static function upd_adbulk($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		$SQL->set_duplicate(array("campaign_bulk = campaign_bulk","adgroup_bulk = adgroup_bulk",
								  "keyword_bulk = keyword_bulk","ad_bulk = VALUES(ad_bulk)"));

		return $SQL->execute("administrator");
	}
}
