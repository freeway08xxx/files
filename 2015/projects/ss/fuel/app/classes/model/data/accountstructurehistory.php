<?php

class Model_Data_Accountstructurehistory extends \Model {

	// テーブル名
	protected static $_table_name = "t_accountstructure_history";
	
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
							"status = VALUES(status)"
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
		return $SQL->execute("searchsuite_structure_admin")->as_array();
	}

	/*========================================================================*/
	/* 更新
	/*========================================================================*/
	public static function upd($params, $conditions=null) {
		$SQL = DB::update(self::$_table_name);
		$SQL->set($params);
		$SQL = self::get_where($SQL, $conditions);
		return $SQL->execute("searchsuite_structure_admin");
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
