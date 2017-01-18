<?php
class Model_Data_WabiSabiH_SumKW extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_sum_kw';

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
		$SQL .= '       keyword_id,';
		$SQL .= '       keyword,';
		$SQL .= '       sum(sum_imp),';
		$SQL .= '       sum(sum_click),';
		$SQL .= '       sum(sum_cost),';
		$SQL .= '       sum(sum_conv),';
		$SQL .= '       sum(sum_imp * rank) / sum(sum_imp),';
		$SQL .= '       now(),';
		$SQL .= '       "INIT",';
		$SQL .= '       null,';
		$SQL .= '       null';
		$SQL .= '  from '.$sum_table;
		$SQL .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL .= ' group by wabisabi_id, client_id, media_id, account_id, campaign_id, adgroup_id, keyword_id';

		return DB::query($SQL)->execute('administrator');
	}
}
