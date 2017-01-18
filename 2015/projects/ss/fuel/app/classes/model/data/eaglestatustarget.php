<?php

################################################################################
#
# Title : ステータス変更処理対象コンポーネント用モデル
#
#  2014/06/01  First Version
#
################################################################################

class Model_Data_EagleStatusTarget extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_eagle_status_target";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"),
						  array(self::$_table_name . ".adgroup_id", "adgroup_id"),
						  array(self::$_table_name . ".target_kbn", "target_kbn"),
						  array(self::$_table_name . ".target_id", "target_id"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);

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

		return $SQL->execute("administrator");
	}

	/*========================================================================*/
	/* キャンペーン配下のコンポーネント情報取得
	/*========================================================================*/
	public static function get_target_list($id, $component) {

		## 処理対象コンポーネントがキャンペーンの場合
		$SQL = DB::select(array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"),
						  array(self::$_table_name . ".campaign_id", "campaign_id"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);
		$SQL->order_by(self::$_table_name . ".id")
			->order_by(self::$_table_name . ".media_id")
			->order_by(self::$_table_name . ".account_id")
			->order_by(self::$_table_name . ".campaign_id");

		## 処理対象コンポーネントが広告グループの場合
		if ($component === "adgroup" || $component === "keyword" || $component === "ad") {
			$SQL->select(array(self::$_table_name . ".adgroup_id", "adgroup_id"));
			$SQL->order_by(self::$_table_name . ".adgroup_id");

			## 処理対象コンポーネントがキーワードの場合
			if ($component === "keyword") {
				$SQL->select(array(self::$_table_name . ".target_id", "target_id"));
				$SQL->where(self::$_table_name . ".target_kbn", "=", "KW");
				$SQL->order_by(self::$_table_name . ".target_id");

			## 処理対象コンポーネントが広告の場合
			} elseif ($component === "ad") {
				$SQL->select(array(self::$_table_name . ".target_id", "target_id"));
				$SQL->where(self::$_table_name . ".target_kbn", "=", "AD");
				$SQL->order_by(self::$_table_name . ".target_id");
			}
		}
		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* アカウント一覧取得
	/*========================================================================*/
	public static function get_target_account_list($id) {

		$SQL = DB::select(array(self::$_table_name . ".id", "id"),
						  array(self::$_table_name . ".media_id", "media_id"),
						  array(self::$_table_name . ".account_id", "account_id"));
		$SQL->from(self::$_table_name);
		$SQL->where(self::$_table_name . ".id", "=", $id);
		$SQL->group_by(self::$_table_name . ".id")
			->group_by(self::$_table_name . ".media_id")
			->group_by(self::$_table_name . ".account_id");

		return $SQL->execute("readonly")->as_array();
	}
}
