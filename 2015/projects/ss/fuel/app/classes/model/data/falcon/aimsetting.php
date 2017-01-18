<?php

################################################################################
#
# Title : XXXXXXXX
#
#  2014/03/XX  First Version
#
################################################################################

class Model_Data_Falcon_AimSetting extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_falcon_aim_setting";

	public static function get_info($client_id, $template_id){
			$query = DB::select()->from(self::$_table_name);
			$query->where('client_id', $client_id);
			$query->where('template_id', $template_id);
		return $query->execute('default')->as_array();
	}

	public static function del_ins($id, $values) {
		self::del($id);
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name
					, array_merge(array('template_id'),array_keys($value)));
			}
			$query->values(array_merge(array($id), $value));
		}
		return $query->execute();
	}

	public static function del($id) {
		$query = DB::delete(self::$_table_name);
		$query->where('template_id', $id);
		return $query->execute();
	}

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($template_id, $client_id) {

		$SQL = DB::select(array("target", "target"),
							array("element", "element"),
							array("value", "value"));
		$SQL->from(self::$_table_name);
		$SQL->where("template_id", $template_id)
			->where("client_id", $client_id);

		return $SQL->execute("readonly");
	}
}
