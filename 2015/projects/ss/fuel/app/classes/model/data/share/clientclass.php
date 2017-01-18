<?php

class Model_Data_Share_ClientClass extends \Model
{
	protected static $_table_name = 't_monitor_client_class';

  public static function get_client_class_list_all($client_list=null){
    $query = DB::select()->from(self::$_table_name);
    if ($client_list) {
      $query->where('client_id', 'in', $client_list);  
    }
    $query->order_by('sort');  
    return $query->execute()->as_array();
  }

  public static function get_client_class_list($client_id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_id', '=', $client_id);  
    $query->order_by('sort');  
    return $query->execute()->as_array();
  }

  public static function get_client_class_list_with_p($client_class_id){
    $join_table = 't_monitor_client';
    $query = DB::select(self::$_table_name.'.*', array($join_table.'.name','parent_name'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.client_id', '=', $join_table.'.id')
      ->where(self::$_table_name.'.id', '=', $client_class_id);
    return $query->execute()->current();
  }

  public static function set_client_class($sort, $name, $id=null, $client_id=null){
    if ($id) {
      $query = DB::update(self::$_table_name);
      $query->set(array('sort' => $sort, 'name' => $name));
      $query->where('id', $id);
      $query->execute();
    } else {
      $query = DB::insert(self::$_table_name, array('sort','name','client_id'));
      $query->values(array($sort,$name,$client_id));
      $res = $query->execute();
      if (isset($res[0])) $id = $res[0];
    }
    if (!$id) return false;
    $query = DB::select()->from(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute()->current();
  }

  public static function del_client_class($id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute();
  }

}
