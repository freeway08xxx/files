<?php

################################################################################
#
# Title : バッチ処理管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_AccountSyncSchedule extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_account_sync_schedule";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$SQL = DB::select(self::$_table_name . ".id",
						  self::$_table_name . ".reserve_id",
						  self::$_table_name . ".media_id",
						  self::$_table_name . ".account_id",
						  self::$_table_name . ".action_date",
						  self::$_table_name . ".action_time",
						  self::$_table_name . ".action_minutes",
						  self::$_table_name . ".output_format",
						  self::$_table_name . ".eagle_flg",
						  self::$_table_name . ".approval_flg",
						  self::$_table_name . ".mail_user_list",
						  self::$_table_name . ".action_status",
						  self::$_table_name . ".out_file_path",
						  self::$_table_name . ".created_at",
						  "mora.user.user_name")
		    ->from(self::$_table_name)
			->join("mora.user", "INNER")->on("mora.user.id", "=", self::$_table_name . ".created_user")
		    ->where(self::$_table_name . ".media_id", $media_id)
		    ->where(self::$_table_name . ".account_id", $account_id);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 取得(即時実行)
	/*========================================================================*/
	public static function get_by_reserve_id($reserve_id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "schedule_id"),
						  self::$_table_name . ".media_id",
						  self::$_table_name . ".account_id",
						  self::$_table_name . ".output_format",
						  self::$_table_name . ".eagle_flg",
						  self::$_table_name . ".approval_flg",
						  self::$_table_name . ".mail_user_list",
						  self::$_table_name . ".created_user",
						  "mora.account.account_name",
						  "mora.account.client_id")
		    ->from(self::$_table_name)
			->join("mora.account", "INNER")->on("mora.account.id", "=", self::$_table_name . ".account_id")
		    ->where(self::$_table_name . ".reserve_id", $reserve_id);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 取得(予約実行)
	/*========================================================================*/
	public static function get_by_time($date, $time, $minutes) {

		$SQL = DB::select(array(self::$_table_name . ".id", "schedule_id"),
						  self::$_table_name . ".media_id",
						  self::$_table_name . ".account_id",
						  self::$_table_name . ".output_format",
						  self::$_table_name . ".eagle_flg",
						  self::$_table_name . ".approval_flg",
						  self::$_table_name . ".mail_user_list",
						  self::$_table_name . ".created_user",
						  "mora.account.account_name",
						  "mora.account.client_id")
		    ->from(self::$_table_name)
			->join("mora.account", "INNER")->on("mora.account.id", "=", self::$_table_name . ".account_id")
		    ->where(self::$_table_name . ".action_date", $date)
		    ->where(self::$_table_name . ".action_time", $time)
		    ->where(self::$_table_name . ".action_minutes", $minutes);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 実行チェック
	/*========================================================================*/
	public static function chk_schedule($schedule_id) {

		$SQL = DB::select("id")
		    ->from(self::$_table_name)
		    ->where("id", $schedule_id)
		    ->where("action_status", null);

		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 一括DL対象を取得
	/*========================================================================*/
	public static function get_bulk_dl($ids) {

		$SQL = DB::select("id", "account_id", "out_file_path")
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

		return $SQL->execute('administrator');
	}

	/*========================================================================*/
	/* 更新
	/*========================================================================*/
	public static function upd($schedule_id, $action_status = null, $out_file_path = null) {

		$SQL = DB::update(self::$_table_name)
			->set(array("action_status" => $action_status,
						"out_file_path" => $out_file_path))
			->where("id", $schedule_id);

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
