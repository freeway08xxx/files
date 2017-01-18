<?php

class Model_Data_Share_CrawlLog extends \Model
{
	protected static $_table_name = 't_monitor_crawl_log';

  public static function get_crawl_log($search_param){
    $query = DB::select()->from(self::$_table_name)
      ->where('industry_class_id', '=', $search_param['industry_class_id'])
      ->where('client_class_id', '=', $search_param['client_class_id'])
      ->where('media_id', '=', $search_param['media_id'])
      ->where('device_id', '=', $search_param['device_id']);
    if ($search_param['from_day'] and $search_param['to_day']) {
      $query->where('action_day', '>=', $search_param['from_day'])
            ->where('action_day', '<=', $search_param['to_day']);
    }
    return $query->execute();
  }

  public static function ins($value){
    $param = array('industry_class_id', 'client_class_id', 'media_id'
                  , 'device_id', 'action_day', 'status'
                  , 'proxy_address', 'start_time');
    $query = DB::insert(self::$_table_name, $param);
    $query->values($value);
    return $query->execute();
  }

  public static function upd_status($status, $industry_class_id, $client_class_id, $media_id, $device_id, $day){
    $query = DB::update(self::$_table_name);
    $query->set(array('status' => $status, 'end_time' => date('Y-m-d H:i:s')));
    $query->where('industry_class_id', $industry_class_id)
      ->where('client_class_id', $client_class_id)
      ->where('media_id', $media_id)
      ->where('device_id', $device_id);
    return $query->execute();
  }
}
