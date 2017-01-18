<?php

################################################################################
#
# Title : キャンペーン管理用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleCampaign extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_campaign";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".campaign_name", "campaign_name"),
						  array(self::$_table_name . ".status", "campaign_status"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 取得（PK指定）
	/*========================================================================*/
	public static function get_by_pk($media_id, $account_id, $campaign_id) {

		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".campaign_name", "campaign_name"),
						  array(self::$_table_name . ".status", "campaign_status"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id)
			->where(self::$_table_name . ".campaign_id", "=", $campaign_id);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($columns, $values) {

		$SQL = DB::insert(self::$_table_name, $columns);

		foreach ($values as $value) {
			$SQL->values($value);
		}

		$SQL->set_duplicate(array("campaign_name = VALUES(campaign_name)", "status = VALUES(status)"));

		return $SQL->execute("administrator");
	}

	/*========================================================================*/
	/* 指定アカウントのキャンペーン配下のコンポーネント情報取得
	/*========================================================================*/
	public static function get_undeleted_component($component, $media_id, $account_id, $mode = null, $offset = null, $limit = null) {

		## 処理対象コンポーネントがキャンペーンの場合
		if ($mode === "count") {
			$SQL = DB::select(array("count(*)", "count"));
		} else {
			$SQL = DB::select(array(self::$_table_name . ".campaign_id", "campaign_id"),
							  array(self::$_table_name . ".campaign_name", "campaign_name"),
							  array(self::$_table_name . ".status", "campaign_status"));
		}
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=" , $account_id);
		$SQL->and_where_open()
			->where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_PAUSED)
			->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_PAUSED)
			->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_PAUSED)
			->and_where_close();

		## 処理対象コンポーネントが広告グループの場合
		if ($component === "adgroup" || $component === "keyword" || $component === "ad") {
			$adg_table_name = "t_eagle_adgroup";

			if ($mode !== "count") {
				$SQL->select(array($adg_table_name . ".adgroup_id", "adgroup_id"),
							 array($adg_table_name . ".adgroup_name", "adgroup_name"),
							 array($adg_table_name . ".status", "adgroup_status"),
							 array($adg_table_name . ".match_type", "adgroup_match_type"),
							 array($adg_table_name . ".cpc_max", "adgroup_cpc_max"),
							 array($adg_table_name . ".bid_modifier", "adgroup_bid_modifier")
						 );
			}
			$SQL->join($adg_table_name, "INNER")
				->on(self::$_table_name . ".media_id", "=", $adg_table_name . ".media_id")
				->on(self::$_table_name . ".account_id", "=", $adg_table_name . ".account_id")
				->on(self::$_table_name . ".campaign_id", "=", $adg_table_name . ".campaign_id");
			$SQL->and_where_open()
				->where($adg_table_name . ".status", ADWORDS_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", ADWORDS_ADGROUP_PAUSED)
				->or_where($adg_table_name . ".status", YAHOO_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", YAHOO_ADGROUP_PAUSED)
				->or_where($adg_table_name . ".status", YDN_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", YDN_ADGROUP_PAUSED)
				->and_where_close();

			## 処理対象コンポーネントがキーワードの場合
			if ($component === "keyword") {
				$kw_table_name = "t_eagle_keyword";

				if ($mode !== "count") {
					$SQL->select(array($kw_table_name . ".keyword_id", "keyword_id"),
								 array($kw_table_name . ".keyword", "keyword"),
								 array($kw_table_name . ".status", "keyword_status"));
				}
				$SQL->join($kw_table_name, "INNER")
					->on($adg_table_name . ".media_id", "=", $kw_table_name . ".media_id")
					->on($adg_table_name . ".account_id", "=", $kw_table_name . ".account_id")
					->on($adg_table_name . ".campaign_id", "=", $kw_table_name . ".campaign_id")
					->on($adg_table_name . ".adgroup_id", "=", $kw_table_name . ".adgroup_id");
				$SQL->and_where_open()
					->where($kw_table_name . ".status", ADWORDS_CRITERION_ACTIVE)
					->or_where($kw_table_name . ".status", ADWORDS_CRITERION_PAUSED)
					->or_where($kw_table_name . ".status", YAHOO_CRITERION_ACTIVE)
					->or_where($kw_table_name . ".status", YAHOO_CRITERION_PAUSED)
					->and_where_close();

			## 処理対象コンポーネントが広告の場合
			} elseif ($component === "ad") {
				$ad_table_name = "t_eagle_ad";

				if ($mode !== "count") {
					$SQL->select(array($ad_table_name . ".ad_id", "ad_id"),
								 array($ad_table_name . ".ad_name", "ad_name"),
								 array($ad_table_name . ".title", "title"),
								 array($ad_table_name . ".description_1", "description_1"),
								 array($ad_table_name . ".description_2", "description_2"),
								 array($ad_table_name . ".status", "ad_status"));
				}
				$SQL->join($ad_table_name, "INNER")
					->on($adg_table_name . ".media_id", "=", $ad_table_name . ".media_id")
					->on($adg_table_name . ".account_id", "=", $ad_table_name . ".account_id")
					->on($adg_table_name . ".campaign_id", "=", $ad_table_name . ".campaign_id")
					->on($adg_table_name . ".adgroup_id", "=", $ad_table_name . ".adgroup_id");
				$SQL->and_where_open()
					->where($ad_table_name . ".status", ADWORDS_REPORT_AD_ACTIVE)
					->or_where($ad_table_name . ".status", ADWORDS_REPORT_AD_PAUSED)
					->or_where($ad_table_name . ".status", YAHOO_AD_ACTIVE)
					->or_where($ad_table_name . ".status", YAHOO_AD_PAUSED)
					->or_where($ad_table_name . ".status", YDN_AD_ACTIVE)
					->or_where($ad_table_name . ".status", YDN_AD_PAUSED)
					->and_where_close();
			}
		}
		if (!is_null($offset)) $SQL->offset($offset);
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 指定アカウントのキャンペーン配下のコンポーネント情報取得
	/*========================================================================*/
	public static function get_undeleted_components($component, $account_id_list, $search_list = null, $mode = null, $offset = null, $limit = null) {

		## 処理対象コンポーネントがキャンペーンの場合
		if ($mode === "count") {
			$SQL = DB::select(array("count(*)", "count"));
		} else {
			$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
							  array(self::$_table_name . ".account_id", "account_id"),
							  array(self::$_table_name . ".campaign_id", "campaign_id"),
							  array(self::$_table_name . ".campaign_name", "campaign_name"),
							  array(self::$_table_name . ".status", "campaign_status"));
		}
		$SQL->from(self::$_table_name);
		$SQL->and_where_open();
		foreach ($account_id_list as $account_id) {
			## media_id, account_id 分割
			list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

			$SQL->or_where_open();
			$SQL->where(self::$_table_name . ".media_id", "=", $tmp_media_id)
				->and_where(self::$_table_name . ".account_id", "=" , $tmp_account_id);
			$SQL->or_where_close();
		}
		$SQL->and_where_close();
		$SQL->and_where_open()
			->where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_PAUSED)
			->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_PAUSED)
			->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_ACTIVE)
			->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_PAUSED)
			->and_where_close();

		## 検索条件付与
		if (!is_null($search_list)) {
			if (!empty($search_list["search_media"])) $SQL->where(self::$_table_name . ".media_id", "=", intval($search_list["search_media"]));
			if ($search_list["search_component"] === "campaign") {
				if (!empty($search_list["search_id"])) $SQL->where(self::$_table_name . ".campaign_id", "like", $search_list["search_id"]."%");
				if (!empty($search_list["search_name"])) $SQL->where(self::$_table_name . ".campaign_name", "like", $search_list["search_name"]."%");
				if (isset($search_list["search_status"])) {
					if ($search_list["search_status"] === "1") {
						$SQL->and_where_open()
							->where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_ACTIVE)
							->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_ACTIVE)
							->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_ACTIVE)
							->and_where_close();
					} elseif ($search_list["search_status"] === "0") {
						$SQL->and_where_open()
							->where(self::$_table_name . ".status", ADWORDS_CAMPAIGN_PAUSED)
							->or_where(self::$_table_name . ".status", YAHOO_CAMPAIGN_PAUSED)
							->or_where(self::$_table_name . ".status", YDN_CAMPAIGN_PAUSED)
							->and_where_close();
					}
				}
			}
		}

		## 処理対象コンポーネントが広告グループの場合
		if ($component === "adgroup" || $component === "keyword" || $component === "ad") {
			$adg_table_name = "t_eagle_adgroup";

			if ($mode !== "count") {
				$SQL->select(array($adg_table_name . ".adgroup_id", "adgroup_id"),
							 array($adg_table_name . ".adgroup_name", "adgroup_name"),
							 array($adg_table_name . ".status", "adgroup_status"));
			}
			$SQL->join($adg_table_name, "INNER")
				->on(self::$_table_name . ".media_id", "=", $adg_table_name . ".media_id")
				->on(self::$_table_name . ".account_id", "=", $adg_table_name . ".account_id")
				->on(self::$_table_name . ".campaign_id", "=", $adg_table_name . ".campaign_id");
			$SQL->and_where_open()
				->where($adg_table_name . ".status", ADWORDS_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", ADWORDS_ADGROUP_PAUSED)
				->or_where($adg_table_name . ".status", YAHOO_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", YAHOO_ADGROUP_PAUSED)
				->or_where($adg_table_name . ".status", YDN_ADGROUP_ACTIVE)
				->or_where($adg_table_name . ".status", YDN_ADGROUP_PAUSED)
				->and_where_close();

			## 検索条件付与
			if (!is_null($search_list)) {
				if ($search_list["search_component"] === "adgroup") {
					if (!empty($search_list["search_id"])) $SQL->where($adg_table_name . ".adgroup_id", "like", $search_list["search_id"]."%");
					if (!empty($search_list["search_name"])) $SQL->where($adg_table_name . ".adgroup_name", "like", $search_list["search_name"]."%");
					if (isset($search_list["search_status"])) {
						if ($search_list["search_status"] === "1") {
							$SQL->and_where_open()
								->where($adg_table_name . ".status", ADWORDS_ADGROUP_ACTIVE)
								->or_where($adg_table_name . ".status", YAHOO_ADGROUP_ACTIVE)
								->or_where($adg_table_name . ".status", YDN_ADGROUP_ACTIVE)
								->and_where_close();
						} elseif ($search_list["search_status"] === "0") {
							$SQL->and_where_open()
								->where($adg_table_name . ".status", ADWORDS_ADGROUP_PAUSED)
								->or_where($adg_table_name . ".status", YAHOO_ADGROUP_PAUSED)
								->or_where($adg_table_name . ".status", YDN_ADGROUP_PAUSED)
								->and_where_close();
						}
					}
				}
			}

			## 処理対象コンポーネントがキーワードの場合
			if ($component === "keyword") {
				$kw_table_name = "t_eagle_keyword";

				if ($mode !== "count") {
					$SQL->select(array($kw_table_name . ".keyword_id", "keyword_id"),
								 array($kw_table_name . ".keyword", "keyword"),
								 array($kw_table_name . ".status", "keyword_status"));
				}
				$SQL->join($kw_table_name, "INNER")
					->on($adg_table_name . ".media_id", "=", $kw_table_name . ".media_id")
					->on($adg_table_name . ".account_id", "=", $kw_table_name . ".account_id")
					->on($adg_table_name . ".campaign_id", "=", $kw_table_name . ".campaign_id")
					->on($adg_table_name . ".adgroup_id", "=", $kw_table_name . ".adgroup_id");
				$SQL->and_where_open()
					->where($kw_table_name . ".status", ADWORDS_CRITERION_ACTIVE)
					->or_where($kw_table_name . ".status", ADWORDS_CRITERION_PAUSED)
					->or_where($kw_table_name . ".status", YAHOO_CRITERION_ACTIVE)
					->or_where($kw_table_name . ".status", YAHOO_CRITERION_PAUSED)
					->and_where_close();

				## 検索条件付与
				if (!is_null($search_list)) {
					if ($search_list["search_component"] === "keyword") {
						if (!empty($search_list["search_id"])) $SQL->where($kw_table_name . ".keyword_id", "like", $search_list["search_id"]."%");
						if (!empty($search_list["search_name"])) $SQL->where($kw_table_name . ".keyword", "like", $search_list["search_name"]."%");
						if (isset($search_list["search_status"])) {
							if ($search_list["search_status"] === "1") {
								$SQL->and_where_open()
									->where($kw_table_name . ".status", ADWORDS_CRITERION_ACTIVE)
									->or_where($kw_table_name . ".status", YAHOO_CRITERION_ACTIVE)
									->and_where_close();
							} elseif ($search_list["search_status"] === "0") {
								$SQL->and_where_open()
									->where($kw_table_name . ".status", ADWORDS_CRITERION_PAUSED)
									->or_where($kw_table_name . ".status", YAHOO_CRITERION_PAUSED)
									->and_where_close();
							}
						}
					}
				}

			## 処理対象コンポーネントが広告の場合
			} elseif ($component === "ad") {
				$ad_table_name = "t_eagle_ad";

				if ($mode !== "count") {
					$SQL->select(array($ad_table_name . ".ad_id", "ad_id"),
								 array($ad_table_name . ".ad_name", "ad_name"),
								 array($ad_table_name . ".title", "title"),
								 array($ad_table_name . ".description_1", "description_1"),
								 array($ad_table_name . ".description_2", "description_2"),
								 array($ad_table_name . ".status", "ad_status"));
				}
				$SQL->join($ad_table_name, "INNER")
					->on($adg_table_name . ".media_id", "=", $ad_table_name . ".media_id")
					->on($adg_table_name . ".account_id", "=", $ad_table_name . ".account_id")
					->on($adg_table_name . ".campaign_id", "=", $ad_table_name . ".campaign_id")
					->on($adg_table_name . ".adgroup_id", "=", $ad_table_name . ".adgroup_id");
				$SQL->and_where_open()
					->where($ad_table_name . ".status", ADWORDS_REPORT_AD_ACTIVE)
					->or_where($ad_table_name . ".status", ADWORDS_REPORT_AD_PAUSED)
					->or_where($ad_table_name . ".status", YAHOO_AD_ACTIVE)
					->or_where($ad_table_name . ".status", YAHOO_AD_PAUSED)
					->or_where($ad_table_name . ".status", YDN_AD_ACTIVE)
					->or_where($ad_table_name . ".status", YDN_AD_PAUSED)
					->and_where_close();

				## 検索条件付与
				if (!is_null($search_list)) {
					if ($search_list["search_component"] === "ad") {
						if (!empty($search_list["search_id"])) $SQL->where($ad_table_name . ".ad_id", "like", $search_list["search_id"]);
						if (!empty($search_list["search_name"])) {
							$SQL->and_where_open()
								->or_where($ad_table_name . ".ad_name", "like", $search_list["search_name"]."%")
								->or_where($ad_table_name . ".title", "like", $search_list["search_name"]."%")
								->or_where($ad_table_name . ".description_1", "like", $search_list["search_name"]."%")
								->or_where($ad_table_name . ".description_2", "like", $search_list["search_name"]."%")
								->and_where_close();
						}
						if (isset($search_list["search_status"])) {
							if ($search_list["search_status"] === "1") {
								$SQL->and_where_open()
									->where($ad_table_name . ".status", ADWORDS_REPORT_AD_ACTIVE)
									->or_where($ad_table_name . ".status", YAHOO_AD_ACTIVE)
									->or_where($ad_table_name . ".status", YDN_AD_ACTIVE)
									->and_where_close();
							} elseif ($search_list["search_status"] === "0") {
								$SQL->and_where_open()
									->where($ad_table_name . ".status", ADWORDS_REPORT_AD_PAUSED)
									->or_where($ad_table_name . ".status", YAHOO_AD_PAUSED)
									->or_where($ad_table_name . ".status", YDN_AD_PAUSED)
									->and_where_close();
							}
						}
					}
				}
			}
		}
		if (!is_null($offset)) $SQL->offset($offset);
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}
}
