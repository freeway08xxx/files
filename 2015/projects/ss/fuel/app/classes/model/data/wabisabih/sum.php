<?php
class Model_Data_WabiSabiH_Sum extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_sum';

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
								'rank        = VALUES(rank)',
								'report_days = report_days + VALUES(report_days)',
								'avg_cost    = sum_cost / report_days')
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
								'sum_conv    = sum_conv + VALUES(sum_conv)',
								'report_days = report_days + VALUES(report_days)',
								'avg_cost    = sum_cost / report_days')
							);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 最長入札変更日以降に入札された実績ありキーワード一覧取得
	############################################################################
	public static function get_update_report_keyword_list($wabisabi_id, $max_cpc_update_date) {

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
						  'cpc_update_date');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('cpc_update_date', '>', $max_cpc_update_date)
			->where('sum_imp', '>', 0);
		$SQL->group_by('wabisabi_id','client_id','media_id','account_id','campaign_id','adgroup_id','keyword_id');

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
	## 最長入札変更日以降に入札されたCVありキーワード一覧取得
	############################################################################
	public static function get_update_extcv_keyword_list($wabisabi_id, $max_cpc_update_date) {

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
						  'cpc_update_date');
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('cpc_update_date', '>', $max_cpc_update_date)
			->where('sum_conv', '>', 0);
		$SQL->group_by('wabisabi_id','client_id','media_id','account_id','campaign_id','adgroup_id','keyword_id');

		return $SQL->execute('administrator')->as_array();
	}

	############################################################################
	## 新入札調整率増加数算出
	############################################################################
	public static function upd_new_bid_modifier_increase_value_STEP1_1($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = -0.25';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv + sum_adg_sp.sum_conv > 4';
		$SQL .= '   and sum_adg_sp.sum_cost / sum_adg_sp.sum_conv > '.$target_cpa;
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) <= 0.5';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP1_2($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = -1 * (1 - (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv)) * 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv + sum_adg_sp.sum_conv > 4';
		$SQL .= '   and sum_adg_sp.sum_cost / sum_adg_sp.sum_conv > '.$target_cpa;
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) >  0.5';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) <= 0.8';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP1_3($wabisabi_id) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = ((sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) - 1) * 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.rank       >= '.RANK_LV1;
		$SQL .= '   and sum_adg_pc.sum_conv + sum_adg_sp.sum_conv > 4';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) >  1.2';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) <= 2.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP1_4($wabisabi_id) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 0';
		$SQL .= '   and sum_adg_sp.rank       >= '.RANK_LV1;
		$SQL .= '   and sum_adg_pc.sum_conv + sum_adg_sp.sum_conv > 4';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) > 2.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP2_1($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = -0.25';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 4';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_cost    > '.$target_cpa.' * 0.5';
		$SQL .= '   and sum_adg_sp.sum_conv    = 0';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / sum_adg_sp.sum_cost <= 0.5';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP2_2($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = -1 * (1 - (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / sum_adg_sp.sum_cost) * 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_imp     > 0';
		$SQL .= '   and sum_adg_pc.sum_cost    > 0';
		$SQL .= '   and sum_adg_pc.sum_conv    > 4';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_cost    > '.$target_cpa.' * 0.5';
		$SQL .= '   and sum_adg_sp.sum_conv    = 0';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / sum_adg_sp.sum_cost >  0.5';
		$SQL .= '   and (sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / sum_adg_sp.sum_cost <= 0.8';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP3_1($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = ((sum_adg_pc.sum_cost / sum_adg_pc.sum_conv) / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) - 1) * 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_cost    > '.$target_cpa;
		$SQL .= '   and sum_adg_pc.sum_conv    = 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 4';
		$SQL .= '   and sum_adg_sp.rank       >= '.RANK_LV1;
		$SQL .= '   and '.$target_cpa.' / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) >  1.2';
		$SQL .= '   and '.$target_cpa.' / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) <= 2.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_modifier_increase_value_STEP3_2($wabisabi_id, $target_cpa) {

		$sum_adg_table = 't_wabisabih_sum_adg';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_pc';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_pc.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_pc.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_pc.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_pc.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_pc.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_pc.adgroup_id';
		$SQL .= ' inner join '.$sum_adg_table.' as sum_adg_sp';
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = sum_adg_sp.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = sum_adg_sp.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = sum_adg_sp.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = sum_adg_sp.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = sum_adg_sp.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = sum_adg_sp.adgroup_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_modifier_increase_value = 0.5';
		$SQL .= ' where sum_adg_pc.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_pc.device      = "PC+TAB"';
		$SQL .= '   and sum_adg_pc.sum_cost    > '.$target_cpa;
		$SQL .= '   and sum_adg_pc.sum_conv    = 0';
		$SQL .= '   and sum_adg_sp.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and sum_adg_sp.device      = "SP"';
		$SQL .= '   and sum_adg_sp.sum_imp     > 0';
		$SQL .= '   and sum_adg_sp.sum_cost    > 0';
		$SQL .= '   and sum_adg_sp.sum_conv    > 4';
		$SQL .= '   and sum_adg_sp.rank       >= '.RANK_LV1;
		$SQL .= '   and '.$target_cpa.' / (sum_adg_sp.sum_cost / sum_adg_sp.sum_conv) > 2.0';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 新入札調整率更新
	############################################################################
	public static function upd_new_bid_modifier($wabisabi_id, $limit_mba = null) {

		$SQL = DB::update(self::$_table_name);
		if (isset($limit_mba)) {
			$SQL->value('new_bid_modifier', DB::expr('case when bid_modifier = 0 then 0'
														.' when bid_modifier is not null and bid_modifier + new_bid_modifier_increase_value > '.$limit_mba.' then '.$limit_mba
														.' when bid_modifier is not null and bid_modifier + new_bid_modifier_increase_value < 0 then 0'
														.' when bid_modifier is not null then bid_modifier + new_bid_modifier_increase_value'
														.' when new_bid_modifier_increase_value != 0 and 1 + new_bid_modifier_increase_value > '.$limit_mba.' then '.$limit_mba
														.' when new_bid_modifier_increase_value != 0 and 1 + new_bid_modifier_increase_value < 0 then 0'
														.' when new_bid_modifier_increase_value != 0 then 1 + new_bid_modifier_increase_value'
														.' else bid_modifier end'));
			$SQL->value('new_bid_modifier_increase_value', DB::expr('case when bid_modifier = 0 then 0 when bid_modifier != new_bid_modifier then new_bid_modifier - bid_modifier end'));
		} else {
			$SQL->value('new_bid_modifier', DB::expr('case when bid_modifier = 0 then 0'
														.' when bid_modifier is not null and bid_modifier + new_bid_modifier_increase_value > 4 then 4'
														.' when bid_modifier is not null and bid_modifier + new_bid_modifier_increase_value < 0 then 0'
														.' when bid_modifier is not null then bid_modifier + new_bid_modifier_increase_value'
														.' when new_bid_modifier_increase_value != 0 and 1 + new_bid_modifier_increase_value > 4 then 4'
														.' when new_bid_modifier_increase_value != 0 and 1 + new_bid_modifier_increase_value < 0 then 0'
														.' when new_bid_modifier_increase_value != 0 then 1 + new_bid_modifier_increase_value'
														.' else bid_modifier end'));
			$SQL->value('new_bid_modifier_increase_value', DB::expr('case when bid_modifier = 0 then 0 when bid_modifier != new_bid_modifier then new_bid_modifier - bid_modifier end'));
		}
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator');
	}

	############################################################################
	## KW単位の掲載順位更新
	############################################################################
	public static function upd_kw_rank($wabisabi_id) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id   = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id    = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id  = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id  = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id  = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.kw_rank = '.$sum_kw_table.'.rank';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 入札調整フラグ更新
	############################################################################
	public static function upd_bid_adjust_flg_A($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "A_CPA1"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 0.5';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_B($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "B_CPA2"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa.' * 0.5';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa;

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_C($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "C_CPA3"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa;
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 1.5 ';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_D($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "D_CPA4"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa.' * 1.5';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 2.0 ';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_E($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "E_CPA5"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa.' * 2.0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 3.0 ';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_F($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "F_CPA6"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa.' * 3.0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 4.0 ';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_G($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "G_CPA7"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) >  '.$target_cpa.' * 4.0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) <= '.$target_cpa.' * 5.0 ';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_H($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "H_CPA8"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    > 0';
		$SQL .= '   and ('.$sum_kw_table.'.sum_cost / '.$sum_kw_table.'.sum_conv) > '.$target_cpa.' * 5.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_I($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "I_No_Cost"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    = 0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_J($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "J_Cost8"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 0.5';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_K($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "K_Cost7"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 0.5';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa;

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_L($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "L_Cost6"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa;
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 1.5';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_M($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "M_Cost5"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 1.5';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 2.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_N($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "N_Cost4"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 2.0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 3.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_O($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "O_Cost3"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 3.0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 4.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_P($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "P_Cost2"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 4.0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost   <= '.$target_cpa.' * 5.0';

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_bid_adjust_flg_Q($wabisabi_id, $target_cpa) {

		$sum_kw_table = 't_wabisabih_sum_kw';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum_kw_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id    = '.$sum_kw_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id      = '.$sum_kw_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id       = '.$sum_kw_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id     = '.$sum_kw_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id    = '.$sum_kw_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id     = '.$sum_kw_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id     = '.$sum_kw_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.bid_adjust_flg = "Q_Cost1"';
		$SQL .= ' where '.$sum_kw_table.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.$sum_kw_table.'.sum_imp     > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_conv    = 0';
		$SQL .= '   and '.$sum_kw_table.'.sum_cost    > '.$target_cpa.' * 5.0';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 入札調整上限フラグ更新（プラス＆マイナス調整の対象外）
	############################################################################
	public static function upd_bid_adjust_limit_flg($wabisabi_id, $limit_cpc) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_limit_flg', DB::expr('case when cpc_max = '.$limit_cpc.' then "'.BID_ADJUST_LIMIT_FLG_LIMIT.'" else "'.BID_ADJUST_LIMIT_FLG_OVER.'" end'));
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('cpc_max', '>=', $limit_cpc);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 上限入札額に達するポテンシャルフラグ更新（プラス調整時の上限入札額超え判定する際に処理件数削減の為に利用）
	############################################################################
	public static function upd_bid_adjust_limit_potential_flg($wabisabi_id, $limit_cpc, $new_bid_rate_max) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_limit_potential_flg', BID_ADJUST_LIMIT_POTENTIAL_ON);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where(DB::expr('cpc_max * '.$new_bid_rate_max), '>=', $limit_cpc);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 挿入（基準日CPC算出用データ反映）
	############################################################################
	public static function ins_refdata($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array(
								'reference_click = reference_click + VALUES(reference_click)',
								'reference_cost  = reference_cost + VALUES(reference_cost)')
							);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 基準日の合計コスト取得
	############################################################################
	public static function get_sum_ref_cost($wabisabi_id) {

		$SQL = DB::select(array(DB::expr('ifnull(sum(reference_cost), 0)'), 'sum_ref_cost'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'not in', array('I_No_Cost','R_No_Imp'));

		return $SQL->execute('administrator')->current();
	}

	############################################################################
	## 基準日CPCより平均コスト算出・反映（I_No_Cost,R_No_Imp）
	############################################################################
	public static function get_avg_cost_nocost_noimp($wabisabi_id) {

		$SQL = DB::select(array(DB::expr('case when sum(reference_cost) > 0 then sum(reference_cost) / sum(reference_click) else 0 end'), 'tmp_avg_cost'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator')->current();
	}

	public static function upd_avg_cost_nocost_noimp($wabisabi_id, $tmp_avg_cost) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum45_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = '.$sum45_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = '.$sum45_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = '.$sum45_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = '.$sum45_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = '.$sum45_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = '.$sum45_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id   = '.$sum45_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.avg_cost = ( case when '.$sum45_table.'.sum_cost > 0 then '.$tmp_avg_cost.'/'.AVG_COST_RATE.' else '.$tmp_avg_cost.'/'.AVG_COST_RATE_OF_NOCOST.' end )';
		$SQL .= ' where '.self::$_table_name.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.self::$_table_name.'.avg_cost    = 0';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 新入札係数＆新コスト増加係数算出
	############################################################################
	public static function upd_new_bid_rate_STEP1_1($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_3'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_3']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'A_CPA1')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV1)
			->where('kw_rank', '<',  RANK_LV2)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP1_2($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_4'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_4']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'A_CPA1')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV2)
			->where('kw_rank', '<',  RANK_LV3)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP1_3($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_5'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_5']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'A_CPA1')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV3)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP2_1($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_1'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_1']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'B_CPA2')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV1)
			->where('kw_rank', '<',  RANK_LV2)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP2_2($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_2'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_2']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'B_CPA2')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV2)
			->where('kw_rank', '<',  RANK_LV3)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP2_3($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['pos_3'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['pos_3']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'B_CPA2')
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('kw_rank', '>=', RANK_LV3)
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP3($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['neg_4'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['neg_4']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'in', array('N_Cost4','O_Cost3','P_Cost2','Q_Cost1'))
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP4($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['neg_3'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['neg_3']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'M_Cost5')
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP5($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['neg_2'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['neg_2']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'L_Cost6')
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP6($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['neg_3'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['neg_3']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'in', array('E_CPA5','F_CPA6','G_CPA7','H_CPA8'))
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	public static function upd_new_bid_rate_STEP7($wabisabi_id, $new_bid_rate_list) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_bid_rate', $new_bid_rate_list['neg_2'])
			->value('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][$new_bid_rate_list['neg_2']]);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', 'D_CPA4')
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where('cpc_max', '>', 0);

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 上限入札額設定(プラス調整＆引き上げ)
	############################################################################
	public static function get_new_cpc_max_upper($wabisabi_id, $limit_cpc) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'campaign_id',
						  'adgroup_id',
						  'keyword_id',
						  'cpc_max',
						  DB::expr('case when bid_modifier is null then 1 else bid_modifier end as bid_modifier'),
						  'new_bid_modifier_increase_value',
						  DB::expr('case when new_bid_modifier is null then 1 else new_bid_modifier end as new_bid_modifier'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UNDER)
			->where('new_bid_rate',  '>', NEW_BID_RATE_DEFAULT)
			->where('new_cost_rate', '>', $GLOBALS['NEW_COST_RATE_LIST'][NEW_BID_RATE_DEFAULT])
			->where('cpc_max * new_bid_rate', '>', $limit_cpc);
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id', 'keyword_id');

		return $SQL->execute('administrator')->as_array();
	}

	public static function upd_new_cpc_max_upper($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $adgroup_id, $keyword_id, $device, $limit_cpc, $new_cost_rate) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_UPPER)
			->value('new_cpc_max', $limit_cpc)
			->value('new_increase_cost', DB::expr('case when bid_adjust_flg in ("I_No_Cost","R_No_Imp") then avg_cost else avg_cost * '.$new_cost_rate.' end'));
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('client_id', $client_id)
			->where('media_id', $media_id)
			->where('account_id', $account_id)
			->where('campaign_id', $campaign_id)
			->where('adgroup_id', $adgroup_id)
			->where('keyword_id', $keyword_id)
			->where('device', $device);

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 上限入札額設定(引き下げ)
	############################################################################
	public static function get_new_cpc_max_lower($wabisabi_id, $limit_cpc) {

		$SQL = DB::select('wabisabi_id',
						  'client_id',
						  'media_id',
						  'account_id',
						  'campaign_id',
						  'adgroup_id',
						  'keyword_id',
						  'cpc_max',
						  DB::expr('case when bid_modifier is null then 1 else bid_modifier end as bid_modifier'),
						  'new_bid_modifier_increase_value',
						  DB::expr('case when new_bid_modifier is null then 1 else new_bid_modifier end as new_bid_modifier'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_OVER)
			->where('new_bid_rate', NEW_BID_RATE_DEFAULT)
			->where('new_cost_rate', $GLOBALS['NEW_COST_RATE_LIST'][NEW_BID_RATE_DEFAULT])
			->where('cpc_max', '>', $limit_cpc);
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id', 'keyword_id');

		return $SQL->execute('administrator')->as_array();
	}

	public static function upd_new_cpc_max_lower($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $adgroup_id, $keyword_id, $device, $limit_cpc, $new_cost_rate) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('bid_adjust_limit_flg', BID_ADJUST_LIMIT_FLG_LOWER)
			->value('new_cpc_max', $limit_cpc)
		## 引き下げ＆コストなしの増加コストは０
			->value('new_increase_cost', DB::expr('case when sum_cost = 0 then 0 else avg_cost * '.$new_cost_rate.' end'));
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('client_id', $client_id)
			->where('media_id', $media_id)
			->where('account_id', $account_id)
			->where('campaign_id', $campaign_id)
			->where('adgroup_id', $adgroup_id)
			->where('keyword_id', $keyword_id)
			->where('device', $device);

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 入札調整率の変更に伴うスマホの新コスト増加係数再算出対象取得
	############################################################################
	public static function get_upd_new_cost_rate($wabisabi_id, $client_id = null, $media_id = null, $account_id = null, $campaign_id = null, $adgroup_id = null, $keyword_id = null) {

		$SQL = DB::select('SP.wabisabi_id',
						  'SP.client_id',
						  'SP.media_id',
						  'SP.account_id',
						  'SP.campaign_id',
						  'SP.adgroup_id',
						  'SP.keyword_id',
						  'SP.device',
						  'SP.cpc_max',
						  DB::expr('case when SP.bid_modifier is null then 1 else SP.bid_modifier end as bid_modifier'),
						  'SP.new_bid_rate',
						  DB::expr('case when SP.new_bid_modifier is null then 1 else SP.new_bid_modifier end as new_bid_modifier'));
		$SQL->from(array(self::$_table_name, 'PC'));
		$SQL->join(array(self::$_table_name, 'SP'), 'INNER')
			->on('PC.wabisabi_id', '=', 'SP.wabisabi_id')
			->on('PC.client_id', '=', 'SP.client_id')
			->on('PC.media_id', '=', 'SP.media_id')
			->on('PC.account_id', '=', 'SP.account_id')
			->on('PC.campaign_id', '=', 'SP.campaign_id')
			->on('PC.adgroup_id', '=', 'SP.adgroup_id')
			->on('PC.keyword_id', '=', 'SP.keyword_id');
		$SQL->where('PC.wabisabi_id', $wabisabi_id)
			->where('SP.wabisabi_id', $wabisabi_id);
		if (isset($campaign_id)) {
			$SQL->where('PC.client_id', $client_id)
				->where('SP.client_id', $client_id)
				->where('PC.media_id', $media_id)
				->where('SP.media_id', $media_id)
				->where('PC.account_id', $account_id)
				->where('SP.account_id', $account_id)
				->where('PC.campaign_id', $campaign_id)
				->where('SP.campaign_id', $campaign_id);
		}
		if (isset($keyword_id)) {
			$SQL->where('PC.adgroup_id', $adgroup_id)
				->where('SP.adgroup_id', $adgroup_id)
				->where('PC.keyword_id', $keyword_id)
				->where('SP.keyword_id', $keyword_id);
		}
		$SQL->where('PC.device', 'PC+TAB')
			->where('SP.device', 'SP')
		## Conversion Optimizer対応
			->where('PC.cpc_max', '>', 0)
			->where('SP.cpc_max', '>', 0)
			->where('PC.bid_modifier', '!=', DB::expr('`PC`.`new_bid_modifier`'))
			->where('SP.bid_modifier', '!=', DB::expr('`SP`.`new_bid_modifier`'));

		return DB::query($SQL)->execute('administrator')->as_array();
	}

	############################################################################
	## 新コスト増加係数更新
	############################################################################
	public static function upd_new_cost_rate($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $adgroup_id, $keyword_id, $device, $new_cost_rate) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_cost_rate', $new_cost_rate);
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('client_id', $client_id)
			->where('media_id', $media_id)
			->where('account_id', $account_id)
			->where('campaign_id', $campaign_id)
			->where('adgroup_id', $adgroup_id)
			->where('keyword_id', $keyword_id)
			->where('device', $device);

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 予測増加コスト算出(プラス＆マイナス調整前)
	############################################################################
	public static function get_sum_new_increase_cost($wabisabi_id) {

		## プラス＆マイナス調整対象
		$SQL_1  = 'select case when bid_adjust_flg in ("I_No_Cost","R_No_Imp") then avg_cost else avg_cost * new_cost_rate end as new_increase_cost';
		$SQL_1 .= '  from '.self::$_table_name;
		$SQL_1 .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL_1 .= '   and bid_adjust_limit_flg in ("'.BID_ADJUST_LIMIT_FLG_UNDER.'","'.BID_ADJUST_LIMIT_FLG_LIMIT.'")';
		$SQL_1 .= '   and new_bid_rate  != '.NEW_BID_RATE_DEFAULT;
		$SQL_1 .= '   and new_cost_rate != '.$GLOBALS['NEW_COST_RATE_LIST'][NEW_BID_RATE_DEFAULT];

		## 上限入札額設定(プラス調整＆引き上げ)
		$SQL_2  = 'select new_increase_cost';
		$SQL_2 .= '  from '.self::$_table_name;
		$SQL_2 .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL_2 .= '   and bid_adjust_limit_flg = "'.BID_ADJUST_LIMIT_FLG_UPPER.'"';
		$SQL_2 .= '   and new_bid_rate  > '.NEW_BID_RATE_DEFAULT;
		$SQL_2 .= '   and new_cost_rate > '.$GLOBALS['NEW_COST_RATE_LIST'][NEW_BID_RATE_DEFAULT];

		## 上限入札額設定(引き下げ)
		$SQL_3  = 'select new_increase_cost';
		$SQL_3 .= '  from '.self::$_table_name;
		$SQL_3 .= ' where wabisabi_id = '.$wabisabi_id;
		$SQL_3 .= '   and bid_adjust_limit_flg = "'.BID_ADJUST_LIMIT_FLG_LOWER.'"';
		$SQL_3 .= '   and new_bid_rate  = '.NEW_BID_RATE_DEFAULT;
		$SQL_3 .= '   and new_cost_rate = '.$GLOBALS['NEW_COST_RATE_LIST'][NEW_BID_RATE_DEFAULT];

		## サマリ
		$SQL  = 'select ifnull(sum(new_increase_cost), 0) as sum_new_increase_cost';
		$SQL .= '  from ( (';
		$SQL .= $SQL_1;
		$SQL .= ') union all (';
		$SQL .= $SQL_2;
		$SQL .= ') union all (';
		$SQL .= $SQL_3;
		$SQL .= ') ) as tmp';

		return DB::query($SQL)->execute('administrator')->current();
	}

	############################################################################
	## プラス＆マイナス調整対象キーワード一覧取得
	############################################################################
	public static function get_bid_adjust_keyword($wabisabi_id, $mode, $sum_bid_adjust_flg_list = array(), $sum45_bid_adjust_flg_list = array()) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL = DB::select(self::$_table_name.'.wabisabi_id',
						  self::$_table_name.'.client_id',
						  self::$_table_name.'.media_id',
						  self::$_table_name.'.account_id',
						  self::$_table_name.'.campaign_id',
						  self::$_table_name.'.adgroup_id',
						  self::$_table_name.'.keyword_id',
						  self::$_table_name.'.kw_rank',
						  self::$_table_name.'.cpc_max',
						  DB::expr('case when '.self::$_table_name.'.bid_modifier is null then 1 else '.self::$_table_name.'.bid_modifier end as bid_modifier'),
						  'new_bid_modifier_increase_value',
						  DB::expr('case when '.self::$_table_name.'.new_bid_modifier is null then 1 else '.self::$_table_name.'.new_bid_modifier end as new_bid_modifier'),
						  self::$_table_name.'.new_bid_rate',
						  self::$_table_name.'.bid_adjust_limit_potential_flg');
		$SQL->from(self::$_table_name);
		$SQL->join($sum45_table, 'INNER')
			->on(self::$_table_name.'.wabisabi_id', '=', $sum45_table.'.wabisabi_id')
			->on(self::$_table_name.'.client_id',   '=', $sum45_table.'.client_id')
			->on(self::$_table_name.'.media_id',    '=', $sum45_table.'.media_id')
			->on(self::$_table_name.'.account_id',  '=', $sum45_table.'.account_id')
			->on(self::$_table_name.'.campaign_id', '=', $sum45_table.'.campaign_id')
			->on(self::$_table_name.'.adgroup_id',  '=', $sum45_table.'.adgroup_id')
			->on(self::$_table_name.'.keyword_id',  '=', $sum45_table.'.keyword_id');
		$SQL->where(self::$_table_name.'.wabisabi_id', $wabisabi_id)
			->where(self::$_table_name.'.bid_adjust_flg', 'in', $sum_bid_adjust_flg_list)
			->where(self::$_table_name.'.bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where(self::$_table_name.'.cpc_max', '>', 0);
		if (!empty($sum45_bid_adjust_flg_list)) {
			$SQL->where($sum45_table.'.bid_adjust_flg', 'in', $sum45_bid_adjust_flg_list);
		}
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id', 'keyword_id');
		## プラス調整の場合、CPAの昇順・Costの昇順・Impの降順
		if ($mode === 'positive') {
			$SQL->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost) / sum('.self::$_table_name.'.sum_conv) is null'), 'asc')
				->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost) / sum('.self::$_table_name.'.sum_conv)'), 'asc')
				->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost)'), 'asc')
				->order_by(DB::expr('sum('.self::$_table_name.'.sum_imp)'), 'desc');
		## マイナス調整の場合、プラス調整の逆順
		} else {
			$SQL->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost) / sum('.self::$_table_name.'.sum_conv)'), 'desc')
				->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost)'), 'desc')
				->order_by(DB::expr('sum('.self::$_table_name.'.sum_imp)'), 'asc');
		}

		return DB::query($SQL)->execute('administrator')->as_array();
	}

	############################################################################
	## プラス調整6以外の入札調整処理
	############################################################################
	public static function upd_bid_adjust_by_keyword($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $adgroup_id, $keyword_id, $new_bid_rate, $new_cost_rate) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum45_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = '.$sum45_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = '.$sum45_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = '.$sum45_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = '.$sum45_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = '.$sum45_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = '.$sum45_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id   = '.$sum45_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_rate  = '.$new_bid_rate.',';
		$SQL .= '       '.self::$_table_name.'.new_cost_rate = '.$new_cost_rate;
		$SQL .= ' where '.self::$_table_name.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.self::$_table_name.'.client_id   = '.$client_id;
		$SQL .= '   and '.self::$_table_name.'.media_id    = '.$media_id;
		$SQL .= '   and '.self::$_table_name.'.account_id  = "'.$account_id.'"';
		$SQL .= '   and '.self::$_table_name.'.campaign_id = "'.$campaign_id.'"';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id  = "'.$adgroup_id.'"';
		$SQL .= '   and '.self::$_table_name.'.keyword_id  = "'.$keyword_id.'"';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## プラス調整6の入札調整処理
	############################################################################
	public static function upd_bid_adjust_by_campaign($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $new_bid_rate, $new_cost_rate, $sum_bid_adjust_flg_list = array(), $sum45_bid_adjust_flg_list = array(), $sum_kw_rank = null) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum45_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = '.$sum45_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = '.$sum45_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = '.$sum45_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = '.$sum45_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = '.$sum45_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = '.$sum45_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id   = '.$sum45_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.new_bid_rate  = '.$new_bid_rate.',';
		$SQL .= '       '.self::$_table_name.'.new_cost_rate = '.$new_cost_rate;
		$SQL .= ' where '.self::$_table_name.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.self::$_table_name.'.client_id   = '.$client_id;
		$SQL .= '   and '.self::$_table_name.'.media_id    = '.$media_id;
		$SQL .= '   and '.self::$_table_name.'.account_id  = "'.$account_id.'"';
		$SQL .= '   and '.self::$_table_name.'.campaign_id = "'.$campaign_id.'"';
		$SQL .= '   and '.self::$_table_name.'.bid_adjust_flg in ("'.implode('","', $sum_bid_adjust_flg_list).'")';
		$SQL .= '   and '.self::$_table_name.'.bid_adjust_limit_flg in ("'.BID_ADJUST_LIMIT_FLG_UNDER.'","'.BID_ADJUST_LIMIT_FLG_LIMIT.'")';
		if (!empty($sum45_bid_adjust_flg_list)) {
			$SQL .= '   and '.$sum45_table.'.bid_adjust_flg in ("'.implode('","', $sum45_bid_adjust_flg_list).'")';
		}
		if ($sum_kw_rank == RANK_LV1) {
			$SQL .= '   and '.RANK_LV1.' <= '.self::$_table_name.'.kw_rank and '.self::$_table_name.'.kw_rank < '.RANK_LV2;
		} elseif ($sum_kw_rank == RANK_LV2) {
			$SQL .= '   and '.RANK_LV2.' <= '.self::$_table_name.'.kw_rank and '.self::$_table_name.'.kw_rank < '.RANK_LV3;
		} elseif ($sum_kw_rank == RANK_LV3) {
			$SQL .= '   and '.RANK_LV3.' <= '.self::$_table_name.'.kw_rank';
		}

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## プラス調整6対象キーワード一覧取得
	############################################################################
	public static function get_bid_adjust_keyword_by_campaign($wabisabi_id, $client_id, $media_id, $account_id, $campaign_id, $sum_bid_adjust_flg_list = array(), $sum45_bid_adjust_flg_list = array(), $sum_kw_rank = null) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL = DB::select(self::$_table_name.'.wabisabi_id',
						  self::$_table_name.'.client_id',
						  self::$_table_name.'.media_id',
						  self::$_table_name.'.account_id',
						  self::$_table_name.'.campaign_id',
						  self::$_table_name.'.adgroup_id',
						  self::$_table_name.'.keyword_id',
						  self::$_table_name.'.kw_rank',
						  self::$_table_name.'.cpc_max',
						  DB::expr('case when '.self::$_table_name.'.bid_modifier is null then 1 else '.self::$_table_name.'.bid_modifier end as bid_modifier'),
						  'new_bid_modifier_increase_value',
						  DB::expr('case when '.self::$_table_name.'.new_bid_modifier is null then 1 else '.self::$_table_name.'.new_bid_modifier end as new_bid_modifier'),
						  self::$_table_name.'.new_bid_rate',
						  self::$_table_name.'.bid_adjust_limit_potential_flg');
		$SQL->from(self::$_table_name);
		$SQL->join($sum45_table, 'INNER')
			->on(self::$_table_name.'.wabisabi_id', '=', $sum45_table.'.wabisabi_id')
			->on(self::$_table_name.'.client_id',   '=', $sum45_table.'.client_id')
			->on(self::$_table_name.'.media_id',    '=', $sum45_table.'.media_id')
			->on(self::$_table_name.'.account_id',  '=', $sum45_table.'.account_id')
			->on(self::$_table_name.'.campaign_id', '=', $sum45_table.'.campaign_id')
			->on(self::$_table_name.'.adgroup_id',  '=', $sum45_table.'.adgroup_id')
			->on(self::$_table_name.'.keyword_id',  '=', $sum45_table.'.keyword_id');
		$SQL->where(self::$_table_name.'.wabisabi_id', $wabisabi_id)
			->where(self::$_table_name.'.client_id', $client_id)
			->where(self::$_table_name.'.media_id', $media_id)
			->where(self::$_table_name.'.account_id', $account_id)
			->where(self::$_table_name.'.campaign_id', $campaign_id)
			->where(self::$_table_name.'.bid_adjust_flg', 'in', $sum_bid_adjust_flg_list)
			->where(self::$_table_name.'.bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT))
		## Conversion Optimizer対応
			->where(self::$_table_name.'.cpc_max', '>', 0);
		if (!empty($sum45_bid_adjust_flg_list)) {
			$SQL->where($sum45_table.'.bid_adjust_flg', 'in', $sum45_bid_adjust_flg_list);
		}
		if ($sum_kw_rank == RANK_LV1) {
			$SQL->where(self::$_table_name.'.kw_rank', '>=', RANK_LV1)
				->where(self::$_table_name.'.kw_rank',  '<', RANK_LV2);
		} elseif ($sum_kw_rank == RANK_LV2) {
			$SQL->where(self::$_table_name.'.kw_rank', '>=', RANK_LV2)
				->where(self::$_table_name.'.kw_rank',  '<', RANK_LV3);
		} elseif ($sum_kw_rank == RANK_LV3) {
			$SQL->where(self::$_table_name.'.kw_rank', '>=', RANK_LV3);
		}
		$SQL->group_by('wabisabi_id', 'client_id', 'media_id', 'account_id', 'campaign_id', 'adgroup_id', 'keyword_id');
		$SQL->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost) / sum('.self::$_table_name.'.sum_conv) is null'), 'asc')
			->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost) / sum('.self::$_table_name.'.sum_conv)'), 'asc')
			->order_by(DB::expr('sum('.self::$_table_name.'.sum_cost)'), 'asc')
			->order_by(DB::expr('sum('.self::$_table_name.'.sum_imp)'), 'desc');

		return DB::query($SQL)->execute('administrator')->as_array();
	}

	############################################################################
	## 新入札額＆増加コスト確定
	############################################################################
	public static function upd_new_cpc_and_cost($wabisabi_id) {

		$SQL = DB::update(self::$_table_name);
		$SQL->value('new_cpc_max', DB::expr('floor(cpc_max * new_bid_rate)'))
			->value('new_increase_cost', DB::expr('case when bid_adjust_flg in ("I_No_Cost","R_No_Imp") and new_bid_rate > '.NEW_BID_RATE_DEFAULT.' then avg_cost else avg_cost * new_cost_rate end'));
		$SQL->where('wabisabi_id', $wabisabi_id)
			->where('bid_adjust_flg', '!=', null)
			->where('bid_adjust_limit_flg', 'in', array(BID_ADJUST_LIMIT_FLG_UNDER,BID_ADJUST_LIMIT_FLG_LIMIT));

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## 予測増加コスト算出(プラス＆マイナス調整後)
	############################################################################
	public static function get_fix_sum_new_increase_cost($wabisabi_id) {

		$SQL = DB::select(DB::expr('sum(new_increase_cost) as sum_new_increase_cost'));
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return DB::query($SQL)->execute('administrator')->current();
	}

	############################################################################
	## データ45のCVRによる上限入札額設定(プラス調整1-5のみ)
	## 現入札額が既に上限額を超えている場合、入札しない
	############################################################################
	public static function upd_new_cpc_max_rollback($wabisabi_id, $target_cpa, $sum_bid_adjust_flg_list = array('I_No_Cost','J_Cost8','K_Cost7'), $sum45_bid_adjust_flg_list = array('A_CPA1','B_CPA2','C_CPA3')) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum45_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = '.$sum45_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = '.$sum45_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = '.$sum45_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = '.$sum45_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = '.$sum45_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = '.$sum45_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id   = '.$sum45_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.new_cpc_max  = '.self::$_table_name.'.cpc_max';
		$SQL .= ' where '.self::$_table_name.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.self::$_table_name.'.new_bid_rate > '.NEW_BID_RATE_DEFAULT;
		$SQL .= '   and '.self::$_table_name.'.new_cpc_max > '.self::$_table_name.'.cpc_max';
		$SQL .= '   and '.$sum45_table.'.sum_click > 0';
		$SQL .= '   and '.$sum45_table.'.sum_conv  > 0';
		$SQL .= '   and '.self::$_table_name.'.bid_adjust_flg in ("'.implode('","', $sum_bid_adjust_flg_list).'")';
		$SQL .= '   and '.self::$_table_name.'.kw_rank >= '.RANK_LV1;
		if (!empty($sum45_bid_adjust_flg_list)) {
			$SQL .= '   and '.$sum45_table.'.bid_adjust_flg in ("'.implode('","', $sum45_bid_adjust_flg_list).'")';
		}
		$SQL .= '   and '.self::$_table_name.'.cpc_max >= '.$target_cpa.' * '.NEW_CPC_MAX_LIMIT_RATE.' * ('.$sum45_table.'.sum_conv / '.$sum45_table.'.sum_click)';

		return DB::query($SQL)->execute('administrator');
	}

	############################################################################
	## データ45のCVRによる上限入札額設定(プラス調整1-5のみ)
	## 新入札額が上限額を超えた場合、上限額に更新
	############################################################################
	public static function upd_new_cpc_max_cap($wabisabi_id, $target_cpa, $sum_bid_adjust_flg_list = array('I_No_Cost','J_Cost8','K_Cost7'), $sum45_bid_adjust_flg_list = array('A_CPA1','B_CPA2','C_CPA3')) {

		$sum45_table = 't_wabisabih_sum45';

		$SQL  = 'update '.self::$_table_name;
		$SQL .= ' inner join '.$sum45_table;
		$SQL .= '    on '.self::$_table_name.'.wabisabi_id  = '.$sum45_table.'.wabisabi_id';
		$SQL .= '   and '.self::$_table_name.'.client_id    = '.$sum45_table.'.client_id';
		$SQL .= '   and '.self::$_table_name.'.media_id     = '.$sum45_table.'.media_id';
		$SQL .= '   and '.self::$_table_name.'.account_id   = '.$sum45_table.'.account_id';
		$SQL .= '   and '.self::$_table_name.'.campaign_id  = '.$sum45_table.'.campaign_id';
		$SQL .= '   and '.self::$_table_name.'.adgroup_id   = '.$sum45_table.'.adgroup_id';
		$SQL .= '   and '.self::$_table_name.'.keyword_id   = '.$sum45_table.'.keyword_id';
		$SQL .= '   set '.self::$_table_name.'.new_cpc_max  = '.$target_cpa.' * '.NEW_CPC_MAX_LIMIT_RATE.' * ('.$sum45_table.'.sum_conv / '.$sum45_table.'.sum_click)';
		$SQL .= ' where '.self::$_table_name.'.wabisabi_id = '.$wabisabi_id;
		$SQL .= '   and '.self::$_table_name.'.new_bid_rate > '.NEW_BID_RATE_DEFAULT;
		$SQL .= '   and '.self::$_table_name.'.new_cpc_max > '.self::$_table_name.'.cpc_max';
		$SQL .= '   and '.$sum45_table.'.sum_click > 0';
		$SQL .= '   and '.$sum45_table.'.sum_conv  > 0';
		$SQL .= '   and '.self::$_table_name.'.bid_adjust_flg in ("'.implode('","', $sum_bid_adjust_flg_list).'")';
		$SQL .= '   and '.self::$_table_name.'.kw_rank >= '.RANK_LV1;
		if (!empty($sum45_bid_adjust_flg_list)) {
			$SQL .= '   and '.$sum45_table.'.bid_adjust_flg in ("'.implode('","', $sum45_bid_adjust_flg_list).'")';
		}
		$SQL .= '   and '.self::$_table_name.'.new_cpc_max > '.$target_cpa.' * '.NEW_CPC_MAX_LIMIT_RATE.' * ('.$sum45_table.'.sum_conv / '.$sum45_table.'.sum_click)';

		return DB::query($SQL)->execute('administrator');
	}
}
