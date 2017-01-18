<?php

################################################################################
#
# Title : キーワード管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleKeyword extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_keyword";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".keyword_id", "keyword_id"),
						  array(self::$_table_name . ".keyword", "keyword"),
						  array(self::$_table_name . ".status", "keyword_status"));
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

		$SQL->set_duplicate(array("keyword = VALUES(keyword)", "status = VALUES(status)"));

		return $SQL->execute("administrator");
	}
}
