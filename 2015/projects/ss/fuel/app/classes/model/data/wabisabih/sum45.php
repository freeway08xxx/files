<?php
class Model_Data_WabiSabiH_Sum45 extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_sum45';

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
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_A($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'A_CPA1');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 0.5);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_B($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'B_CPA2');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa * 0.5)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_C($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'C_CPA3');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 1.5);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_D($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'D_CPA4');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa * 1.5)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 2.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_E($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'E_CPA5');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa * 2.0)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 3.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_F($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'F_CPA6');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa * 3.0)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 4.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_G($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'G_CPA7');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>',  $target_cpa * 4.0)
			->where('(sum_cost / sum_conv)', '<=', $target_cpa * 5.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_H($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'H_CPA8');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '>', 0)
			->where('(sum_cost / sum_conv)', '>', $target_cpa * 5.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_I($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'I_No_Cost');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '=', 0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_J($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'J_Cost8');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '<=', $target_cpa * 0.5);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_K($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'K_Cost7');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa * 0.5)
			->where('sum_cost', '<=', $target_cpa);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_L($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'L_Cost6');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa)
			->where('sum_cost', '<=', $target_cpa * 1.5);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_M($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'M_Cost5');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa * 1.5)
			->where('sum_cost', '<=', $target_cpa * 2.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_N($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'N_Cost4');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa * 2.0)
			->where('sum_cost', '<=', $target_cpa * 3.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_O($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'O_Cost3');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa * 3.0)
			->where('sum_cost', '<=', $target_cpa * 4.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_P($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'P_Cost2');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>',  $target_cpa * 4.0)
			->where('sum_cost', '<=', $target_cpa * 5.0);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_Q($wabisabi_id, $target_cpa) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_flg', 'Q_Cost1');
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('sum_imp',  '>', 0)
			->where('sum_cost', '>', 0)
			->where('sum_conv', '=', 0)
			->where('sum_cost', '>', $target_cpa * 5.0);

		return $SQL->execute('administrator');
	}
}
