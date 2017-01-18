<?php

################################################################################
#
# Title : 処理対象アカウント用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleTargetAccount extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_target_account";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);

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
	/* 指定アカウントの掲載取得が１時間以内に完了したか確認
	/*========================================================================*/
	public static function chk_structure($id = null, $account_id, $media_id) {

		$SQL = DB::select(array("count(*)", "count"));
		$SQL->from(self::$_table_name);
		if (!is_null($id)) {
			$SQL->where(self::$_table_name . ".id", "=", $id);
		}
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id)
			->where("date_add(updated_at, INTERVAL 3 HOUR)", ">", DB::expr("now()"));

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 指定アカウントの掲載取得完了
	/*========================================================================*/
	public static function upd_structure_complete($id, $account_id) {

		$SQL = DB::update(self::$_table_name);
		$SQL->where("id", "=", $id)
			->where("account_id", "=", $account_id);

		return $SQL->execute("administrator");
	}
}
