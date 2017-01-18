<?php
class Model_Data_Share_ClickRate extends \Model
{
	protected static $_table_name = 't_monitor_click_rate';

  public static function get_click_rate($industry_class_id, $client_class_id, $device_id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_class_id', '=', $client_class_id)
          ->where('industry_class_id', '=', $industry_class_id)
          ->where('device_id', '=', $device_id);
      $res = $query->execute()->as_array();
    if(!$res) {
      $query = DB::select()->from(self::$_table_name);
      $query->where('client_class_id', '=', 0)
            ->where('industry_class_id', '=', 0)
            ->where('device_id', '=', $device_id);
      $res = $query->execute()->as_array();
    }
    return $res;
  }

  public static function get_client_list($mail_address){
    $query = DB::select()->from(self::$_table_name);
    $query->where('mail_address', '=', $mail_address);
    $list = $query->execute()->as_array();
    $res = array();
    foreach($list as $item) {
      $res[] = $item['client_id'];
    }
    return $res;
  }
}
