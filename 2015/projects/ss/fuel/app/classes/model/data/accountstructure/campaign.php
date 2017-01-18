<?php

class Model_Data_Accountstructure_Campaign extends \Model {

	// テーブル名
	protected static $_table_name = "s_campaign";
	
	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {
		$column = array_keys(reset($values));
		$SQL = DB::insert(self::$_table_name, $column);
		foreach ($values as $value) {
			$SQL->values($value);
		}
		$SQL->set_duplicate([
							 "campaign_name = VALUES(campaign_name)"
							,"status = VALUES(status)"
							,"budget_id = VALUES(budget_id)"
							,"daily_budget = VALUES(daily_budget)"
							,"deliver = VALUES(deliver)"
							,"bid_modifier = VALUES(bid_modifier)"
							]
							);

		return $SQL->execute("searchsuite_structure_admin");
	}
	
	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get_list($columns, $conditions=null) {
		$SQL = DB::select(implode(',', $columns));
		$SQL->from(self::$_table_name);
		$SQL = self::get_where($SQL, $conditions);
		return $SQL->execute("searchsuite_structure")->as_array();
	}
	
	/*========================================================================*/
	/* 検索条件
	/*========================================================================*/
	private static function get_where($SQL, $conditions) {
		if ($conditions) {
			foreach ($conditions as $condition) {
				$SQL->where($condition['key'], $condition['operator'], $condition['value']);
			}
		}
		return $SQL;
	}
}
