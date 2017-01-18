<?php

class Model_Data_Falcon_AccountSetting extends \Model
{
	protected static $_table_name = 't_falcon_template_account_setting';

	public static function get(){
			$query = DB::select()->from(self::$_table_name);
			$query->where('client_id', $client_id);
			$query->where('template_id', $template_id);
		return $query->execute('default')->current();
	}

	public static function get_info($client_id, $template_id){
			$query = DB::select(DB::expr("CONCAT(media_id, '//', account_id) AS account_id"))->from(self::$_table_name);
			$query->where('client_id', $client_id);
			$query->where('template_id', $template_id);
		return $query->execute('default')->as_array('account_id');
	}

	public static function get_account_for_media($client_id, $template_id){
			$query = DB::select(DB::expr("DISTINCT media_id"))->from(self::$_table_name);
			$query->where('client_id', $client_id);
			$query->where('template_id', $template_id);
			$query->order_by('media_id', 'asc');
		return $query->execute('default')->as_array('media_id');
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
}
