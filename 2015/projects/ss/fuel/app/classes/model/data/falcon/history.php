<?php

class Model_Data_Falcon_History extends \Model
{
	protected static $_table_name = 't_falcon_export_report_history';

	public static function get($client_id, $limit=null){
		$query = DB::select('t_falcon_export_report_history.*', 'mora.user.user_name')->from(self::$_table_name);
		$query->join('t_falcon_template_setting', 'LEFT')->on('t_falcon_template_setting.id', '=', 't_falcon_export_report_history.template_id');
		$query->join('mora.user', 'INNER')->on('mora.user.id', '=', 't_falcon_export_report_history.created_user');
		$query->where('t_falcon_export_report_history.client_id', $client_id);
		$query->order_by('t_falcon_export_report_history.created_at', 'desc');
		if($limit){
			$query->limit($limit);
		}
		return $query->execute('default')->as_array();
	}

	public static function get_export_info($history_id){
		$query = DB::select()->from(self::$_table_name);
		$query->where('id', $history_id);
		return $query->execute()->current();
	}

	public static function ins($values){
		$query = DB::insert(self::$_table_name, array_keys($values));
		$query->values(array_values($values));
		return $query->execute();
	}

	public static function upd($id, $values){
		$query = DB::update(self::$_table_name);
		$query->set($values);
		$query->where('id', '=', $id);
		return $query->execute();
	}

	public static function del($id){
		$query = DB::delete(self::$_table_name);
		$query->where('id', '=', $id);
		return $query->execute();
	}

}
