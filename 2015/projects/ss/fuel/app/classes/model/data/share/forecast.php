<?php

class Model_Data_Share_Forecast extends \Model
{
	private $_table_name = 't_monitor_forecast_';

  public function __construct($media_name) {
    $this->_table_name .= $media_name;
  }

  public function set_forecast($values){
    $param = array('device_id', 'action_month', 'keyword_id', 'imp', 'click', 'cpc');
    $query = DB::insert($this->_table_name, $param);
    foreach($values as $value){
      $query->values($value);
    }
    $query->set_duplicate(array('imp = VALUES(imp)','click = VALUES(click)','cpc = VALUES(cpc)'
                          ,'updated_at = VALUES(updated_at)','updated_user = VALUES(updated_user)'));
    return $query->execute();
  }

  public function get_forecast_by_industry($industry_class_id, $device_id, $target_month){
    $join_table = 't_monitor_industryclass_keyword';

    $query = DB::select($this->_table_name.'.*')
      ->from($this->_table_name)
      ->join($join_table, 'INNER')
      ->on($this->_table_name.'.keyword_id', '=', $join_table.'.keyword_id')
      ->where($join_table.'.industry_class_id', '=', $industry_class_id)
      ->where($this->_table_name.'.device_id', '=', $device_id)
      ->where($this->_table_name.'.action_month', '=', $target_month);
    $result = $query->execute()->as_array();
    return $result;
  }

  public function get_forecast_by_client($client_class_id, $device_id, $target_month){
    $join_table = 't_monitor_clientclass_keyword';

    $query = DB::select($this->_table_name.'.*')
      ->from($this->_table_name)
      ->join($join_table, 'INNER')
      ->on($this->_table_name.'.keyword_id', '=', $join_table.'.keyword_id')
      ->where($join_table.'.client_class_id', '=', $client_class_id)
      ->where($this->_table_name.'.device_id', '=', $device_id)
      ->where($this->_table_name.'.action_month', '=', $target_month);
    $result = $query->execute()->as_array();
    return $result;
  }
}
