<?php

class Model_Data_Querytargeting extends \Model {

	// テーブル名セット
	protected static $_table_name = 't_query_keyword';

	// SELECT：指定カテゴリーキーワードリスト取得（WEB用）
	public static function get_all_keyword($category){

		$query = DB::select()
			-> from(self::$_table_name)
			-> where('category_name', '=', $category);
		$sql_result = $query->execute()->as_array();
		return $sql_result;
	}

	// SELECT：カテゴリーリスト全取得（WEB用）
	public static function get_all_category(){

		$query = DB::select('category_name')
			-> from(self::$_table_name)
			-> group_by('category_name')
			-> order_by('id', 'asc');
		$sql_result = $query->execute()->as_array();
		return $sql_result;
	}

	// SELECT：キーワードリスト取得（Batch用）
	public static function get_keyword_list($category){

		$query = DB::select()
			-> from(self::$_table_name)
			-> where('category_name', '=', $category)
			-> where('delete_flg', '=', '0');

		$sql_result = $query->execute()->as_array();
		return $sql_result;
	}

	// SELECT：カテゴリーが既にあるか
	public static function get_category_double($category){

		$query = DB::select('category_name')
			-> from(self::$_table_name)
			-> where('category_name', '=', $category);
		$sql_result = $query->execute()->current();

		return $sql_result;
	}

	// SELECT：キーワード登録重複探し
	public static function get_keyword_double($category, $keyword){

		$query = DB::select('keyword')
			-> from(self::$_table_name)
			-> where('category_name', '=', $category)
			-> where('keyword', '=', $keyword);
		$sql_result = $query->execute()->current();

		return $sql_result;
	}

	// BULK INSERT：キーワード登録
	public static function bulk_ins($columns, $keywords, $category) {

		$query = DB::insert(self::$_table_name, $columns);
		foreach ($keywords as $keyword) {
			$query->values(array($keyword,$category,date('Y/m/d H:i:s'),date('Y/m/d H:i:s')));
		}
		return $query->execute();
	}

	// DELETE：キーワード削除
	public static function del_category($category) {

		$query = DB::delete(self::$_table_name);
		$query->where('category_name', '=', $category);
		
		return $query->execute();
	}

	// UPDATE：キーワードクローリング実行時間記録更新
	public static function upd_crawl_execute_time() {

		$query = DB::update(self::$_table_name);
		$query->set(array('last_select_datetime' => \DB::expr('NOW()')));
		$query->where('delete_flg', '=', '0');
		return $query->execute();
	}
}
