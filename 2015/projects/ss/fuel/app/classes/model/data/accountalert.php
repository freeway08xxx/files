<?php

class Model_Data_AccountAlert extends \Model
{
	protected static $_table_name = 't_account_alert';

	protected static $_column = array('client_id', 'account_id', 'media_id', 'status_code', 'account_budget', 'limit_budget', 'synchro_time');

  public static function get($client_id=null, $account_id=null, $media_id=null, $stop_flg=null){
    $query = DB::select()->from(self::$_table_name);
    $query->where('status_code', ACCOUNT_STATUS_ACTIVE); 
    if ($client_id) {
      $query->where('client_id', $client_id);
    }
    if ($account_id) {
      $query->where('account_id', $account_id);
    }
    if ($media_id) {
      $query->where('media_id', $media_id);
    }
    if ($stop_flg) {
      $query->where('stop_flg', $stop_flg)
        ->join('mora.account', 'INNER')->on('mora.account.id', '=', self::$_table_name.'.account_id');
    }
    return $query->execute()->as_array();
  }

  public static function get_stoped($stop_date=null){
    $query = DB::select()->from(self::$_table_name);
    $query->where('status_code', ACCOUNT_STATUS_ACTIVE) 
        ->where('stop_flg', '1')
        ->join('mora.account', 'INNER')->on('mora.account.id', '=', self::$_table_name.'.account_id');
    if ($stop_date) {
      $query->where('stop_date', '>=', $stop_date);
    }
    return $query->execute()->as_array();
  }

  public static function get_alert_account($client_id, $account_id) {
    $query = DB::select()->from(self::$_table_name)
        ->join('mora.account', 'INNER')->on('mora.account.id', '=', self::$_table_name.'.account_id')
        ->join('mora.account_info', 'INNER')->on('mora.account_info.account_id', '=', self::$_table_name.'.account_id');
    $query->where(self::$_table_name.'.status_code', ACCOUNT_STATUS_ACTIVE); 
    $query->where(self::$_table_name.'.client_id', $client_id);
    $query->where(self::$_table_name.'.account_id', $account_id);
    return $query->execute()->current();
  }

  public static function get_alert_client($alert_code, $media_id=null, $client_id=null){
    $query = DB::select()->from(self::$_table_name)
        ->join('mora.account', 'INNER')->on('mora.account.id', '=', self::$_table_name.'.account_id')
        ->where('alert_code', $alert_code) 
        ->where('status_code', ACCOUNT_STATUS_ACTIVE) 
        ->order_by(self::$_table_name.'.client_id'); 
    if ($client_id) {
      $query->where(self::$_table_name.'.client_id', $client_id);
    }
    if ($media_id) {
      $query->where(self::$_table_name.'.media_id', $media_id);
    }
    return $query->execute()->as_array();
  }

  public static function get_stop_account($client_id=null){
    $query = DB::select()->from(self::$_table_name)
        ->where('status_code', ACCOUNT_STATUS_ACTIVE) 
        ->where('stop_flg', '1'); 
    if ($client_id) {
      $query->where(self::$_table_name.'.client_id', $client_id);
    }
    return $query->execute()->as_array();
  }

  public static function ins($values){
    $query = DB::insert(self::$_table_name, self::$_column);
    foreach($values as $value) {
      $query->values($value);
    }
    $query->set_duplicate(array('status_code = VALUES(status_code)','account_budget = VALUES(account_budget)','synchro_time = VALUES(synchro_time)'));
    return $query->execute();
  }

  public static function upd($client_id, $account_id, $limit, $ary_param=array()){
    $query = DB::update(self::$_table_name);
    $set_param = array('limit_budget' => $limit);
    if ($ary_param) {
      if (isset($ary_param['remainder_status'])) {
        $set_param['alert_code'] = $ary_param['remainder_status'];
      }
      if (isset($ary_param['total_cost'])) {
        $set_param['consumption_cost'] = $ary_param['total_cost'];
      }
      if (isset($ary_param['prediction_cost'])) {
        $set_param['prediction_cost'] = $ary_param['prediction_cost'];
      }
      if (isset($ary_param['remainder_day'])) {
        $set_param['remainder_day'] = $ary_param['remainder_day'];
      }
    }
    $query->set($set_param);
    $query->where('client_id', $client_id);
    $query->where('account_id', $account_id);
    return $query->execute();
  }

  public static function upd_stat($client_id, $account_id, $status, $ary_param=array()){
    $query = DB::update(self::$_table_name);
    $set_param = array();
    $set_param['alert_code'] = $status;
    if ($ary_param) {
      if (isset($ary_param['total_cost'])) {
        $set_param['consumption_cost'] = $ary_param['total_cost'];
      }
      if (isset($ary_param['prediction_cost'])) {
        $set_param['prediction_cost'] = $ary_param['prediction_cost'];
      }
      if (isset($ary_param['remainder_day'])) {
        $set_param['remainder_day'] = $ary_param['remainder_day'];
      }
      if (isset($ary_param['stop_flg'])) {
        $set_param['stop_flg'] = $ary_param['stop_flg'];
        if ($ary_param['stop_flg'] == '1') {
          $set_param['stop_date'] = date('Y-m-d');
        }
      }
    }
    $query->set($set_param);
    $query->where('client_id', $client_id);
    $query->where('account_id', $account_id);
    return $query->execute();
  }

  public static function del($id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute();
  }

}
