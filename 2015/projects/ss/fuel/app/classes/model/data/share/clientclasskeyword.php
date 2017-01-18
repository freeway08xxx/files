<?php

class Model_Data_Share_ClientClassKeyword extends \Model
{
	protected static $_table_name = 't_monitor_clientclass_keyword';

  public static function get_keyword_id($client_class_id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_class_id', $client_class_id);
    return $query->execute()->as_array();
  }

  public static function insert_keyword_id_list($client_class_id, $list){
    $query = DB::insert(self::$_table_name, array('client_class_id', 'keyword_id'));
    foreach($list as $keyword_id){
      $query->values(array($client_class_id, $keyword_id));
    }
    $query->set_duplicate(array('updated_at = VALUES(updated_at)','updated_user = VALUES(updated_user)'));
    return $query->execute();
  }

  public static function del_clientclass_keyword($id, $client_class_id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', $id);
    $query->where('client_class_id', $client_class_id);
    return $query->execute();
  }

  public static function get_clientclass_keyword_count_list($client_id){
    $join_table = 't_monitor_client_class';
    $query = DB::select(
        array($join_table.'.id', 'id'), 
        array('COUNT("'.self::$_table_name.'.keyword_id")','count'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.client_class_id', '=', $join_table.'.id')
      ->where($join_table.'.client_id', $client_id)
      ->group_by(self::$_table_name.'.client_class_id');
    $res = $query->execute();
    $return = array();
    foreach($res as $item) {
      $return[$item['id']] = $item['count'];
    }
    return $return;
  }
}
