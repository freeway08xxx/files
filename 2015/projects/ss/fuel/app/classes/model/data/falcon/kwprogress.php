<?php

class Model_Data_Falcon_KwProgress extends \Model
{
	protected static $_table_name = 't_falcon_keyword_progress_setting';

	public static function get($client_id) {
		$query = DB::select("keyword", "match_type")
				->from(self::$_table_name);
		$query->where("client_id", $client_id);

		return $query->execute()->as_array();
	}

	public static function ins($values){
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name, array_keys($value));
			}
			$query->values($value);
		}
		return $query->execute();
	}

	public static function del($client_id){
		DB::start_transaction('administrator');
		$query = DB::delete(self::$_table_name);
		$query->where('client_id', $client_id);
		$query->execute();
		DB::commit_transaction('administrator');
		return true;
	}

}
