<?php

class Model_Data_CategoryElement extends \Model
{
	protected static $_table_name = 't_category_element';


	/**
	 * 種別取得
	 * @param category_element カテゴリ種別（大・中・小）
	 */
	public static function set_element($element){

		if(!$element){
			return false;
		}

		if($element == "category_id"){
			$set_element["element"] = "category_id";
			$set_element["table"] = "t_category";
		}elseif($element == "category_middle_id"){
			$set_element["element"] = "category_middle_id";
			$set_element["table"] = "t_category_middle";
		}elseif($element == "category_big_id"){
			$set_element["element"] = "category_big_id";
			$set_element["table"] = "t_category_big";
		}

		return $set_element;
	}

	//カテゴリ取得
	public static function get_category_list($client_id, $category_genre_id, $account_id, $group_by_flg=null){
		$query = DB::select(self::$_table_name.'.*',
							't_category.category_name',
							't_category_middle.category_name as category_middle_name',
							't_category_big.category_name as category_big_name');
							if(isset($group_by_flg)){
								if($group_by_flg == 1){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id) AS unique_key"));
								}elseif($group_by_flg == 2){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, '')) AS unique_key"));
								}elseif($group_by_flg == 3){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, '')) AS unique_key"));
								}elseif($group_by_flg == 4){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, ''), IF(keyword_id is null, '', ':'), IFNULL(keyword_id, '')) AS unique_key"));
								}
							}
			$query->from(self::$_table_name)
					->join('t_category', 'INNER')->on('t_category.id', '=', self::$_table_name.'.category_id')
					->join('t_category_middle', 'INNER')->on('t_category_middle.id', '=', self::$_table_name.'.category_middle_id')
					->join('t_category_big', 'INNER')->on('t_category_big.id', '=', self::$_table_name.'.category_big_id')
					->where(self::$_table_name.'.client_id', $client_id)
					->where(self::$_table_name.'.category_genre_id', $category_genre_id)
					->where(self::$_table_name.'.account_id', $account_id);
		if(isset($group_by_flg)){
			if($group_by_flg == 1){
				$query->group_by(self::$_table_name.'.media_id')
					  ->group_by(self::$_table_name.'.account_id');
			}elseif($group_by_flg == 2){
				$query->group_by(self::$_table_name.'.media_id')
					  ->group_by(self::$_table_name.'.account_id')
					  ->group_by(self::$_table_name.'.campaign_id');
			}elseif($group_by_flg == 3){
				$query->group_by(self::$_table_name.'.media_id')
					  ->group_by(self::$_table_name.'.account_id')
					  ->group_by(self::$_table_name.'.campaign_id')
					  ->group_by(self::$_table_name.'.ad_group_id');
			}elseif($group_by_flg == 4){
				$query->group_by(self::$_table_name.'.media_id')
					  ->group_by(self::$_table_name.'.account_id')
					  ->group_by(self::$_table_name.'.campaign_id')
					  ->group_by(self::$_table_name.'.ad_group_id')
					  ->group_by(self::$_table_name.'.keyword_id');
			}
		}
		$query->group_by('category_big_name')
			  ->group_by('category_middle_name')
			  ->group_by('category_name');

		return $query->execute()->as_array('unique_key');
	}

	//element_type_idごとのカテゴリ数取得
	public static function get_elementtypeid_list($client_id, $category_genre_id, $account_id, $group_by_flg=null){
		$query = DB::select('element_type_id','count(distinct category_big_id) as count_big','count(distinct category_middle_id) as count_middle','count(distinct category_id) as count_min');
							if(isset($group_by_flg)){
								if($group_by_flg == 1){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id) AS unique_key"));
								}elseif($group_by_flg == 2){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, '')) AS unique_key"));
								}elseif($group_by_flg == 3){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, '')) AS unique_key"));
								}elseif($group_by_flg == 4){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, ''), IF(keyword_id is null, '', ':'), IFNULL(keyword_id, '')) AS unique_key"));
								}
							}
			$query->from(self::$_table_name)
					->where('client_id', $client_id)
					->where('category_genre_id', $category_genre_id)
					->where('account_id', $account_id);
		if(isset($group_by_flg)){
			if($group_by_flg == 1){
				$query->group_by('media_id')
					  ->group_by('account_id');
			}elseif($group_by_flg == 2){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id');
			}elseif($group_by_flg == 3){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id')
					  ->group_by('ad_group_id');
			}elseif($group_by_flg == 4){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id')
					  ->group_by('ad_group_id')
					  ->group_by('keyword_id');
			}
		}
		$query->group_by('element_type_id');

		return $query->execute()->as_array();
	}

	//コンポーネントごとのカテゴリ数取得
	public static function get_element_count_list($client_id, $category_genre_id, $account_id, $group_by_flg){
		$query = DB::select('count(distinct category_big_id) as total_big','count(distinct category_middle_id) as total_middle','count(distinct category_id) as total_min');
							if(isset($group_by_flg)){
								if($group_by_flg == 1){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id) AS unique_key"));
								}elseif($group_by_flg == 2){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, '')) AS unique_key"));
								}elseif($group_by_flg == 3){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, '')) AS unique_key"));
								}elseif($group_by_flg == 4){
									$query->select(DB::expr("CONCAT(media_id, ':', account_id, IF(campaign_id is null, '', ':'), IFNULL(campaign_id, ''), IF(ad_group_id is null, '', ':'), IFNULL(ad_group_id, ''), IF(keyword_id is null, '', ':'), IFNULL(keyword_id, '')) AS unique_key"));
								}
							}
			$query->from(self::$_table_name)
					->where('client_id', $client_id)
					->where('category_genre_id', $category_genre_id)
					->where('account_id', $account_id);
		if(isset($group_by_flg)){
			if($group_by_flg == 1){
				$query->group_by('media_id')
					  ->group_by('account_id');
			}elseif($group_by_flg == 2){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id');
			}elseif($group_by_flg == 3){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id')
					  ->group_by('ad_group_id');
			}elseif($group_by_flg == 4){
				$query->group_by('media_id')
					  ->group_by('account_id')
					  ->group_by('campaign_id')
					  ->group_by('ad_group_id')
					  ->group_by('keyword_id');
			}
		}

		return $query->execute()->as_array('unique_key');
	}

	//カテゴリ取得
	public static function get_category_list_for_id($client_id, $category_genre_id){
		$query = DB::select(self::$_table_name.'.*',
							't_category.category_name',
							't_category_middle.category_name as category_middle_name',
							't_category_big.category_name as category_big_name')
				->from(self::$_table_name)
				->join('t_category', 'INNER')->on('t_category.id', '=', self::$_table_name.'.category_id')
				->join('t_category_middle', 'INNER')->on('t_category_middle.id', '=', self::$_table_name.'.category_middle_id')
				->join('t_category_big', 'INNER')->on('t_category_big.id', '=', self::$_table_name.'.category_big_id')
				->where(self::$_table_name.'.category_genre_id', $category_genre_id);
		return $query->execute();
	}

	//カテゴリのアカウントを取得
	public static function get_category_account($category_type, $category_id) {

		$query = DB::select("account_id")
				->distinct()
				->from(self::$_table_name)
				->where($category_type, $category_id);

		return $query->execute()->as_array();
	}

	public static function ins($values){
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name, array_keys($value));
			}
			$query->values($value);
		}
		return $query->execute();
	}

	public static function del($values){
		foreach ($values as $key => $value) {
			$query = DB::delete(self::$_table_name);
			$query->where($value, $value);
			$query->execute();
		}
		return;
	}

	public static function del_for_genre_id($genre_id){
		$query = DB::delete(self::$_table_name);
		$query->where('category_genre_id', $genre_id);
		return $query->execute();
	}

	public static function del_for_category_id($genre_id, $category_elem, $category_id){
		$query = DB::delete(self::$_table_name);
		$query->where('category_genre_id', $genre_id);
		$query->where($category_elem, $category_id);
		return $query->execute();
	}

}
