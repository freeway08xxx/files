<?php
class Model_Data_WabiSabiH_Sum7 extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_sum7';

	############################################################################
	## テーブル再構成
	############################################################################
	public static function alter_table() {

		$SQL = 'alter table ' . self::$_table_name . ' engine InnoDB';

		return \DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 全削除
	############################################################################
	public static function truncate() {

		return \DBUtil::truncate_table(self::$_table_name, 'administrator');
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($wabisabi_id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 不要データ削除
	############################################################################
	public static function del_deactive($wabisabi_id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('created_user', '!=', 'INIT');

		return $SQL->execute('administrator');
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
	## 挿入（レポートデータ反映）
	############################################################################
	public static function ins_report($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array(
								'sum_imp   = VALUES(sum_imp)',
								'sum_click = VALUES(sum_click)',
								'sum_cost  = VALUES(sum_cost)',
								'sum_conv  = VALUES(sum_conv)',
								'rank      = VALUES(rank)')
							);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 挿入（外部CVデータ反映）
	############################################################################
	public static function ins_extcv($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array('sum_conv = VALUES(sum_conv)'));

		return $SQL->execute('administrator');
	}

	############################################################################
	## キャンペーンサマリデータ取得
	############################################################################
	public static function get_sum_campaign_list($wabisabi_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id');
		$SQL->having(DB::expr('sum(sum_cost)'), '>', 0);
		$SQL->order_by(DB::expr('sum(sum_cost) / sum(sum_conv) is null'), 'asc')
			->order_by(DB::expr('sum(sum_cost) / sum(sum_conv)'), 'asc')
			->order_by(DB::expr('sum(sum_cost)'), 'asc')
			->order_by(DB::expr('sum(sum_imp)'), 'desc');

		return $SQL->execute('administrator')->as_array();
	}
}
