<?php

class Model_Data_Share_Industry extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'sort',
		'name',
		'created_at',
		'delete_flg' => array('default' => '0'),
		'delete_datetime'
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
	protected static $_table_name = 'share.industry';

  public static function get_industry_list(){
    $query = self::query()->where('delete_flg', '=', '0');  
    $query->order_by('sort');  
    return $query->get();
  }

  public static function set_industry($sort, $name, $id=null){
    if ($id) {
      $query = self::find($id);
    } else {
      $query = new self;
    }
    $query->sort = $sort;
    $query->name = $name;
    $query->save();
    return $query;
  }

  public static function del_industry($id){
    $query = self::find($id);
    $query->delete_flg = '1';
    $query->delete_datetime = date("Y-m-d H:i:s",time());
    $query->save();
    return $query;
  }

}
