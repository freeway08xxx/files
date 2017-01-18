<?php
class Model_Data_WabiSabiH_SettingFilter extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_setting_filter';

	############################################################################
	## フィルタ設定一覧取得
	############################################################################
	public static function get_list($wabisabi_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		return $SQL->execute('administrator');
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($wabisabi_id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator');
	}
}
