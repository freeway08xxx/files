<?php

################################################################################
#
# Title : バッチ処理管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_ImpEstimateSetting extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_imp_estimate_setting";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($id) {

		$SQL = DB::select("media",
						  "get_method",
						  "search_method",
						  "device",
						  "search_obj",
						  "cpc",
						  "keyword")
		    ->from(self::$_table_name)
		    ->where("id", $id);

		return $SQL->execute()->current();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		return $SQL->execute();
	}
}
