<?php

class Model_Data_Share_IndustryUser extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'industry_id',
		'mail_address',
		'admin_flg',
		'show_flg',
		'created_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'share.industry_user';

  public static function check_industry_user($industry_id, $mail_address){
    $query = self::query()->where('industry_id', '=', $industry_id)
              ->where('mail_address', '=', $mail_address)->get_one();
    if ($query) {
      return true;
    } else {
      return false;
    }
  }

  public static function get_user_list($industry_id){
      $query = self::query()->where('industry_id', '=', $industry_id);
      return $query->get();
  }

  public static function get_industry_list($mail_address){
      $list = self::query()->where('mail_address', '=', $mail_address)->get();
      $res = array();
      foreach($list as $item) {
        $res[] = $item['industry_id'];
      }
      return $res;
  }

  public static function get_select_list_with_admin($admin_flg, $mail_address){
      $join_table = "share.industry";
      $query = DB::select($join_table.'.*')
        ->from($join_table)
        ->where($join_table.'.delete_flg', '=', '0');
      if (!$admin_flg) {
        $query->join(self::$_table_name, 'INNER')
          ->on($join_table.'.id', '=', self::$_table_name.'.industry_id')
          ->where(self::$_table_name.'.mail_address', '=', $mail_address);
      }
      
      return $query->execute()->as_array();
  }

  public static function insert_industry_user($industry_id, $mail_address){
    $res = self::query()
            ->where('industry_id', '=', $industry_id)
            ->where('mail_address', '=', $mail_address)
            ->get_one();
    if($res) {
      return false;
    }
    $query = new self;
    $query->industry_id = $industry_id;
    $query->mail_address = $mail_address;
    $query->admin_flg = 1;
    $query->show_flg = 1;
    $query->save();
    return $query;
  }

  public static function update_industry_user($industry_id, $mail_address, $param){
    return true;
  }

  public static function delete_industry_user($id, $industry_id){
    $query = self::find($id);
    if ($query && $query->industry_id == $industry_id) {
      $query->delete();
      return true;
    }
  }
}
