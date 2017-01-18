<?php

class Model_Data_Share_IndustryClassKeyword extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'industry_class_id',
		'keyword_id',
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
	protected static $_table_name = 'share.industryclass_keyword';

  public static function get_keyword_id($industry_class_id){
      $query = self::query()->where('industry_class_id', '=', $industry_class_id);
      return $query->get();
  }

  public static function insert_keyword_id_list($industry_class_id, $list){
    $query = DB::insert(self::$_table_name);
    $query->columns(array('industry_class_id', 'keyword_id', 'created_at'));
    foreach($list as $keyword_id){
      $query = $query->values(array($industry_class_id, $keyword_id, date("Y-m-d H:i:s",time())));
    }
    $sql = str_replace('INSERT INTO','INSERT IGNORE INTO',$query->compile());
    DB::query($sql)->execute();
    return;
  }

  public static function del_industryclass_keyword($id, $industry_class_id){
    $query = self::find($id);
    if ($query && $query->industry_class_id == $industry_class_id) {
      $query->delete();
      return true;
    }
  }

  public static function get_industryclass_keyword_count_list($industry_id){
    $join_table = 'share.industry_class';
    $query = DB::select(
        array($join_table.'.id', 'id'), 
        array('COUNT("'.self::$_table_name.'.keyword_id")','count'))
      ->from(self::$_table_name)
      ->join($join_table, 'INNER')
      ->on(self::$_table_name.'.industry_class_id', '=', $join_table.'.id')
      ->where($join_table.'.industry_id', '=', $industry_id)
      ->group_by(self::$_table_name.'.industry_class_id');
    $res = $query->execute();
    $return = array();
    foreach($res as $item) {
      $return[$item['id']] = $item['count'];
    }
    return $return;
  }
}
