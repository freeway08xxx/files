<?php

class Model_Data_Falcon_LineSetting extends \Model
{
	protected static $_table_name = 't_falcon_line_setting';

	public static function get(){
	}

	public static function get_info($client_id, $template_id){
		$query = DB::select()->from(self::$_table_name);
		$query->where('client_id', $client_id);
		$query->where('template_id', $template_id);
		$query->order_by('line_no', 'asc');
		$query->order_by('device_id', 'asc');
		return $query->execute('default')->as_array();
	}

	public static function get_copy($template_id, $device_id){
		$query = DB::select('"element" as "add_id", "label" as "cell_label", "formula", "formula_cell_type" as "cell_type", "line_no"')->from(self::$_table_name);
		$query->where('template_id', $template_id);
		$query->where('device_id', $device_id);
		return $query->execute('default')->as_array();
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
