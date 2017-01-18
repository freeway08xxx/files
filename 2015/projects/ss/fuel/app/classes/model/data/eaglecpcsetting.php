<?php

class Model_Data_EagleCpcSetting extends \Model {

	// テーブル名
	protected static $_table_name = "t_eagle_cpc_setting";

	/**
	 * CPC変更画面の設定値を取得する
	 *
	 * @param $id 処理ID
	 * @return CPC変更画面の設定値
	 */
	public static function get($id) {

		$SQL = DB::select(array(self::$_table_name . ".setting_value", "setting_value"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);

		return $SQL->execute("readonly")->current();
	}

	/**
	 * CPC変更画面の設定値を登録する
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
}
