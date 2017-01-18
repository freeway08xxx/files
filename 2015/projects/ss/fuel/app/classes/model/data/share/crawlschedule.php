<?php

class Model_Data_Share_CrawlSchedule extends \Model
{
	protected static $_table_name = 't_monitor_crawl_schedule';

  // クロール用取得
  public static function get($device_id, $crawl_type, $crawl_day=""){
    $query = DB::select()->from(self::$_table_name);
    $query->where('device_id', $device_id)
          ->where('crawl_type', $crawl_type)
          ->where('crawl_day', $crawl_day);
    return $query->execute()->as_array();
  }

  // クロール用取得
  public static function get_by_id($id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('id', $id);
    return $query->execute()->current();
  }

  // クライアント詳細スケジュール一覧
  // ＊後述業種詳細スケジュールと一つの処理にもできるが、if文祭りになりそうなので敢えて分割
  public static function get_schedule_list_with_client($client_id){
    $join_table = 't_monitor_client_class';
    $as_name_1 = 'cs1';
    $as_name_2 = 'cs2';
    $query = DB::select($join_table.'.*'
                , array($as_name_1.'.crawl_type','device_1_crawl_type')
                , array($as_name_2.'.crawl_type','device_2_crawl_type')
                , array($as_name_1.'.crawl_day','device_1_crawl_day')
                , array($as_name_2.'.crawl_day','device_2_crawl_day'))
      ->from($join_table)
      ->join(array(self::$_table_name, $as_name_1), 'LEFT OUTER')
      ->on($as_name_1.'.client_class_id', '=', $join_table.'.id')
      ->and_on($as_name_1.'.industry_class_id', '=', DB::expr(0))
      ->and_on($as_name_1.'.device_id', '=', DB::expr(DEVICE_ID_PC))
      ->join(array(self::$_table_name, $as_name_2), 'LEFT OUTER')
      ->on($as_name_2.'.client_class_id', '=', $join_table.'.id')
      ->and_on($as_name_2.'.industry_class_id', '=', DB::expr(0))
      ->and_on($as_name_2.'.device_id', '=', DB::expr(DEVICE_ID_SMARTPHONE))
      ->where($join_table.'.client_id', '=', $client_id);
    $result = $query->execute()->as_array();
    return $result;
  }

  // 業種詳細スケジュール一覧
  public static function get_schedule_list_with_industry($industry_id){
    $join_table = 't_monitor_industry_class';
    $as_name_1 = 'cs1';
    $as_name_2 = 'cs2';
    $query = DB::select($join_table.'.*'
                , array($as_name_1.'.crawl_type','device_1_crawl_type')
                , array($as_name_2.'.crawl_type','device_2_crawl_type')
                , array($as_name_1.'.crawl_day','device_1_crawl_day')
                , array($as_name_2.'.crawl_day','device_2_crawl_day'))
      ->from($join_table)
      ->join(array(self::$_table_name, $as_name_1), 'LEFT OUTER')
      ->on($as_name_1.'.industry_class_id', '=', $join_table.'.id')
      ->and_on($as_name_1.'.client_class_id', '=', DB::expr(0))
      ->and_on($as_name_1.'.device_id', '=', DB::expr(DEVICE_ID_PC))
      ->join(array(self::$_table_name, $as_name_2), 'LEFT OUTER')
      ->on($as_name_2.'.industry_class_id', '=', $join_table.'.id')
      ->and_on($as_name_2.'.client_class_id', '=', DB::expr(0))
      ->and_on($as_name_2.'.device_id', '=', DB::expr(DEVICE_ID_SMARTPHONE))
      ->where($join_table.'.industry_id', '=', $industry_id);
    $result = $query->execute()->as_array();
    return $result;
  }

  // クロールスケジュール登録
  public static function set_crawl_schedule($industry_class_id, $client_class_id, $device_id, $crawl_type=1, $crawl_day=""){
    $query = DB::select()->from(self::$_table_name);
    $query->where('industry_class_id', $industry_class_id)
          ->where('client_class_id', $client_class_id)
          ->where('device_id', $device_id);
    $res = $query->execute()->current();
    if ($res) {
      $query = DB::update(self::$_table_name);
      $query->set(array('crawl_type' => $crawl_type, 'crawl_day' => $crawl_day));
      $query->where('industry_class_id', $industry_class_id)
            ->where('client_class_id', $client_class_id)
            ->where('device_id', $device_id);
    } else {
      $query = DB::insert(self::$_table_name
                , array('industry_class_id','client_class_id','device_id','crawl_type','crawl_day'));
      $query->values(array($industry_class_id, $client_class_id, $device_id, $crawl_type, $crawl_day));
    }
    return $query->execute();
  }

  // クロールスケジュール削除（物理）
  public static function delete_crawl_schedule($industry_class_id, $client_class_id, $device_id){
    $query = DB::delete(self::$_table_name);
    $query->where('industry_class_id', $industry_class_id)
          ->where('client_class_id', $client_class_id)
          ->where('device_id', $device_id);
    return $query->execute();
  }

}
