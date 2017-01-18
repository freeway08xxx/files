<?php

class Model_Data_Adcustomizer_Google_Info extends \Model
{
	protected static $_table_name = 't_adcustomizer_google_info';

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id, $feed_name, $feed_id=null) {

		$query = DB::select();
		$query->from(self::$_table_name);
		$query->where("media_id",$media_id);
		$query->where("account_id",$account_id);
		if($feed_name){
			$query->where("feed_name",$feed_name);
		}
		if($feed_id){
			$query->where("feed_id",$feed_id);
		}
		$query->order_by('created_at','desc');
		$query->order_by('updated_at','desc');

		return $query->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$query = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$query->values(array_values($value));
		}

		$query->set_duplicate(array("feed_name = VALUES(feed_name)",
								  "feed_status = VALUES(feed_status)",
								  "placeholder_type = VALUES(placeholder_type)",
								  )
		);

		return $query->execute();
	}

	/*========================================================================*/
	/* 更新
	/*========================================================================*/
	public static function upd($media_id, $account_id, $feed_id, $feed_name=null, $feed_status=null, $feed_mapping_id=null) {

		$query = DB::update(self::$_table_name);

		if (isset($feed_name)) {
			$set_param['feed_name'] = $feed_name;
		}
		if (isset($feed_status)) {
			$set_param['feed_status'] = $feed_status;
		}
		if (isset($feed_mapping_id)) {
			$set_param['feed_mapping_id'] = $feed_mapping_id;
		}
		
		$query->set($set_param);
		$query->where("media_id", $media_id);
		$query->where("account_id", $account_id);
		$query->where("feed_id", $feed_id);

		return $query->execute();
	}
}
