<?php

//キャンペーン属性設定モデル

class Model_Data_Falcon_CampaignAttributeSetting extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_falcon_campaign_attribute_setting";

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id) {

		$query = DB::select('*', DB::expr("CONCAT(media_id, '::', account_id, '::', campaign_id) AS campaign_key"),
							'CASE WHEN t_falcon_campaign_attribute_setting."updated_user" IS NULL THEN t_falcon_campaign_attribute_setting."created_user" ELSE t_falcon_campaign_attribute_setting."updated_user" END as "user_id"',
							'CASE WHEN t_falcon_campaign_attribute_setting."updated_at" IS NULL THEN t_falcon_campaign_attribute_setting."created_at" ELSE t_falcon_campaign_attribute_setting."updated_at" END as "datetime"')->from(self::$_table_name);
		$query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when t_falcon_campaign_attribute_setting."updated_user" is null then t_falcon_campaign_attribute_setting."created_user" else t_falcon_campaign_attribute_setting."updated_user" end');
		$query->where(self::$_table_name . ".media_id", "=", $media_id)
			->where(self::$_table_name . ".account_id", "=", $account_id);

		return $query->execute()->as_array("campaign_key");
	}

	public static function get_by_account_list($input_account_id_list){
		$query = DB::select('*',array("concat( account_id, ':', campaign_id )", 'campaign_key'))
				->from(self::$_table_name);
		$query->where('account_id', 'IN', $input_account_id_list);

		return $query->execute()->as_array('campaign_key');
	}

	public static function get_for_campaign_id($client_id, $account_id_list, $campaign_id_list) {

		$query = DB::select('*', DB::expr("CONCAT(media_id, '::', account_id, '::', campaign_id) AS campaign_key"),
							'CASE WHEN t_falcon_campaign_attribute_setting."updated_user" IS NULL THEN t_falcon_campaign_attribute_setting."created_user" ELSE t_falcon_campaign_attribute_setting."updated_user" END as "user_id"',
							'CASE WHEN t_falcon_campaign_attribute_setting."updated_at" IS NULL THEN t_falcon_campaign_attribute_setting."created_at" ELSE t_falcon_campaign_attribute_setting."updated_at" END as "datetime"')->from(self::$_table_name);
		$query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when t_falcon_campaign_attribute_setting."updated_user" is null then t_falcon_campaign_attribute_setting."created_user" else t_falcon_campaign_attribute_setting."updated_user" end');
		$query->where(self::$_table_name . ".client_id", "=", $client_id)
			->where(self::$_table_name . '.account_id', 'in', $account_id_list)
			->where(self::$_table_name . '.campaign_id', 'in', $campaign_id_list);

		return $query->execute()->as_array("campaign_key");
	}

	public static function ins($client_id, $values){
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name, array_keys($value));
			}
			$query->values($value);
		}
		$query->set_duplicate(array("client_id = VALUES(client_id)",
								  "media_id = VALUES(media_id)",
								  "account_id = VALUES(account_id)",
								  "campaign_id = VALUES(campaign_id)",
								  "device_id = VALUES(device_id)",
								  "ad_type_id = VALUES(ad_type_id)"));

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

}
