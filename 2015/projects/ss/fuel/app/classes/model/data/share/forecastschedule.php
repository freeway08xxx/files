<?php

class Model_Data_Share_ForecastSchedule extends \Orm\Model
{
	protected static $_table_name = 't_monitor_forecast_schedule';

  public static function insert_new_keyword_id($list){
    $param = array('keyword_id', 'media_id', 'schedule_type', 'retry_count', 'action_day');
    $query = DB::insert(self::$_table_name, $param);
    foreach($list as $keyword_id){
      $value = array($keyword_id, MEDIA_ID_YAHOO, 'NEW', 0, substr($keyword_id, -1, 1));
      $query->values($value);
      $value = array($keyword_id, MEDIA_ID_GOOGLE, 'NEW', 0, substr($keyword_id, -1, 1));
      $query->values($value);
    }
    $query->set_duplicate(array('updated_at = VALUES(updated_at)','updated_user = VALUES(updated_user)'));
    return $query->execute();
  }

  public static function update_stay($media_id, $list){
    $query = DB::update(self::$_table_name);
    $query->set(array('schedule_type' => 'STAY'));
    $query->where('media_id', $media_id);
    $query->where('keyword_id', 'in', $list);
    return $query->execute();
  }

  public static function update_retry($media_id, $list){
    $query = DB::update(self::$_table_name);
    $query->set(array('schedule_type' => 'RETRY', 'retry_count' => DB::expr('retry_count + 1')));
    $query->where('media_id', $media_id);
    $query->where('keyword_id', 'in', $list);
    return $query->execute();
  }

  public static function get_keyword_list_by_mode($media_id, $mode, $today_d=""){
    $join_table = 'm_keyword';
    $query = DB::select()->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.keyword_id', '=', $join_table.'.id')
      ->where(self::$_table_name.'.media_id', $media_id)
      ->where(self::$_table_name.'.schedule_type', $mode);
    if($mode == 'RETRY') {
      $query->where(self::$_table_name.'.retry_count', '<', 3);
    } elseif($mode == 'STAY') {
      $query->where(self::$_table_name.'.action_day', $today_d);
    }
    return $query->execute()->as_array();
  }

}
