<?php

class Model_Data_Share_Client extends \Model
{
	protected static $_table_name = 't_monitor_client';

  public static function get_client_list(){
    $query = DB::select()->from(self::$_table_name);
    $query->order_by('sort');  
    return $query->execute()->as_array();
  }

  public static function set_client($sort, $name, $id=null){
    if ($id) {
      $query = DB::update(self::$_table_name);
      $query->set(array('sort' => $sort, 'name' => $name));
      $query->where('id', '=', $id);
      $query->execute();
    } else {
      $query = DB::insert(self::$_table_name, array('sort','name'));
      $query->values(array($sort,$name));
      $res = $query->execute();
      if (isset($res[0])) $id = $res[0];
    }
    if (!$id) return false;
    $query = DB::select()->from(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute()->current();
  }

  public static function del_client($id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute();
  }

}
