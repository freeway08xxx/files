<?php

################################################################################
#
# Title : バッチ処理管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_DownloadList extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_download_list";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($user_id) {

		$SQL = DB::select("id",
						  "screen_name",
						  "file_name",
						  "out_file_path",
						  "created_at")
		    ->from(self::$_table_name)
		    ->where("created_user", $user_id);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 取得(PK)
	/*========================================================================*/
	public static function get_by_pk($id) {

		$SQL = DB::select("file_name", "out_file_path")
		    ->from(self::$_table_name)
		    ->where("id", $id);

		return $SQL->execute()->current();
	}

	/*========================================================================*/
	/* 一括DL対象を取得
	/*========================================================================*/
	public static function get_bulk_dl($ids) {

		$SQL = DB::select("id",
						  "file_name",
						  "out_file_path")
		    ->from(self::$_table_name)
		    ->where("id", "in", $ids);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		return $SQL->execute();
	}

	/*========================================================================*/
	/* 削除
	/*========================================================================*/
	public static function del($ids) {

		$SQL = DB::delete(self::$_table_name)
			->where("id", "in", $ids);

		return $SQL->execute();
	}
}
