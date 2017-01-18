<?php

class Model_Data_Share_IndustryClass extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'industry_id',
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
	protected static $_table_name = 'share.industry_class';

  public static function get_industry_class_list_all($industry_list=null){
    $query = self::query()->where('delete_flg', '=', '0');  
    if ($industry_list) {
      $query->where('industry_id', 'in', $industry_list);  
    }
    $query->order_by('sort');  
    return $query->get();
  }

  public static function get_industry_class_list($industry_id){
    $query = self::query()->where('delete_flg', '=', '0');  
    $query->where('industry_id', '=', $industry_id);  
    $query->order_by('sort');  
    return $query->get();
  }

  public static function get_industry_class_list_with_p($industry_class_id){
    $join_table = 'share.industry';
    $query = DB::select(self::$_table_name.'.*', array($join_table.'.name','parent_name'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.industry_id', '=', $join_table.'.id')
      ->where(self::$_table_name.'.id', '=', $industry_class_id);
    $result = $query->execute()->current();
    return $result;
  }

  public static function set_industry_class($sort, $name, $id=null, $industry_id=null){
    if ($id) {
      $query = self::find($id);
    } else {
      $query = new self;
      $query->industry_id = $industry_id;
    }
    $query->sort = $sort;
    $query->name = $name;
    $query->save();
    return $query;
  }

  public static function del_industry_class($id){
    $query = self::find($id);
    $query->delete_flg = '1';
    $query->delete_datetime = date("Y-m-d H:i:s",time());
    $query->save();
    return $query;
  }

}
