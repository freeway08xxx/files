<?php

class Model_Data_Quota_Yahoo extends \Model
{
	protected static $_table_name = 't_yahoo_api_quota';
	protected static $_properties = array(
		'id',
		'command_group_name',
		'description',
		'daily_quota_amount',
		'daily_remain_quota',
		'created_at',
		'created_user',
		'updated_at',
		'updated_user',
	);

	public static function set_quota($service_name, $quota_limit) {
		$query = DB::update(self::$_table_name);
		$query->set(array('daily_remain_quota' => $quota_limit,
			'updated_at' => \DB::expr('NOW()')));
		$query->where('command_group_name', $service_name);
		return $query->execute('administrator');
	}

	public static function get_all()
	{
		$query = DB::select()->from(self::$_table_name);
		return $query->execute("readonly");
	}
}
