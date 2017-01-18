<?php
class Model_Data_WabiSabiH_HistoryRes extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_history_res';

	############################################################################
	## テーブル再構成
	############################################################################
	public static function alter_table() {

		$SQL = 'alter table ' . self::$_table_name . ' engine InnoDB';

		return \DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 過去データ削除
	############################################################################
	public static function del_past($target_date = null) {

		$SQL = DB::delete(self::$_table_name);
		if (isset($target_date)) $SQL->where('exec_date', '<=', $target_date);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($value) {

		$SQL = DB::insert(self::$_table_name, array_keys($value));
		$SQL->values(array_values($value));
		$SQL->set_duplicate(array('status = VALUES(status)'));

		return $SQL->execute('administrator');
	}

	############################################################################
	## ステータス毎のID取得
	############################################################################
	public static function get_ids_status($id_list, $exec_type, $exec_date, $status = array(DB_STATUS_START)) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', 'in', $id_list)
			->where('exec_type', $exec_type)
			->where('exec_date', $exec_date)
			->where('status', 'in', $status);

		return $SQL->execute('administrator')->as_array();
	}

	############################################################################
	## 処理結果一覧取得
	############################################################################
	public static function get_list($wabisabi_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('readonly')->as_array();
	}
}
