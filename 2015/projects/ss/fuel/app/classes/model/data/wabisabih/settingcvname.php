<?php
class Model_Data_WabiSabiH_SettingCvName extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_setting_cvname';

	############################################################################
	## 処理対象一覧取得
	############################################################################
	public static function get_target_list($target_cv = null, $target_hour = null) {

		$SQL = DB::select('wabisabi_id');
		$SQL->from(self::$_table_name);

		## CAMP
		if ($target_cv === TARGET_CV_CAMP) {
			$SQL->where('tool_id', TOOL_ID_CAMP);
		## CAMP以外の外部CV
		} elseif ($target_cv === TARGET_CV_EXTCV) {
			$SQL->where('tool_id', '!=', TOOL_ID_CAMP);
			if (isset($target_hour)) $SQL->where('extcv_exec_hour', $target_hour);
		}

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 外部CV設定一覧取得
	############################################################################
	public static function get_list($wabisabi_id) {

		$tool_master = 'mora.tool_master';

		$SQL = DB::select(
					DB::expr(self::$_table_name.'.*'),
					DB::expr('CONCAT("[",tool_name,"]",cv_name) AS cv_display'),
					DB::expr('CONCAT(tool_id,";",cv_name) AS cv_key'));
		$SQL->from(self::$_table_name);
		$SQL->join($tool_master, 'INNER')
			->on(self::$_table_name.'.tool_id', '=', $tool_master.'.id');
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
