<?php

class Model_Data_Accountstructure_Ad extends \Model {

	// テーブル名
	protected static $_table_name = "s_ad";
	
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
							 "ad_name = VALUES(ad_name)"
							,"status = VALUES(status)"
							,"approval_status = VALUES(approval_status)"
							,"device_preference = VALUES(device_preference)"
							]
							);

		return $SQL->execute("searchsuite_structure_admin");
	}
}
