<?php

class Model_Data_Share_ClientUser extends \Model
{
	protected static $_table_name = 't_monitor_client_user';

  public static function check_client_user($client_id, $mail_address){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_id', '=', $client_id)
          ->where('mail_address', '=', $mail_address);
    $res = $query->execute()->current();
    if ($res) {
      return true;
    } else {
      return false;
    }
  }

  public static function get_user_list($client_id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_id', '=', $client_id);
    return $query->execute()->as_array();
  }

  public static function get_client_list($mail_address){
    $query = DB::select()->from(self::$_table_name);
    $query->where('mail_address', '=', $mail_address);
    $list = $query->execute();
    $res = array();
    foreach($list as $item) {
      $res[] = $item['client_id'];
    }
    return $res;
  }

  public static function get_select_list_with_admin($admin_flg, $mail_address){
    $join_table = "t_monitor_client";
    $query = DB::select($join_table.'.*')
      ->from($join_table);
    if (!$admin_flg) {
      $query->join(self::$_table_name, 'INNER')
        ->on($join_table.'.id', '=', self::$_table_name.'.client_id')
        ->where(self::$_table_name.'.mail_address', '=', $mail_address);
    }
    
    return $query->execute()->as_array();
  }

  public static function insert_client_user($client_id, $mail_address){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_id', '=', $client_id)
          ->where('mail_address', '=', $mail_address);
    $res = $query->execute()->current();
    if($res) {
      return false;
    }
    $query = DB::insert(self::$_table_name, array('client_id', 'mail_address', 'admin_flg', 'show_flg'));
    $query->values(array($client_id,$mail_address,1,1));
    return $query->execute();
  }

  public static function update_client_user($client_id, $mail_address, $param){
    return true;
  }

  public static function delete_client_user($id, $client_id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', $id)
          ->where('client_id', $client_id);
    return $query->execute();
  }
}
