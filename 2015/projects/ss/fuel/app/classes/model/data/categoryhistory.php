<?php

class Model_Data_CategoryHistory extends \Model
{
	protected static $_table_name = 't_category_edit_history';

  public static function get($client_id, $limit=null){
    $query = DB::select('t_category_edit_history.*', 'mora.user.user_name')->from(self::$_table_name);
    $query->join('t_category_genre', 'LEFT')->on('t_category_genre.id', '=', 't_category_edit_history.category_genre_id');
    $query->join('mora.user', 'INNER')->on('mora.user.id', '=', 't_category_edit_history.created_user');
    $query->where('t_category_edit_history.client_id', $client_id);
    $query->order_by('t_category_edit_history.created_at', 'desc');
    if($limit){
    	$query->limit($limit);
    }
    return $query->execute('default')->as_array();
  }

  public static function get_export_info($history_id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('id', $history_id);
    return $query->execute()->current();
  }

  public static function ins($values){
    $query = DB::insert(self::$_table_name, array_keys($values));
    $query->values(array_values($values));
    return $query->execute();
  }

  public static function upd($id, $values){
    $query = DB::update(self::$_table_name);
    $query->set($values);
    $query->where('id', '=', $id);
    return $query->execute();
  }

  public static function upd_for_genre_id($genre_id, $values){
    $query = DB::update(self::$_table_name);
    $query->set($values);
    $query->where('category_genre_id', '=', $genre_id);
    return $query->execute();
  }

  public static function del($id){
    $query = DB::delete(self::$_table_name);
    $query->where('id', '=', $id);
    return $query->execute();
  }

}
