<?php

################################################################################
#
# Title : 広告グループ管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleAdGroup extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_adgroup";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id, $campaign_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".adgroup_name", "adgroup_name"),
						  array(self::$_table_name . ".status", "adgroup_status"),
						  array(self::$_table_name . ".match_type", "adgroup_match_type"),
						  array(self::$_table_name . ".cpc_max", "adgroup_cpc_max"),
						  array(self::$_table_name . ".bid_modifier", "adgroup_bid_modifier"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id)
			->where(self::$_table_name . ".campaign_id", "=", $campaign_id);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 取得（PK指定）
	/*========================================================================*/
	public static function get_by_pk($media_id, $account_id, $campaign_id, $adgroup_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".adgroup_name", "adgroup_name"),
						  array(self::$_table_name . ".status", "adgroup_status"),
						  array(self::$_table_name . ".match_type", "adgroup_match_type"),
						  array(self::$_table_name . ".cpc_max", "adgroup_cpc_max"),
						  array(self::$_table_name . ".bid_modifier", "adgroup_bid_modifier"));
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

		$SQL->set_duplicate(array("adgroup_name = VALUES(adgroup_name)",
								  "status = VALUES(status)",
								  "cpc_max = VALUES(cpc_max)",
								  "bid_modifier = VALUES(bid_modifier)"));

		return $SQL->execute("administrator");
	}
}
