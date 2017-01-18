<?php

class Model_Data_Share_Keyword extends \Model
{
	protected static $_table_name = 'm_keyword';

  // キーワードリスト登録
  public static function insert_keyword_list($list){
    $insert_flg = false;
    $result = array();
    foreach($list as $keyword){
      $query = DB::select()->from(self::$_table_name);
      $query->where('keyword', $keyword);
      $data = $query->execute()->current();
      if ($data) {
        $result[] = $data['id'];
        continue;
      }
      $query = DB::insert(self::$_table_name, array('keyword'));
      $query->values(array($keyword));
      $data = $query->execute();
      $result[] = $data[0];
    }
    return $result;
  }

  public static function del_keyword($id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', $id);
    return $query->execute();
  }

  // 業種詳細別キーワードリスト取得
  public static function get_keyword_list($client_class_id, $industry_class_id){
    if ($client_class_id > 0) {
      return self::get_keyword_list_by_client($client_class_id);
    } elseif($industry_class_id > 0) {
      return self::get_keyword_list_by_industry($industry_class_id);
    }
  }

  // 業種詳細別キーワードリスト取得
  public static function get_keyword_list_by_industry($id){
    $join_table = 't_monitor_industryclass_keyword';
    $query = DB::select(self::$_table_name.'.*', array($join_table.'.id', 'class_id'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.id', '=', $join_table.'.keyword_id')
      ->where($join_table.'.industry_class_id', '=', $id);
    return $query->execute()->as_array();
  }

  // クライアント詳細別キーワードリスト取得
  public static function get_keyword_list_by_client($id){
    $join_table = 't_monitor_clientclass_keyword';
    $query = DB::select(self::$_table_name.'.*', array($join_table.'.id', 'class_id'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.id', '=', $join_table.'.keyword_id')
      ->where($join_table.'.client_class_id', '=', $id);
    return $query->execute()->as_array();
  }

}
