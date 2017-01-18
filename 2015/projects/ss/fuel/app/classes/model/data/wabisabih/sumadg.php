<?php
class Model_Data_WabiSabiH_SumADG extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_sum_adg';

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
	public static function ins($wabisabi_id) {

		$sum_table = 't_wabisabih_sum';

		$SQL  = 'insert into '.self::$_table_name.' ';
		$SQL .= 'select wabisabi_id,';
		$SQL .= '       client_id,';
		$SQL .= '       media_id,';
		$SQL .= '       account_id,';
		$SQL .= '       account_name,';
		$SQL .= '       campaign_id,';
		$SQL .= '       campaign_name,';
		$SQL .= '       adgroup_id,';
		$SQL .= '       adgroup_name,';
		$SQL .= '       device,';
		$SQL .= '       cpc_update_date_adg,';
		$SQL .= '       0,';
		$SQL .= '       0,';
		$SQL .= '       0,';
		$SQL .= '       0,';
		$SQL .= '       0,';
		$SQL .= '       now(),';
		$SQL .= '       "INIT",';
		$SQL .= '       null,';
		$SQL .= '       null';
		$SQL .= '  from '.$sum_table;
		$SQL .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL .= ' group by wabisabi_id, client_id, media_id, account_id, campaign_id, adgroup_id, device';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 最長入札日取得
	############################################################################
	public static function get_max_cpc_update_date($wabisabi_id) {

		$SQL = DB::select(DB::expr('min(cpc_update_date) as max_cpc_update_date'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator')->current();
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
								'sum_imp     = VALUES(sum_imp)',
								'sum_click   = VALUES(sum_click)',
								'sum_cost    = VALUES(sum_cost)',
								'sum_conv    = VALUES(sum_conv)',
								'rank        = VALUES(rank)')
							);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 挿入（前日レポートデータ反映）
	############################################################################
	public static function upd_report($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array(
								'rank        = ((sum_imp * rank + VALUES(sum_imp) * VALUES(rank))) / (sum_imp + VALUES(sum_imp))',
								'sum_imp     = sum_imp + VALUES(sum_imp)',
								'sum_click   = sum_click + VALUES(sum_click)',
								'sum_cost    = sum_cost + VALUES(sum_cost)',
								'sum_conv    = sum_conv + VALUES(sum_conv)')
							);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 最長入札変更日以降に入札された実績あり広告グループ一覧取得
	############################################################################
	public static function get_update_report_adgroup_list($wabisabi_id, $max_cpc_update_date) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'account_name',
						  'campaign_id',
						  'campaign_name',
						  'adgroup_id',
						  'adgroup_name',
						  'cpc_update_date');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('cpc_update_date', '>', $max_cpc_update_date)
			->where('sum_imp', '>', 0);
		$SQL->group_by('wabisabi_id','client_id','media_id','account_id','campaign_id','adgroup_id');

		return $SQL->execute('administrator')->as_array();
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
	## 挿入（前日外部CVデータ反映）
	############################################################################
	public static function upd_extcv($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array('sum_conv = sum_conv + VALUES(sum_conv)'));

		return $SQL->execute('administrator');
	}

	############################################################################
	## 最長入札変更日以降に入札されたCVあり広告グループ一覧取得
	############################################################################
	public static function get_update_extcv_adgroup_list($wabisabi_id, $max_cpc_update_date) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'account_name',
						  'campaign_id',
						  'campaign_name',
						  'adgroup_id',
						  'adgroup_name',
						  'cpc_update_date');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('cpc_update_date', '>', $max_cpc_update_date)
			->where('sum_conv', '>', 0);
		$SQL->group_by('wabisabi_id','client_id','media_id','account_id','campaign_id','adgroup_id');

		return $SQL->execute('administrator')->as_array();
	}
}
