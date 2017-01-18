<?php

class Model_Data_CategoryGenre extends \Model
{
	protected static $_table_name = 't_category_genre';

  public static function get($id){
    $query = DB::select()->from(self::$_table_name);
    $query->where('id', $id);
    return $query->execute()->current();
  }

  public static function get_for_client_id($client_id){
    $query = DB::select('t_category_genre.*', 'user.user_name',
    					'CASE WHEN t_category_genre."updated_user" IS NULL THEN t_category_genre."created_user" ELSE t_category_genre."updated_user" END as "user_id"',
    					'CASE WHEN t_category_genre."updated_at" IS NULL THEN t_category_genre."created_at" ELSE t_category_genre."updated_at" END as "datetime"')->from(self::$_table_name);
    $query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when t_category_genre."updated_user" is null then t_category_genre."created_user" else t_category_genre."updated_user" end');
    $query->where('t_category_genre.client_id', $client_id);
    $query->order_by('t_category_genre.created_at', 'desc');
    return $query->execute()->as_array();
  }

  public static function get_for_category_genre_name($client_id, $category_genre_name){
    $query = DB::select()->from(self::$_table_name);
    $query->where('client_id', $client_id);
    $query->where('category_genre_name', $category_genre_name);
    return $query->execute()->current();
  }

  public static function ins($id, $values){
    DB::start_transaction('administrator');
    if ($id) {
      $query = DB::update(self::$_table_name);
      $query->set($values);
      $query->where('id', $id);
      $query->execute();
    } else {
      $query = DB::insert(self::$_table_name, array_keys($values));
      $query->values(array_values($values));
      $id = $query->execute();
      if (!isset($id[0])) {
        DB::rollback_transaction('administrator');
        return false;
      }
      $id = $id[0];
    }
    DB::commit_transaction('administrator');

    return true;
  }

  public static function del($id){
    DB::start_transaction('administrator');
    $query = DB::delete(self::$_table_name);
    $query->where('id', $id);
    $query->execute();
    \Model_Data_Category::del_for_genre_id("category_big_id", $id);
    \Model_Data_Category::del_for_genre_id("category_middle_id", $id);
    \Model_Data_Category::del_for_genre_id("category_id", $id);
    \Model_Data_CategoryElement::del_for_genre_id($id);
    DB::commit_transaction('administrator');

    return true;
  }
}
