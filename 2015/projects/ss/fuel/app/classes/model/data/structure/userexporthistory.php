<?php

class Model_Data_Structure_UserExportHistory extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_structure_user_export_history";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($limit=100) {
		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->limit($limit);
		$SQL->order_by('id', 'desc');
		return $SQL->execute("readonly")->as_array();
	}

	public static function get_by_id($id) {
		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('id',$id);
		return $SQL->execute("readonly")->current();
	}

	public static function ins($values){
		$query = DB::insert(self::$_table_name, array_keys($values));
		$query->values(array_values($values));
		return $query->execute();
	}

	public static function upd($id, $values){
		$query = DB::update(self::$_table_name);
		$query->set($values);
		$query->where('id',$id);
		return $query->execute();
	}

}
