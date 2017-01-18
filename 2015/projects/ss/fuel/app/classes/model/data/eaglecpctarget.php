<?php

class Model_Data_EagleCpcTarget extends \Model {

	// テーブル名
	protected static $_table_name = "t_eagle_cpc_target";

	/**
	 * CPCの変更値を取得する
	 *
	 * @param $id 処理ID
	 * @return CPCの変更値リスト
	 */
	public static function get($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".adgroup_cpc_max", "adgroup_cpc_max"),
						  array(self::$_table_name . ".adgroup_bid_modifier", "adgroup_bid_modifier"),
						  array(self::$_table_name . ".adgroup_bid_modifier_operator", "adgroup_bid_modifier_operator"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);

		return $SQL->execute("readonly")->as_array();
	}

	/**
	 * CPC変更の最新の処理IDを取得
	 *
	 * @param なし
	 * @return 処理ID
	 */
	public static function get_new_id() {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"));
		$SQL->from(self::$_table_name);
		$SQL->order_by(self::$_table_name . ".id", "desc");
		$SQL->limit(1);

		return $SQL->execute("readonly")->as_array();
	}
	/**
	 * CPCの変更値を登録する
	 *
	 * @param $columns 登録するカラム
	 * @param $values 登録する値
	 * @return 登録件数
	 */
	public static function ins($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		return $SQL->execute("administrator");
	}

	/*
	 * アカウント一覧を取得する
	 *
	 * @param $id 処理ID
	 * @return アカウント一覧
	 */
	public static function get_target_account_list($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);
		$SQL->group_by(self::$_table_name . ".id")
			->group_by(self::$_table_name . ".media_id")
			->group_by(self::$_table_name . ".account_id");

		return $SQL->execute("readonly")->as_array();
	}
}
