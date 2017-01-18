<?php

class Model_Data_Account extends \Model
{
	protected static $_table_name = 't_account';

  public static function get_adwords($id=null){
    $query = DB::select()->from(self::$_table_name);
    $query->where('media_id', '=', 2);
    if ($id) {
      $query->where('id', '=', $id);
    }
    return $query->execute();
  }

  public static function upd($id, $token){
    $query = DB::update(self::$_table_name);
    $query->set(array('login_token' => $token));
    $query->where('id', '=', $id);
    return $query->execute();
  }

}
