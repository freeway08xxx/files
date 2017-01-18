<?php

class Model_Data_Accountstructure_Adgroup extends \Model {

	// テーブル名
	protected static $_table_name = "s_adgroup";
	
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
							 "adgroup_name = VALUES(adgroup_name)"
							,"status = VALUES(status)"
							,"cpc_update_day = case when (cpc_max != VALUES(cpc_max) or bid_modifier != VALUES(bid_modifier)) then now() else cpc_update_day end"
							,"cpc_max = VALUES(cpc_max)"
							]
							);

		return $SQL->execute("searchsuite_structure_admin");
	}
}
