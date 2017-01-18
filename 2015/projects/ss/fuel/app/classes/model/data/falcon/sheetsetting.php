<?php

class Model_Data_Falcon_SheetSetting extends \Model
{
	protected static $_table_name = 't_falcon_sheet_setting';

	public static function get(){
		$query = DB::select()->from(self::$_table_name);
		$query->where('client_id', $client_id);
		$query->where('template_id', $template_id);
		return $query->execute('default')->current();
	}

	public static function get_info($client_id, $template_id){
		$query = DB::select()->from(self::$_table_name);
		$query->where('client_id', $client_id);
		$query->where('template_id', $template_id);
		return $query->execute('default')->as_array('sheet_name');
	}

	public static function del_ins($id, $values) {
		self::del($id);
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name, array_merge(array('template_id'),array_keys($value)));
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
}
