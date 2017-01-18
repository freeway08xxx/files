<?php

class Model_Data_Share_Result extends \Model
{
	private $_table_name = 't_monitor_result_';

  public function __construct($media_name) {
    $this->_table_name .= $media_name;
  }

  // 日にち別実行分キーワードチェック
  public function chk_keyword($device_id, $keyword_id, $action_day){
    $query = DB::select()->from($this->_table_name);
    $query->where('device_id', $device_id)
          ->where('keyword_id', $keyword_id)
          ->where('action_day', $action_day);
    return $query->execute()->as_array();
  }

  // リプレイス用クロール結果削除
  public function del_keyword($device_id, $keyword_id, $action_day){
    $query = DB::delete($this->_table_name);
    $query->where('device_id', $device_id)
          ->where('keyword_id', $keyword_id)
          ->where('action_day', $action_day);
    return $query->execute();
  }

  public function ins_keyword($values){
    $param = array('device_id', 'carrier_id', 'action_day', 'keyword_id'
              , 'url', 'rank', 'lp_url', 'title', 'description', 'rollup');
    $query = DB::insert($this->_table_name, $param);
    foreach($values as $value) {
      $query->values($value);
    }
    return $query->execute();
  }

  // 集計検索クエリ
  public function get_result_list($keywords, $search_array){
    $query = DB::select('*',DB::expr('SUM(rank) as sum_rank'),DB::expr('COUNT(*) as count'))->from($this->_table_name);
    if ($search_array['device_id']) {
      $query->where($this->_table_name.'.device_id', '=', $search_array['device_id']);
    }
    if ($search_array['carrier_id']) {
      $query->where($this->_table_name.'.carrier_id', '=', $search_array['carrier_id']);
    }
    if ($search_array['rank']) {
      $query->where($this->_table_name.'.rank', '=', $search_array['rank']);
    }
    if ($search_array['keyword_id']) {
      $query->where($this->_table_name.'.keyword_id', '=', $search_array['keyword_id']);
    } else {
      if (!$keywords) {
        return array();
      }
      $query->where($this->_table_name.'.keyword_id', 'IN', $keywords);
    }
    if ($search_array['from_day'] and $search_array['to_day']) {
      if ($search_array['from_day'] == $search_array['to_day']) {
        $query->where($this->_table_name.'.action_day', '=', $search_array['to_day']);
      } else {
        $query->where($this->_table_name.'.action_day', 'between', array($search_array['from_day'], $search_array['to_day']));
      }
    }
    $query->group_by('carrier_id', 'title', 'description', 'keyword_id');
    return $query->execute()->as_array();
  }

}
