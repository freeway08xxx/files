<?php

class Model_Data_Category extends \Model
{
	protected static $_table_name = 't_category';


	/**
	 * テーブル名取得
	 * @param category_element カテゴリ種別（大・中・小）
	 */
	public static function set_tablename($element){

		if(!$element){
			return false;
		}

		if($element == "category_id"){
			$table_name = self::$_table_name;
		}elseif($element == "category_middle_id"){
			$table_name = self::$_table_name . "_middle";
		}elseif($element == "category_big_id"){
			$table_name = self::$_table_name . "_big";
		}

		return $table_name;
	}

	//カテゴリ取得（ID指定）
	public static function get($element, $id){
		$table_name = self::set_tablename($element);
		$query = DB::select()->from($table_name);
		$query->where('id', $id);
		return $query->execute()->current();
	}

	//カテゴリリスト取得（レポート用）
	public static function get_category_list($element, $client_id, $category_genre_id){
		$table_name = self::set_tablename($element);
		$query = DB::select('category_name',array('id', 'category_id'))
						->from($table_name);
		$query->where('client_id', $client_id);
		$query->where('category_genre_id', $category_genre_id);
		$query->order_by('sort_order IS NULL');
		$query->order_by('sort_order');
		$query->order_by('category_name');
		return $query->execute()->as_array();
	}

	//カテゴリ数取得
	public static function get_category_count($element, $client_id){
		$table_name = self::set_tablename($element);
		$query = DB::select('category_genre_id, count(*) as count')
						->from($table_name)
						->where('client_id', $client_id)
						->group_by('category_genre_id');
		return $query->execute()->as_array('category_genre_id');
	}

	//カテゴリリスト取得（更新者付き）
	public static function get_for_category_genre_id($element, $client_id, $category_genre_id){
		$table_name = self::set_tablename($element);

		$query = DB::select($table_name.'.*', 'user.user_name',
							'CASE WHEN '.$table_name .'."updated_user" IS NULL THEN '.$table_name.'."created_user" ELSE '.$table_name.'."updated_user" END as "user_id"',
							'CASE WHEN '.$table_name.'."updated_at" IS NULL THEN '.$table_name.'."created_at" ELSE '.$table_name.'."updated_at" END as "datetime"')->from($table_name);
		$query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when '.$table_name.'."updated_user" is null then '.$table_name.'."created_user" else '.$table_name.'."updated_user" end');
		$query->where($table_name.'.client_id', $client_id);
		$query->where($table_name.'.category_genre_id', $category_genre_id);
		$query->order_by('sort_order IS NULL');
		$query->order_by('sort_order');
		$query->order_by('category_name');
		return $query->execute()->as_array('category_name');
	}

	//同一ジャンル内の並び順の最大値を取得
	public static function get_max_sort_order($element, $client_id, $category_genre_id){
		$table_name = self::set_tablename($element);

		$query = DB::select('MAX("sort_order") as max_count')->from($table_name);
		$query->where($table_name.'.client_id', $client_id);
		$query->where($table_name.'.category_genre_id', $category_genre_id);
		return $query->execute()->current();
	}

	//同一ジャンル内のカテゴリ数を取得
	public static function get_count($element, $client_id, $category_genre_id){
		$table_name = self::set_tablename($element);

		$query = DB::select('count(*) as count_elem')->from($table_name);
		$query->where($table_name.'.client_id', $client_id);
		$query->where($table_name.'.category_genre_id', $category_genre_id);
		return $query->execute()->current();
	}

	//カテゴリリスト（カテゴリ名指定）
	public static function get_for_category_name($element, $client_id, $category_genre_id, $category_name){
		$table_name = self::set_tablename($element);

		$query = DB::select()->from($table_name);
		$query->where('client_id', $client_id);
		$query->where('category_genre_id', $category_genre_id);
		$query->where('category_name', $category_name);
		return $query->execute()->current();
	}

	//カテゴリ追加
	public static function ins($element, $id, $values){
		$table_name = self::set_tablename($element);

		DB::start_transaction('administrator');
		if ($id) {
			$query = DB::update($table_name);
			$query->set($values);
			$query->where('id', $id);
			$query->execute();
		} else {
			$query = DB::insert($table_name, array_keys($values));
			$query->values(array_values($values));
			$id = $query->execute();
			if (!isset($id[0])) {
				DB::rollback_transaction('administrator');
				return false;
			}
			$id = $id[0];
		}
		DB::commit_transaction('administrator');

		return $id;
	}

	//カテゴリ設定
	public static function setting($element, $values){
		$table_name = self::set_tablename($element);

		DB::start_transaction('administrator');
		foreach ($values as $value){
			if ($value["id"]) {
				$query = DB::update($table_name);
				$query->set($value);
				$query->where('id', $value["id"]);
				$query->execute();
			} else {
				$query = DB::insert($table_name, array_keys($value));
				$query->values(array_values($value));
				$id = $query->execute();
				if (!isset($id[0])) {
					DB::rollback_transaction('administrator');
					return false;
				}
			}
		}
		DB::commit_transaction('administrator');

		return true;
	}

	//カテゴリ削除
	public static function del($genre_id, $element, $id){
		$table_name = self::set_tablename($element);

		DB::start_transaction('administrator');
		$query = DB::delete($table_name);
		$query->where('id', $id);
		$query->where('category_genre_id', $genre_id);
		$query->execute();

		\Model_Data_CategoryElement::del_for_category_id($genre_id, $element, $id);
		DB::commit_transaction('administrator');

		return true;
	}

	//カテゴリ削除
	public static function del_for_genre_id($element, $genre_id){
		$table_name = self::set_tablename($element);

		$query = DB::delete($table_name);
		$query->where('category_genre_id', $genre_id);
		return $query->execute();
	}
}
