<?php

class Model_Data_Adcustomizer_Google_History extends \Model
{
	protected static $_table_name = "t_adcustomizer_google_history";

	public static function get_by_id($id) {
		$query = DB::select();
		$query->from(self::$_table_name);
		$query->where('id',$id);
		return $query->execute("readonly")->current();
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
