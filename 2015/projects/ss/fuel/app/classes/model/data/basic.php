<?php

class Model_Data_Basic extends \Model
{
	protected static $_table_name = 't_basic';

	public static function get(){
		$query = DB::select()->from(self::$_table_name);
		return $query->execute()->as_array();
	}

	public static function find_by_id($id) {
		$query = DB::select()->from(self::$_table_name)->where('id',$id);
		return $query->execute()->current();
	}

	public static function ins($text,$file_name){
		$query = DB::insert(self::$_table_name, array('text','file_name'));
		$query->values(array($text,$file_name));
		return $query->execute();
	}

	public static function upd($id, $text,$file_name){
		$query = DB::update(self::$_table_name);
		$query->set(array('text' => $text, 'file_name' => $file_name));
		$query->where('id', '=', $id);
		return $query->execute();
	}

	public static function del($id){
		$query = DB::delete(self::$_table_name);
		$query->where('id', '=', $id);
		return $query->execute();
	}

}
