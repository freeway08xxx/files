<?php

class Model_Data_Adcustomizer_Google_Attribute extends \Model
{
	protected static $_table_name = 't_adcustomizer_google_attribute';

	/*========================================================================*/
	/* 取得
	/*========================================================================*/
	public static function get($feed_id, $attribute_id=null) {

		$query = DB::select();
		$query->from(self::$_table_name);
		$query->where("feed_id",$feed_id);
		if (isset($attribute_id)) {
			$query->where("attribute_id",$attribute_id);
		}		

		return $query->execute("readonly")->as_array();
	}
	
	/*========================================================================*/
	/* 挿入・更新
	/*========================================================================*/
	public static function ins($values) {

		$query = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$query->values(array_values($value));
		}

		$query->set_duplicate(array("name = VALUES(name)",
								  "type = VALUES(type)",
								  "field_id = VALUES(field_id)",
								  )
		);

		return $query->execute("administrator");
	}

	/*========================================================================*/
	/* 更新
	/*========================================================================*/
	public static function upd($feed_id, $attribute_id, $field_id=null) {

		$query = DB::update(self::$_table_name)
					->value('field_id', $field_id)
					->where('feed_id',$feed_id)
					->where('attribute_id',$attribute_id);
		return $query->execute("administrator");
	}

}
