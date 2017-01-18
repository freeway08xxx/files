<?php

################################################################################
#
# Title : インフォメーション取得
#
#  2014/12/16  First Version
#_

################################################################################

class Model_Data_Information extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_information";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($type,$limit = null) {
	
		$SQL = DB::select("id",
						  "created_at",
						  "created_user",
						  "updated_at",
						  "text")
		    ->from(self::$_table_name)
		    ->order_by('created_at', 'DESC') 
		    ->where("type", $type);
		if (!is_null($limit)) $SQL->limit($limit);    
		return $SQL->execute()->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	// public static function ins($columns, $values) {

	// 	$SQL = DB::insert(self::$_table_name, $columns);

	// 	foreach ($values as $value) {
	// 		$SQL->values($value);
	// 	}

	// 	return $SQL->execute();
	// }
}
