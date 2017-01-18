<?php
class Model_Data_WabiSabiH_HistoryBid extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_history_bid';

	############################################################################
	## テーブル再構成
	############################################################################
	public static function alter_table() {

		$SQL = 'alter table ' . self::$_table_name . ' engine InnoDB';

		return \DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($wabisabi_id, $target_date = null) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);
		if (!isset($target_date)) $target_date = date('Ymd');
		$SQL->where('new_cpc_update_date', $target_date);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 過去データ削除
	############################################################################
	public static function del_past($target_date = null) {

		$SQL = DB::delete(self::$_table_name);
		if (isset($target_date)) $SQL->where('new_cpc_update_date', '<=', $target_date);

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
		$SQL .= '       cpc_max,';
		$SQL .= '       bid_modifier,';
		$SQL .= '       bid_adjust_flg,';
		$SQL .= '       new_bid_rate,';
		$SQL .= '       new_bid_modifier_increase_value,';
		$SQL .= '       new_cost_rate,';
		$SQL .= '       new_cpc_max,';
		$SQL .= '  case when 0.1 > new_bid_modifier and new_bid_modifier > 0 then 0.1 else new_bid_modifier end as new_bid_modifier,';
		$SQL .= '       now(),';
		$SQL .= '       now(),';
		$SQL .= '       "'.\Session::get('user_id_sem').'",';
		$SQL .= '       null,';
		$SQL .= '       null';
		$SQL .= '  from '.$sum_table;
		$SQL .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and device      = "PC+TAB"';
		## Conversion Optimizer対応
		$SQL .= '   and cpc_max > 0';
		$SQL .= '   and new_cpc_max > 0';
		$SQL .= '   and (';
		$SQL .= '       (new_cpc_max != cpc_max and new_cpc_max > 0)';
		$SQL .= '    or (new_bid_modifier != bid_modifier and new_bid_modifier is not null))';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 入札対象広告グループ一覧取得
	############################################################################
	public static function get_bid_adgroup_list($wabisabi_id, $target_date) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'campaign_id',
						  'adgroup_id',
						  'bid_modifier',
						  'new_bid_modifier');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('new_cpc_update_date', $target_date)
			->where('bid_modifier', '!=', DB::expr('`new_bid_modifier`'));
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id');

		return $SQL->execute('administrator')->as_array();
	}

	############################################################################
	## 入札対象キーワード一覧取得
	############################################################################
	public static function get_bid_keyword_list($wabisabi_id, $target_date) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'campaign_id',
						  'adgroup_id',
						  'keyword_id',
						  'cpc_max',
						  'new_cpc_max');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('new_cpc_update_date', $target_date)
			->where('new_cpc_max', '>', 0)
			->where('cpc_max', '!=', DB::expr('`new_cpc_max`'));
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id', 'keyword_id');

		return $SQL->execute('administrator')->as_array();
	}

	############################################################################
	## 入札対象一覧取得
	############################################################################
	public static function get_bid_list($wabisabi_id, $target_date) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'account_name',
						  'campaign_id',
						  'campaign_name',
						  'adgroup_id',
						  'adgroup_name',
						  'keyword_id',
						  'keyword',
						  'cpc_max',
						  'new_cpc_max',
						  'bid_modifier',
						  'new_bid_modifier',
						  'new_cpc_update_date');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('new_cpc_update_date', $target_date)
			->and_where_open()
				->or_where_open()
					->where('new_cpc_max', '>', 0)
					->where('cpc_max', '!=', DB::expr('`new_cpc_max`'))
				->or_where_close()
				->or_where('bid_modifier', '!=', DB::expr('`new_bid_modifier`'))
			->and_where_close();

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 入札対象件数取得
	############################################################################
	public static function get_bid_count($wabisabi_id) {

		$SQL = DB::select('wabisabi_id',
						  'new_cpc_update_date',
						  DB::expr('count(*) as count'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->and_where_open()
				->or_where_open()
					->where('new_cpc_max', '>', 0)
					->where('cpc_max', '!=', DB::expr('`new_cpc_max`'))
				->or_where_close()
				->or_where('bid_modifier', '!=', DB::expr('`new_bid_modifier`'))
			->and_where_close();
		$SQL->group_by('wabisabi_id', 'new_cpc_update_date');
		$SQL->order_by('new_cpc_update_date', 'desc');

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 入札対象のアカウント一覧取得
	############################################################################
	public static function get_bid_account_list($wabisabi_id, $target_date) {

		$SQL = DB::select('media_id',
						  'account_id');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('new_cpc_update_date', $target_date);
		$SQL->group_by('media_id', 'account_id');

		return $SQL->execute('administrator')->as_array();
	}
}
