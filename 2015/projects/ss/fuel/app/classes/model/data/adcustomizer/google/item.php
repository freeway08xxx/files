<?php

class Model_Data_Adcustomizer_Google_Item extends \Model
{
	protected static $_table_name = 't_adcustomizer_google_item';

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($media_id, $account_id, $feed_id=null, $error_flg=false, $key=null) {

		$query = DB::select();
		$query->from(self::$_table_name);
		$query->where("media_id",$media_id);
		$query->where("account_id",$account_id);
		if (isset($feed_id)) {
			$query->where("feed_id",$feed_id);
		}
		if (isset($error_flg)) {
			$query->where("error_flg",$error_flg);
		}

		return $query->execute("readonly")->as_array($key);
	}
	
	/*========================================================================*/
	/* 挿入・更新
	/*========================================================================*/
	public static function ins($values) {

		$query = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$query->values(array_values($value));
		}

		$query->set_duplicate(array("item_info = VALUES(item_info)",
								  "kw_id = VALUES(kw_id)",
								  "error_flg = VALUES(error_flg)",
								  "error_reason = VALUES(error_reason)",
								  "unique_id = VALUES(unique_id)",
								  )
		);

		return $query->execute("administrator");
	}

}
